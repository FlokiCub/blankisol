<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedCostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'cost_center',
        'classification',
        'is_salary',
        'monthly_base_value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_salary' => 'boolean',
        'monthly_base_value' => 'float',
        'is_active' => 'boolean',
    ];

    public function monthlyEntries(): HasMany
    {
        return $this->hasMany(FixedCostMonthlyEntry::class);
    }

    /**
     * Calcula los costos fijos mensuales por centro de costo a partir de los conceptos
     * individuales y las entradas reales del mes, aplicando los tributos salariales:
     * - 14% Contribución a la Seguridad Social (empleador) sobre salarios propios.
     * - 5% Impuesto por Utilización de la Fuerza de Trabajo sobre salarios propios.
     *
     * @return array{
     *     general_fixed_usd: float,
     *     occidente_fixed_usd: float,
     *     centro_fixed_usd: float,
     *     oriente_fixed_usd: float,
     *     total_fixed_usd: float,
     *     salary_bases: array<string, float>,
     *     social_security_taxes: array<string, float>,
     *     labor_force_taxes: array<string, float>,
     *     breakdown_by_item: array<string, array<string, mixed>>
     * }
     */
    public static function calculateMonthlyFixedCosts(string $yearMonth): array
    {
        $items = static::where('is_active', true)->get();
        if ($items->isEmpty()) {
            return [
                'general_fixed_usd' => 2640.07,
                'occidente_fixed_usd' => 200.00,
                'centro_fixed_usd' => 200.00,
                'oriente_fixed_usd' => 200.00,
                'total_fixed_usd' => 3240.07,
                'salary_bases' => ['GENERAL' => 1360.0, 'OCC' => 0.0, 'CEN' => 0.0, 'ORI' => 0.0],
                'social_security_taxes' => ['GENERAL' => 190.40, 'OCC' => 0.0, 'CEN' => 0.0, 'ORI' => 0.0],
                'labor_force_taxes' => ['GENERAL' => 68.00, 'OCC' => 0.0, 'CEN' => 0.0, 'ORI' => 0.0],
                'breakdown_by_item' => [],
            ];
        }

        $entries = FixedCostMonthlyEntry::where('year_month', $yearMonth)
            ->get()
            ->keyBy('fixed_cost_item_id');

        $pctSS = (float) EconomicParameter::getValue('Contrib_SS', 0.14);
        $pctFT = (float) EconomicParameter::getValue('Imp_Fuerza_Trabajo', 0.05);

        $costCenters = ['GENERAL', 'OCC', 'CEN', 'ORI'];
        $salaryBases = array_fill_keys($costCenters, 0.0);
        $otherCosts = array_fill_keys($costCenters, 0.0);
        $explicitTaxes = [
            'SS' => array_fill_keys($costCenters, null),
            'FT' => array_fill_keys($costCenters, null),
        ];

        $breakdown = [];

        foreach ($items as $item) {
            $cc = strtoupper($item->cost_center);
            if (!in_array($cc, $costCenters, true)) {
                $cc = 'GENERAL';
            }

            $entry = $entries->get($item->id);
            $amount = $entry ? (float) $entry->actual_amount : (float) $item->monthly_base_value;

            // Depreciación automática desde tabla assets (Fase 6.2.4)
            if ($item->code === 'CF10' && !$entry) {
                $assetDeprec = Asset::calculateTotalMonthlyDepreciation($yearMonth);
                if ($assetDeprec > 0) {
                    $amount = $assetDeprec;
                }
            }

            // Manejo especial de filas de tributos predefinidas (SS-*, FT-*)
            if (str_starts_with($item->code, 'SS-')) {
                if ($entry) {
                    $explicitTaxes['SS'][$cc] = (float) $entry->actual_amount;
                }
                $breakdown[$item->code] = [
                    'name' => $item->name,
                    'cost_center' => $cc,
                    'is_salary' => false,
                    'amount' => $amount,
                    'is_tax' => true,
                ];
                continue;
            }

            if (str_starts_with($item->code, 'FT-')) {
                if ($entry) {
                    $explicitTaxes['FT'][$cc] = (float) $entry->actual_amount;
                }
                $breakdown[$item->code] = [
                    'name' => $item->name,
                    'cost_center' => $cc,
                    'is_salary' => false,
                    'amount' => $amount,
                    'is_tax' => true,
                ];
                continue;
            }

            if ($item->is_salary) {
                $salaryBases[$cc] += $amount;
            } else {
                $otherCosts[$cc] += $amount;
            }

            $breakdown[$item->code] = [
                'name' => $item->name,
                'cost_center' => $cc,
                'is_salary' => $item->is_salary,
                'amount' => $amount,
                'is_tax' => false,
            ];
        }

        $ssTotals = [];
        $ftTotals = [];
        $finalTotals = [];

        foreach ($costCenters as $cc) {
            $ssTotals[$cc] = $explicitTaxes['SS'][$cc] ?? round($salaryBases[$cc] * $pctSS, 2);
            $ftTotals[$cc] = $explicitTaxes['FT'][$cc] ?? round($salaryBases[$cc] * $pctFT, 2);
            $finalTotals[$cc] = round($salaryBases[$cc] + $otherCosts[$cc] + $ssTotals[$cc] + $ftTotals[$cc], 2);
        }

        return [
            'general_fixed_usd' => $finalTotals['GENERAL'],
            'occidente_fixed_usd' => $finalTotals['OCC'],
            'centro_fixed_usd' => $finalTotals['CEN'],
            'oriente_fixed_usd' => $finalTotals['ORI'],
            'total_fixed_usd' => round(array_sum($finalTotals), 2),
            'salary_bases' => $salaryBases,
            'social_security_taxes' => $ssTotals,
            'labor_force_taxes' => $ftTotals,
            'breakdown_by_item' => $breakdown,
        ];
    }
}
