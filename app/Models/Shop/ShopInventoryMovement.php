<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopInventoryMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // Solo utiliza created_at

    protected $table = 'shop_inventory_movements';

    protected $fillable = [
        'product_id',
        'variant_id',
        'order_id',
        'movement_type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ShopProductVariant::class, 'variant_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
