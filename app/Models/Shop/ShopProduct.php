<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_products';

    protected $fillable = [
        'category_id',
        'sku',
        'slug',
        'name',
        'short_description',
        'description',
        'price',
        'sale_price',
        'cost_price',
        'inventory_type',
        'stock_quantity',
        'min_stock_alert',
        'estimated_delivery_days',
        'weight_kg',
        'dimensions',
        'is_active',
        'is_featured',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShopProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ShopProductImage::class, 'product_id')->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ShopProductVariant::class, 'product_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(ShopInventoryMovement::class, 'product_id')->latest();
    }

    /**
     * Retorna el precio efectivo de venta en USD (oferta si existe, o precio base).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price) {
            return (float) $this->sale_price;
        }

        return (float) $this->price;
    }

    /**
     * Valida si el producto tiene stock controlado y se encuentra agotado.
     */
    public function isOutOfStock(): bool
    {
        return $this->inventory_type === 'controlled_stock' && $this->stock_quantity <= 0;
    }

    /**
     * Valida si el producto se comercializa bajo pedido.
     */
    public function isOnDemand(): bool
    {
        return $this->inventory_type === 'on_demand';
    }
}
