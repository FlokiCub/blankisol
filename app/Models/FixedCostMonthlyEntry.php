<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedCostMonthlyEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixed_cost_item_id',
        'year_month',
        'actual_amount',
        'observations',
        'documents',
    ];

    protected $casts = [
        'actual_amount' => 'float',
        'documents' => 'array',
    ];

    public function fixedCostItem(): BelongsTo
    {
        return $this->belongsTo(FixedCostItem::class);
    }
}
