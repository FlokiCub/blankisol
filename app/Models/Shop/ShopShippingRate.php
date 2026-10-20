<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopShippingRate extends Model
{
    use HasFactory;

    protected $table = 'shop_shipping_rates';

    protected $fillable = [
        'zone_id',
        'name',
        'price',
        'estimated_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShopShippingZone::class, 'zone_id');
    }
}
