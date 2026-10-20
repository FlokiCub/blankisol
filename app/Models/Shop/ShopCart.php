<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCart extends Model
{
    use HasFactory;

    protected $table = 'shop_carts';

    protected $fillable = [
        'session_id',
        'user_id',
        'customer_id',
        'coupon_code',
        'coupon_discount_amount',
        'status',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'coupon_discount_amount' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopCartItem::class, 'cart_id');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn (ShopCartItem $item) => $item->subtotal);
    }

    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}

