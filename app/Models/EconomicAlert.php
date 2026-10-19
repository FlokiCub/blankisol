<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EconomicAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_profitability_analysis_id',
        'alert_type',
        'severity',
        'description',
        'is_investigated',
        'justification',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'is_investigated' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(ContainerProfitabilityAnalysis::class, 'container_profitability_analysis_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
