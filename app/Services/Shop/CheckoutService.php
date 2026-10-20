<?php

declare(strict_types=1);

namespace App\Services\Shop;

use App\Models\Shop\ShopCart;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopOrderItem;
use App\Models\Shop\ShopShippingRate;
use App\Services\Shop\Payment\PaymentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        protected PricingService $pricingService,
        protected ShippingService $shippingService,
        protected InventoryService $inventoryService,
        protected PaymentManager $paymentManager
    ) {}

    /**
     * Procesa la compra de manera atómica, aplicando snapshots inmutables en USD y deducción de stock.
     */
    public function processCheckout(ShopCart $cart, array $customerInput, ?string $idempotencyKey = null): array
    {
        $idempotencyKey = $idempotencyKey ?: (string) Str::uuid();

        // 1. Prevención de doble procesamiento (Idempotencia)
        $existingOrder = ShopOrder::where('idempotency_key', $idempotencyKey)->first();
        if ($existingOrder) {
            return [
                'order' => $existingOrder,
                'is_duplicate' => true,
                'checkout_url' => route('shop.checkout.success', $existingOrder->order_number),
            ];
        }

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('No se puede procesar el checkout con un carrito vacío.');
        }

        // 2. Recálculo estricto en el servidor de ítems y precios en USD
        $cart->load(['items.product', 'items.variant']);
        $calculatedItems = [];

        foreach ($cart->items as $cartItem) {
            $product = $cartItem->product;
            if (!$product || !$product->is_active) {
                throw new RuntimeException("El producto [{$cartItem->product_id}] ya no está disponible.");
            }

            $calculatedItems[] = $this->pricingService->calculateItem(
                $product,
                $cartItem->quantity,
                $cartItem->variant
            );
        }

        // Resolver tarifa de envío en USD
        $shippingRateId = !empty($customerInput['shipping_rate_id']) ? (int) $customerInput['shipping_rate_id'] : null;
        $shippingRate = $shippingRateId
            ? ShopShippingRate::where('id', $shippingRateId)->where('is_active', true)->first()
            : null;

        $coupon = $cart->coupon_code
            ? \App\Models\Shop\ShopCoupon::where('code', $cart->coupon_code)->first()
            : null;

        $totals = $this->pricingService->calculateOrderTotals(
            $calculatedItems,
            $coupon,
            $shippingRate,
            0.0 // Tasa impositiva neta
        );

        // 3. Ejecutar transacción atómica en PostgreSQL
        return DB::transaction(function () use (
            $cart,
            $customerInput,
            $calculatedItems,
            $totals,
            $shippingRate,
            $idempotencyKey
        ) {
            $customerType = $customerInput['customer_type'] ?? 'individual';

            // Resolver o crear perfil del cliente
            $customer = ShopCustomer::updateOrCreate(
                ['email' => trim(strtolower($customerInput['email']))],
                [
                    'customer_type' => $customerType,
                    'first_name' => $customerInput['first_name'] ?? 'Cliente',
                    'last_name' => $customerInput['last_name'] ?? '',
                    'phone' => $customerInput['phone'] ?? null,
                    'business_name' => $customerType === 'business' ? ($customerInput['business_name'] ?? null) : null,
                    'tax_id' => $customerType === 'business' ? ($customerInput['tax_id'] ?? null) : ($customerInput['id_number'] ?? null),
                ]
            );

            // Generar número de orden
            $orderNumber = ShopOrder::generateOrderNumber();

            // Snapshot inmutable de cliente
            $customerSnapshot = [
                'customer_type' => $customerType,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'business_name' => $customer->business_name,
                'tax_id' => $customer->tax_id,
            ];

            // Snapshot inmutable de dirección
            $shippingSnapshot = [
                'address' => $customerInput['address'] ?? '',
                'province' => $customerInput['province'] ?? '',
                'municipality' => $customerInput['municipality'] ?? '',
                'postal_code' => $customerInput['postal_code'] ?? null,
                'notes' => $customerInput['delivery_notes'] ?? null,
                'recipient_name' => $customerInput['recipient_name'] ?? $customer->full_name,
                'recipient_phone' => $customerInput['recipient_phone'] ?? $customer->phone,
            ];

            // Crear Orden
            $order = ShopOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_type' => $customerType,
                'customer_data' => $customerSnapshot,
                'shipping_address' => $shippingSnapshot,
                'billing_address' => $shippingSnapshot,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'currency' => 'USD',
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount_amount'],
                'coupon_code' => $totals['coupon_code'],
                'shipping_rate_id' => $shippingRate?->id,
                'shipping_fee' => $totals['shipping_fee'],
                'tax_amount' => $totals['tax_amount'],
                'total' => $totals['total'],
                'notes' => $customerInput['order_notes'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'placed_at' => now(),
            ]);

            // Crear ítems de la orden (snapshots inmutables)
            foreach ($calculatedItems as $item) {
                ShopOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'sku' => $item['sku'],
                    'product_name' => $item['name'],
                    'inventory_type' => $item['inventory_type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost' => $item['unit_cost'],
                    'total_price' => $item['total_price'],
                    'product_data' => $item,
                ]);
            }

            // Deducción de stock con bloqueo pesimista
            $this->inventoryService->deductStockForOrder($order, auth()->id());

            // Vaciar carrito
            $cart->items()->delete();
            $cart->update(['coupon_code' => null]);

            // Iniciar pasarela de pago en USD
            $paymentResult = $this->paymentManager->createPaymentTransaction($order);

            return [
                'order' => $order,
                'is_duplicate' => false,
                'payment' => $paymentResult['payment'],
                'checkout_url' => $paymentResult['checkout_url'],
            ];
        }, 5);
    }
}
