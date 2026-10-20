<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCustomer extends Model
{
    use HasFactory;

    protected $table = 'shop_customers';

    protected $fillable = [
        'user_id',
        'customer_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'business_name',
        'company_name',
        'tax_id',
        'default_province',
        'default_municipality',
        'is_verified',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ShopCustomerAddress::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ShopOrder::class, 'customer_id')->latest();
    }

    public function isBusiness(): bool
    {
        return $this->customer_type === 'business';
    }

    public function getFullNameAttribute(): string
    {
        $company = $this->company_name ?: $this->business_name;
        $personName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        if (empty($personName) && $this->user) {
            $personName = $this->user->name;
        }

        if ($this->isBusiness() && !empty($company)) {
            return !empty($personName) ? "{$company} ({$personName})" : $company;
        }

        return !empty($personName) ? $personName : ($this->user?->name ?? 'Cliente');
    }
}
