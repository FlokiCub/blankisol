<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\CommercialAgent;
use App\Models\CommercialLiquidation;
use App\Models\CommercialReconciliation;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\FuelAndTariffRate;
use App\Models\User;
use Illuminate\Support\Collection;

class CommercialManagementService
{
    /**
     * Calcula la comisión de un gestor comercial según su modalidad (% INGRESO vs $/KG) (Fase 6.2.6).
     */
    public function calculateCommission(
        CommercialAgent $agent,
        float $variableBase,
        ?FuelAndTariffRate $rateCard = null
    ): float {
        $rateCard = $rateCard ?? FuelAndTariffRate::getEffectiveRates();

        if (strtoupper($agent->commission_mode ?? '') === '$/KG') {
            $rate = (float) $rateCard->commercial_commission_usd_kg;
        } else {
            // Predeterminado: % DEL INGRESO
            $rate = (float) $rateCard->commercial_commission_pct;
        }

        return round($variableBase * $rate, 2);
    }

    /**
     * Liquida mensualmente a los gestores comerciales activos (21 gestores).
     *
     * @param array<int|string, float> $agentBases Arreglo indexado por agent_number o agent_id con la base variable
     * @return Collection<int, CommercialLiquidation>
     */
    public function liquidateMonth(string $yearMonth, array $agentBases = []): Collection
    {
        $agents = CommercialAgent::where('is_active', true)->orderBy('agent_number')->get();
        $rateCard = FuelAndTariffRate::getEffectiveRates($yearMonth . '-01');

        $liquidations = collect();

        foreach ($agents as $agent) {
            $base = (float) ($agentBases[$agent->agent_number] ?? $agentBases[$agent->id] ?? 0.0);
            $fixedFee = (float) $agent->monthly_fixed_fee;
            $rate = strtoupper($agent->commission_mode ?? '') === '$/KG'
                ? (float) $rateCard->commercial_commission_usd_kg
                : (float) $rateCard->commercial_commission_pct;

            $commission = round($base * $rate, 2);
            $totalPayable = round($fixedFee + $commission, 2);

            $liquidation = CommercialLiquidation::updateOrCreate(
                [
                    'commercial_agent_id' => $agent->id,
                    'year_month' => $yearMonth,
                ],
                [
                    'monthly_fixed_fee' => $fixedFee,
                    'variable_base' => $base,
                    'variable_rate' => $rate,
                    'calculated_commission' => $commission,
                    'total_payable' => $totalPayable,
                    'status' => 'liquidado',
                    'notes' => "Liquidación mensual {$yearMonth} modalidad {$agent->commission_mode}",
                ]
            );

            $liquidations->push($liquidation);
        }

        return $liquidations;
    }

    /**
     * Realiza la conciliación comercial mensual entre lo declarado por gestores
     * y las comisiones registradas en las operaciones de contenedores (Fase 6.2.7).
     */
    public function reconcileMonth(string $yearMonth, ?User $user = null): CommercialReconciliation
    {
        // 1. Total liquidado o reportado en comisiones por gestores
        $agentsTotal = (float) CommercialLiquidation::where('year_month', $yearMonth)->sum('calculated_commission');

        // 2. Total registrado en comisiones en análisis de contenedores del mes
        $operationsTotal = (float) ContainerProfitabilityAnalysis::where('arrival_date', 'like', "{$yearMonth}%")
            ->sum('cost_commission_total');

        $diff = round($agentsTotal - $operationsTotal, 2);
        $status = abs($diff) <= 0.05 ? 'cuadrado' : 'con_diferencia';

        return CommercialReconciliation::updateOrCreate(
            ['year_month' => $yearMonth],
            [
                'agents_total_amount' => $agentsTotal,
                'operations_total_amount' => $operationsTotal,
                'difference' => $diff,
                'status' => $status,
                'observations' => $status === 'cuadrado'
                    ? 'Conciliación comercial conforme: comisiones de gestores coinciden con operaciones del mes.'
                    : "Discrepancia detectada de \${$diff} USD entre comisiones de gestores y operaciones.",
                'reconciled_by' => $user?->id,
                'reconciled_at' => now(),
            ]
        );
    }
}
