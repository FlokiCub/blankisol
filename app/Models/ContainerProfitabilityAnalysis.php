<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContainerProfitabilityAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'arrival_date',
        'transitaria',
        'status',
        'economic_status',
        'closed_by',
        'closed_at',
        'reopened_reason',
        'applied_rates_snapshot',
        'kg_occidente',
        'kg_centro',
        'kg_oriente',
        'kg_total',
        'revenue_occidente',
        'revenue_centro',
        'revenue_oriente',
        'revenue_other',
        'revenue_total',
        'cost_port_total',
        'cost_transitaria_total',
        'cost_distribution_occidente',
        'cost_distribution_centro',
        'cost_distribution_oriente',
        'cost_distribution_total',
        'cost_commission_total',
        'cost_provisions_and_bank',
        'cost_variable_total',
        'contribution_margin',
        'contribution_margin_pct',
        'fixed_costs_assigned_total',
        'taxes_sales_and_services',
        'tax_territorial_contribution',
        'profit_before_tax',
        'estimated_income_tax',
        'net_profit',
        'net_margin_pct',
        'cost_per_kg',
        'revenue_per_kg',
        'profit_before_tax_per_kg',
        'breakeven_kg',
        'breakeven_tariff_usd_kg',
        'decision_verdict',
        'decision_summary',
        'regional_breakdown',
        'applied_parameters',
        'created_by',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'closed_at' => 'datetime',
        'applied_rates_snapshot' => 'array',
        'kg_occidente' => 'float',
        'kg_centro' => 'float',
        'kg_oriente' => 'float',
        'kg_total' => 'float',
        'revenue_occidente' => 'float',
        'revenue_centro' => 'float',
        'revenue_oriente' => 'float',
        'revenue_other' => 'float',
        'revenue_total' => 'float',
        'cost_port_total' => 'float',
        'cost_transitaria_total' => 'float',
        'cost_distribution_occidente' => 'float',
        'cost_distribution_centro' => 'float',
        'cost_distribution_oriente' => 'float',
        'cost_distribution_total' => 'float',
        'cost_commission_total' => 'float',
        'cost_provisions_and_bank' => 'float',
        'cost_variable_total' => 'float',
        'contribution_margin' => 'float',
        'contribution_margin_pct' => 'float',
        'fixed_costs_assigned_total' => 'float',
        'taxes_sales_and_services' => 'float',
        'tax_territorial_contribution' => 'float',
        'profit_before_tax' => 'float',
        'estimated_income_tax' => 'float',
        'net_profit' => 'float',
        'net_margin_pct' => 'float',
        'cost_per_kg' => 'float',
        'revenue_per_kg' => 'float',
        'profit_before_tax_per_kg' => 'float',
        'breakeven_kg' => 'float',
        'breakeven_tariff_usd_kg' => 'float',
        'regional_breakdown' => 'array',
        'applied_parameters' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(EconomicAlert::class, 'container_profitability_analysis_id');
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'solve_cargo_tracking_id', 'container_id');
    }

    /**
     * Get badge color according to decision verdict.
     */
    public function getVerdictBadgeClassAttribute(): string
    {
        return match ($this->decision_verdict) {
            'FACTIBLE' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            'MARGINAL' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            default => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
        };
    }
}
