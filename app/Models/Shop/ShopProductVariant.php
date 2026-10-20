<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductVariant extends Model
{
    use HasFactory;

    protected $table = 'shop_product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'options',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }
}
