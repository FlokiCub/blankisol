<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommercialAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_number',
        'name',
        'group',
        'territory',
        'region',
        'monthly_fixed_fee',
        'commission_mode',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'agent_number' => 'integer',
        'monthly_fixed_fee' => 'float',
        'is_active' => 'boolean',
    ];

    public function liquidations(): HasMany
    {
        return $this->hasMany(CommercialLiquidation::class);
    }
}
