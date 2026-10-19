<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedCostMonthly extends Model
{
    use HasFactory;

    protected $table = 'fixed_costs_monthly';

    protected $fillable = [
        'year_month',
        'general_fixed_usd',
        'occidente_fixed_usd',
        'centro_fixed_usd',
        'oriente_fixed_usd',
        'notes',
    ];

    protected $casts = [
        'general_fixed_usd' => 'float',
        'occidente_fixed_usd' => 'float',
        'centro_fixed_usd' => 'float',
        'oriente_fixed_usd' => 'float',
    ];

    /**
     * Get monthly fixed costs or calculate dynamically from concepts and salary taxes.
     */
    public static function getForMonth(string $yearMonth): self
    {
        $existing = static::where('year_month', $yearMonth)->first();
        if ($existing) {
            return $existing;
        }

        $calc = FixedCostItem::calculateMonthlyFixedCosts($yearMonth);

        return static::create([
            'year_month' => $yearMonth,
            'general_fixed_usd' => $calc['general_fixed_usd'],
            'occidente_fixed_usd' => $calc['occidente_fixed_usd'],
            'centro_fixed_usd' => $calc['centro_fixed_usd'],
            'oriente_fixed_usd' => $calc['oriente_fixed_usd'],
            'notes' => 'Calculado dinámicamente desde conceptos y tributos salariales',
        ]);
    }

    /**
     * Recalculate and synchronize monthly totals from individual FixedCostItem entries.
     */
    public static function recalculateFromItems(string $yearMonth): self
    {
        $calc = FixedCostItem::calculateMonthlyFixedCosts($yearMonth);

        return static::updateOrCreate(
            ['year_month' => $yearMonth],
            [
                'general_fixed_usd' => $calc['general_fixed_usd'],
                'occidente_fixed_usd' => $calc['occidente_fixed_usd'],
                'centro_fixed_usd' => $calc['centro_fixed_usd'],
                'oriente_fixed_usd' => $calc['oriente_fixed_usd'],
                'notes' => 'Sincronizado con conceptos de gastos fijos y tributos salariales',
            ]
        );
    }
}
