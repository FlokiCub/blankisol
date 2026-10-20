<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_type',
        'customer_data',
        'shipping_address',
        'billing_address',
        'status',
        'payment_status',
        'currency',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_rate_id',
        'shipping_fee',
        'tax_amount',
        'total',
        'notes',
        'idempotency_key',
        'placed_at',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'customer_data' => 'array',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id');
    }

    public function shippingRate(): BelongsTo
    {
        return $this->belongsTo(ShopShippingRate::class, 'shipping_rate_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ShopPayment::class, 'order_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(ShopInventoryMovement::class, 'order_id');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isBusiness(): bool
    {
        return $this->customer_type === 'business';
    }

    public function isIndividual(): bool
    {
        return $this->customer_type === 'individual';
    }

    /**
     * Generador secuencial de número de pedido comercial: ORD-YYYY-XXXXX
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(3))); // 6 caracteres alfanuméricos únicos
        return "ORD-{$year}-{$random}";
    }
}
