<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCoupon extends Model
{
    use HasFactory;

    protected $table = 'shop_coupons';

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isPercentage(): bool
    {
        return $this->discount_type === 'percentage';
    }

    public function isFixed(): bool
    {
        return $this->discount_type === 'fixed';
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Valida si el cupón está vigente y cumple con las condiciones para aplicarse a un subtotal.
     */
    public function isValidForAmount(float $subtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calcula el monto exacto de descuento en USD para un subtotal dado.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValidForAmount($subtotal)) {
            return 0.00;
        }

        $discount = 0.00;

        if ($this->discount_type === 'percentage') {
            $discount = round($subtotal * ((float) $this->discount_value / 100), 2);
        } else {
            // 'fixed' en USD
            $discount = min($subtotal, (float) $this->discount_value);
        }

        if ($this->max_discount_amount !== null && $discount > (float) $this->max_discount_amount) {
            $discount = (float) $this->max_discount_amount;
        }

        return round($discount, 2);
    }
}
