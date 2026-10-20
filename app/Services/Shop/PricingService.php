<?php

declare(strict_types=1);

namespace App\Services\Shop;

use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopProductVariant;
use App\Models\Shop\ShopShippingRate;
use InvalidArgumentException;

class PricingService
{
    /**
     * Calcula la línea de precio de un ítem en USD, aplicando ofertas activas o variantes.
     */
    public function calculateItem(
        ShopProduct $product,
        int $quantity,
        ?ShopProductVariant $variant = null
    ): array {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad del producto debe ser mayor a cero.');
        }

        // Si hay variante con precio específico, toma la variante; si no, el effective_price del producto
        $unitPrice = $variant && $variant->price !== null
            ? (float) $variant->price
            : (float) $product->effective_price;

        $unitCost = (float) ($product->cost_price ?? 0.00);
        $totalPrice = round($unitPrice * $quantity, 2);
        $totalCost = round($unitCost * $quantity, 2);

        return [
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'sku' => $variant?->sku ?? $product->sku,
            'name' => $product->name . ($variant ? " ({$variant->name})" : ''),
            'inventory_type' => $product->inventory_type,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
            'total_price' => $totalPrice,
            'total_cost' => $totalCost,
            'currency' => 'USD',
        ];
    }

    /**
     * Valida y calcula el descuento de un cupón sobre un subtotal en USD.
     */
    public function applyCoupon(?ShopCoupon $coupon, float $subtotal): array
    {
        if (!$coupon) {
            return [
                'applied' => false,
                'coupon' => null,
                'discount_amount' => 0.00,
                'reason' => 'Sin cupón aplicado',
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'applied' => false,
                'coupon' => $coupon,
                'discount_amount' => 0.00,
                'reason' => 'El cupón no está activo o ha expirado.',
            ];
        }

        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            return [
                'applied' => false,
                'coupon' => $coupon,
                'discount_amount' => 0.00,
                'reason' => "Requiere una compra mínima de $" . number_format((float) $coupon->min_order_amount, 2) . " USD.",
            ];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'applied' => true,
            'coupon' => $coupon,
            'coupon_code' => $coupon->code,
            'discount_amount' => round($discount, 2),
            'reason' => 'Cupón aplicado exitosamente.',
        ];
    }

    /**
     * Calcula la liquidación integral de la orden en USD.
     * Cero manipulación del cliente: todos los importes se revalidan en el servidor.
     */
    public function calculateOrderTotals(
        array $calculatedItems,
        ?ShopCoupon $coupon = null,
        ?ShopShippingRate $shippingRate = null,
        float $taxRatePercentage = 0.0
    ): array {
        $subtotal = 0.00;
        $totalCost = 0.00;

        foreach ($calculatedItems as $item) {
            $subtotal += (float) $item['total_price'];
            $totalCost += (float) ($item['total_cost'] ?? 0.00);
        }

        $subtotal = round($subtotal, 2);
        $totalCost = round($totalCost, 2);

        // Cupón
        $couponResult = $this->applyCoupon($coupon, $subtotal);
        $discountAmount = $couponResult['applied'] ? (float) $couponResult['discount_amount'] : 0.00;

        // Tarifa de entrega
        $shippingFee = $shippingRate && $shippingRate->is_active
            ? round((float) $shippingRate->price, 2)
            : 0.00;

        // Base imponible (subtotal tras descuentos, mínimo 0)
        $taxableBase = max(0.00, $subtotal - $discountAmount);
        $taxAmount = round($taxableBase * ($taxRatePercentage / 100.0), 2);

        // Total final a liquidar en USD
        $total = round($taxableBase + $shippingFee + $taxAmount, 2);

        return [
            'currency' => 'USD',
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'coupon_applied' => $couponResult['applied'],
            'coupon_code' => $couponResult['coupon_code'] ?? null,
            'coupon_message' => $couponResult['reason'],
            'shipping_rate_id' => $shippingRate?->id,
            'shipping_fee' => $shippingFee,
            'tax_rate_percent' => $taxRatePercentage,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'estimated_gross_profit' => round($subtotal - $totalCost, 2),
            'items_count' => count($calculatedItems),
        ];
    }
}
