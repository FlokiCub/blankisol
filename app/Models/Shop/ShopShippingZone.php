<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopShippingZone extends Model
{
    use HasFactory;

    protected $table = 'shop_shipping_zones';

    protected $fillable = [
        'name',
        'provinces',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'provinces' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShopShippingRate::class, 'zone_id')->where('is_active', true);
    }
}
