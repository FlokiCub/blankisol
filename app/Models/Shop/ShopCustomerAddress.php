<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'shop_customer_addresses';

    protected $fillable = [
        'customer_id',
        'address_type',
        'recipient_name',
        'phone',
        'province',
        'municipality',
        'address_line1',
        'address_line2',
        'postal_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id');
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->municipality,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}
