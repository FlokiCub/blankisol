<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationScenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'scenario_date',
        'assumptions',
        'results',
        'notes',
    ];

    protected $casts = [
        'scenario_date' => 'datetime',
        'assumptions' => 'array',
        'results' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
