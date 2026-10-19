<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_month',
        'agents_total_amount',
        'operations_total_amount',
        'difference',
        'status',
        'observations',
        'reconciled_by',
        'reconciled_at',
    ];

    protected $casts = [
        'agents_total_amount' => 'float',
        'operations_total_amount' => 'float',
        'difference' => 'float',
        'reconciled_at' => 'datetime',
    ];

    public function reconciler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
