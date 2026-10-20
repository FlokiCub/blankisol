<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopPayment extends Model
{
    use HasFactory;

    protected $table = 'shop_payments';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'idempotency_key',
        'payload',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payload' => 'array',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
