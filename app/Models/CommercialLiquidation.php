<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialLiquidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'commercial_agent_id',
        'year_month',
        'monthly_fixed_fee',
        'variable_base',
        'variable_rate',
        'calculated_commission',
        'total_payable',
        'status',
        'notes',
    ];

    protected $casts = [
        'monthly_fixed_fee' => 'float',
        'variable_base' => 'float',
        'variable_rate' => 'float',
        'calculated_commission' => 'float',
        'total_payable' => 'float',
    ];

    public function commercialAgent(): BelongsTo
    {
        return $this->belongsTo(CommercialAgent::class);
    }
}
