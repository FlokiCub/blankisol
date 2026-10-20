<?php

declare(strict_types=1);

namespace App\Services\Shop;

use App\Models\Shop\ShopCart;
use App\Models\Shop\ShopCartItem;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopProductVariant;
use App\Models\Shop\ShopShippingRate;
use InvalidArgumentException;
use RuntimeException;

class CartService
{
    public function __construct(
        protected PricingService $pricingService,
        protected ShippingService $shippingService
    ) {}

    /**
     * Obtiene o inicializa el carrito de la sesión o cliente.
     */
    public function getOrCreateCart(string $sessionId, ?int $userId = null): ShopCart
    {
        $customerId = null;
        if ($userId) {
            $customer = \App\Models\Shop\ShopCustomer::where('user_id', $userId)->first()
                ?? \App\Models\Shop\ShopCustomer::find($userId);
            $customerId = $customer?->id;
        }

        $cart = ShopCart::where('session_id', $sessionId)->first();

        if (!$cart) {
            $cart = ShopCart::create([
                'session_id' => $sessionId,
                'customer_id' => $customerId,
                'last_activity_at' => now(),
            ]);
        } else {
            $updates = ['last_activity_at' => now()];
            if ($customerId && !$cart->customer_id) {
                $updates['customer_id'] = $customerId;
            }
            $cart->update($updates);
        }

        return $cart;
    }

    /**
     * Añade un producto al carrito verificando stock en USD.
     */
    public function addItem(
        ShopCart $cart,
        ShopProduct $product,
        int $quantity = 1,
        ?ShopProductVariant $variant = null
    ): ShopCartItem {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        if (!$product->is_active) {
            throw new RuntimeException("El producto [{$product->name}] no está disponible para compra.");
        }

        // Buscar si ya existe el ítem en el carrito
        $cartItem = ShopCartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        $existingQty = $cartItem ? $cartItem->quantity : 0;
        $newTotalQty = $existingQty + $quantity;

        // Si es stock físico controlado, verificar disponibilidad
        if ($product->inventory_type === 'controlled_stock') {
            $available = $variant ? ($variant->stock_quantity ?? $product->stock_quantity) : $product->stock_quantity;
            if ($newTotalQty > $available) {
                throw new RuntimeException(
                    "No hay suficiente stock físico para [{$product->name}]. Disponible: {$available}, en carrito: {$existingQty}."
                );
            }
        }

        $effectivePrice = $variant && $variant->price !== null
            ? (float) $variant->price
            : (float) $product->effective_price;

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newTotalQty,
                'unit_price' => $effectivePrice,
            ]);
        } else {
            $cartItem = ShopCartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
                'unit_price' => $effectivePrice,
            ]);
        }

        $cart->touch();
        return $cartItem;
    }

    /**
     * Actualiza la cantidad de un ítem existente.
     */
    public function updateQuantity(ShopCartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($item);
            return;
        }

        $product = $item->product;
        if ($product && $product->inventory_type === 'controlled_stock') {
            $variant = $item->variant;
            $available = $variant ? ($variant->stock_quantity ?? $product->stock_quantity) : $product->stock_quantity;
            if ($quantity > $available) {
                throw new RuntimeException(
                    "Cantidad solicitada ({$quantity}) excede las existencias físicas disponibles ({$available})."
                );
            }
        }

        $item->update(['quantity' => $quantity]);
        $item->cart->touch();
    }

    /**
     * Elimina un ítem del carrito.
     */
    public function removeItem(ShopCartItem $item): void
    {
        $cart = $item->cart;
        $item->delete();
        $cart->touch();
    }

    /**
     * Aplica cupón de descuento al carrito.
     */
    public function applyCoupon(ShopCart $cart, string $code): array
    {
        $code = strtoupper(trim($code));
        $coupon = ShopCoupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => "El cupón '{$code}' no existe.",
            ];
        }

        $validation = $this->pricingService->applyCoupon($coupon, $cart->subtotal);

        if (!$validation['applied']) {
            return [
                'success' => false,
                'message' => $validation['reason'],
            ];
        }

        $cart->update(['coupon_code' => $coupon->code]);

        return [
            'success' => true,
            'message' => "Cupón '{$coupon->code}' aplicado con éxito.",
            'discount_amount' => $validation['discount_amount'],
        ];
    }

    /**
     * Remueve cupón del carrito.
     */
    public function removeCoupon(ShopCart $cart): void
    {
        $cart->update(['coupon_code' => null]);
    }

    /**
     * Genera la liquidación completa del carrito en USD.
     */
    public function getCartSummary(ShopCart $cart, ?int $shippingRateId = null, ?string $province = null): array
    {
        $cart->load(['items.product', 'items.variant']);

        $calculatedItems = [];
        foreach ($cart->items as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }

            $calculatedItems[] = $this->pricingService->calculateItem(
                $cartItem->product,
                $cartItem->quantity,
                $cartItem->variant
            );
        }

        $coupon = $cart->coupon_code
            ? ShopCoupon::where('code', $cart->coupon_code)->first()
            : null;

        $shippingRate = $shippingRateId
            ? ShopShippingRate::where('id', $shippingRateId)->first()
            : null;

        $totals = $this->pricingService->calculateOrderTotals(
            $calculatedItems,
            $coupon,
            $shippingRate,
            0.0
        );

        $availableShippingRates = $this->shippingService->getAvailableRatesForProvince($province);

        return [
            'cart_id' => $cart->id,
            'items' => $calculatedItems,
            'totals' => $totals,
            'subtotal' => (float) ($totals['subtotal'] ?? 0),
            'discount_amount' => (float) ($totals['discount_amount'] ?? 0),
            'shipping_fee' => (float) ($totals['shipping_fee'] ?? 0),
            'customs_tax' => (float) ($totals['customs_tax'] ?? 0),
            'total' => (float) ($totals['total'] ?? 0),
            'available_shipping_rates' => $availableShippingRates,
            'is_empty' => count($calculatedItems) === 0,
        ];
    }
}
