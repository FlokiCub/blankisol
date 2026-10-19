<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\ContainerProfitabilityAnalysis;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContainerOperationalIntegrationService
{
    public const PROVINCES_OCCIDENTE = [
        'Pinar del Río', 'Pinar del Rio',
        'Artemisa',
        'La Habana', 'Habana',
        'Mayabeque',
        'Matanzas',
        'Isla de la Juventud', 'Isla de la juventud',
    ];

    public const PROVINCES_CENTRO = [
        'Cienfuegos',
        'Villa Clara',
        'Sancti Spíritus', 'Sancti Spiritus',
        'Ciego de Ávila', 'Ciego de Avila',
        'Camagüey', 'Camaguey',
    ];

    public const PROVINCES_ORIENTE = [
        'Las Tunas',
        'Holguín', 'Holguin',
        'Granma',
        'Santiago de Cuba',
        'Guantánamo', 'Guantanamo',
    ];

    public function __construct(
        protected ContainerProfitabilityService $profitabilityService
    ) {}

    /**
     * Determina la región geográfica de Cuba según el nombre de la provincia o almacén.
     */
    public static function resolveRegion(?string $provinceOrRegion): string
    {
        if (!$provinceOrRegion) {
            return 'Occidente';
        }

        $val = trim($provinceOrRegion);

        foreach (self::PROVINCES_OCCIDENTE as $prov) {
            if (stripos($val, $prov) !== false) {
                return 'Occidente';
            }
        }

        foreach (self::PROVINCES_CENTRO as $prov) {
            if (stripos($val, $prov) !== false) {
                return 'Centro';
            }
        }

        foreach (self::PROVINCES_ORIENTE as $prov) {
            if (stripos($val, $prov) !== false) {
                return 'Oriente';
            }
        }

        // Si ya viene como nombre de región directa
        if (stripos($val, 'occ') !== false) {
            return 'Occidente';
        }
        if (stripos($val, 'cen') !== false) {
            return 'Centro';
        }
        if (stripos($val, 'ori') !== false) {
            return 'Oriente';
        }

        return 'Occidente';
    }

    /**
     * Lista los contenedores reales agrupados desde la tabla equipments disponibles para análisis (Fase 6.3.2).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getAvailableContainersForAnalysis(): Collection
    {
        $groups = Equipment::whereNotNull('solve_cargo_tracking_id')
            ->where('solve_cargo_tracking_id', '!=', '')
            ->select('solve_cargo_tracking_id')
            ->selectRaw('count(*) as total_equipments')
            ->selectRaw('min(customs_entry_at) as earliest_arrival')
            ->selectRaw('max(customs_entry_at) as latest_arrival')
            ->selectRaw('min(created_at) as first_seen')
            ->groupBy('solve_cargo_tracking_id')
            ->orderBy('earliest_arrival', 'desc')
            ->get();

        $existingAnalyses = ContainerProfitabilityAnalysis::all()->keyBy('container_id');

        return $groups->map(function ($g) use ($existingAnalyses) {
            $containerId = $g->solve_cargo_tracking_id;
            $analysis = $existingAnalyses->get($containerId);

            $arrival = $g->earliest_arrival ?? $g->first_seen;
            $arrivalDate = $arrival ? Carbon::parse($arrival)->toDateString() : now()->toDateString();

            // Consultar muestra de equipos para metadatos
            $sample = Equipment::where('solve_cargo_tracking_id', $containerId)->first();
            $clientData = $sample?->client_data ?? [];
            $transitaria = strtoupper($clientData['transitaria'] ?? 'ENCI');

            return [
                'container_id' => $containerId,
                'total_equipments' => (int) $g->total_equipments,
                'arrival_date' => $arrivalDate,
                'transitaria' => $transitaria,
                'has_analysis' => $analysis !== null,
                'economic_status' => $analysis ? $analysis->economic_status : 'sin_analisis',
                'analysis_id' => $analysis?->id,
                'decision_verdict' => $analysis?->decision_verdict,
                'net_profit' => $analysis?->net_profit,
                'net_margin_pct' => $analysis?->net_margin_pct,
            ];
        });
    }

    /**
     * Auto-recupera datos operativos y calcula kg por región desde rutas y almacenes (Fases 6.3.3 y 6.3.4).
     *
     * @return array<string, mixed>
     */
    public function extractOperationalData(string $containerId): array
    {
        $equipments = Equipment::with(['currentWarehouse', 'dispatchRoutes.destinationWarehouse'])
            ->where('solve_cargo_tracking_id', $containerId)
            ->get();

        if ($equipments->isEmpty()) {
            return [
                'container_id' => $containerId,
                'arrival_date' => now()->toDateString(),
                'transitaria' => 'ENCI',
                'status' => 'Abierto',
                'kg_occidente' => 0.0,
                'kg_centro' => 0.0,
                'kg_oriente' => 0.0,
                'kg_total' => 0.0,
                'equipment_count' => 0,
                'missing_data_flags' => ['no_equipments_found' => true],
            ];
        }

        $earliestArrival = null;
        $transitaria = 'ENCI';
        $kgOcc = 0.0;
        $kgCen = 0.0;
        $kgOri = 0.0;

        foreach ($equipments as $eq) {
            if ($eq->customs_entry_at) {
                if ($earliestArrival === null || $eq->customs_entry_at->lt($earliestArrival)) {
                    $earliestArrival = $eq->customs_entry_at;
                }
            }

            $client = $eq->client_data ?? [];
            if (!empty($client['transitaria'])) {
                $transitaria = strtoupper((string) $client['transitaria']);
            }

            // Determinar peso en kg
            $specs = $eq->technical_specs ?? [];
            $weight = (float) ($specs['weight_kg'] ?? $specs['peso_kg'] ?? $specs['weight'] ?? 0.0);
            if ($weight <= 0) {
                // Fallback por defecto según tipo de bulto
                $weight = 25.0;
            }

            // Determinar región de destino
            $destinationRegion = null;

            // 1. Desde la última ruta de despacho asignada
            $lastRoute = $eq->dispatchRoutes->sortByDesc('pivot.created_at')->first();
            if ($lastRoute && $lastRoute->destinationWarehouse) {
                $destinationRegion = self::resolveRegion($lastRoute->destinationWarehouse->region ?? $lastRoute->destinationWarehouse->province);
            }

            // 2. Desde el almacén actual
            if (!$destinationRegion && $eq->currentWarehouse) {
                $destinationRegion = self::resolveRegion($eq->currentWarehouse->region ?? $eq->currentWarehouse->province);
            }

            // 3. Desde los datos del cliente (provincia de entrega)
            if (!$destinationRegion) {
                $prov = $client['province'] ?? $client['provincia'] ?? null;
                $destinationRegion = self::resolveRegion($prov);
            }

            match ($destinationRegion) {
                'Centro' => $kgCen += $weight,
                'Oriente' => $kgOri += $weight,
                default => $kgOcc += $weight,
            };
        }

        $arrivalDate = $earliestArrival ? $earliestArrival->toDateString() : now()->toDateString();
        $kgTotal = round($kgOcc + $kgCen + $kgOri, 2);

        // Identificación de datos económicos faltantes en la operativa (Fase 6.3.5)
        $missingDataFlags = [
            'liters_port' => 'estimado_por_norma_tecnica',
            'driver_diet' => 'estimado_por_norma_tecnica',
            'labor_transitaria' => 'estimado_por_norma_tecnica',
            'fuel_distribution' => 'estimado_por_norma_tecnica',
            'has_actual_fuel_telemetry' => false,
            'is_precalculation' => true,
        ];

        return [
            'container_id' => $containerId,
            'arrival_date' => $arrivalDate,
            'transitaria' => $transitaria,
            'status' => 'En Análisis',
            'kg_occidente' => round($kgOcc, 2),
            'kg_centro' => round($kgCen, 2),
            'kg_oriente' => round($kgOri, 2),
            'kg_total' => $kgTotal,
            'equipment_count' => $equipments->count(),
            'missing_data_flags' => $missingDataFlags,
        ];
    }

    /**
     * Realiza el precálculo automático y vincula el contenedor real al módulo económico (Fases 6.3.6 y 6.3.7).
     */
    public function linkAndPrecalculateContainer(string $containerId, ?User $user = null): ContainerProfitabilityAnalysis
    {
        $operationalData = $this->extractOperationalData($containerId);

        // Ejecutar motor de cálculo económico
        $calc = $this->profitabilityService->calculate($operationalData);

        return ContainerProfitabilityAnalysis::updateOrCreate(
            ['container_id' => $containerId],
            [
                'arrival_date' => $calc['arrival_date'],
                'transitaria' => $calc['transitaria'],
                'status' => 'En Análisis',
                'economic_status' => 'abierto', // Estado económico separado del operativo (Fase 6.3.6)
                'kg_occidente' => $calc['weights']['kg_occidente'],
                'kg_centro' => $calc['weights']['kg_centro'],
                'kg_oriente' => $calc['weights']['kg_oriente'],
                'kg_total' => $calc['weights']['kg_total'],
                'revenue_occidente' => $calc['revenues']['occidente'],
                'revenue_centro' => $calc['revenues']['centro'],
                'revenue_oriente' => $calc['revenues']['oriente'],
                'revenue_other' => $calc['revenues']['other'],
                'revenue_total' => $calc['revenues']['total'],
                'cost_port_total' => $calc['costs_breakdown']['port']['total'],
                'cost_transitaria_total' => $calc['costs_breakdown']['transitaria']['total'],
                'cost_distribution_occidente' => $calc['costs_breakdown']['distribution']['occidente']['total'],
                'cost_distribution_centro' => $calc['costs_breakdown']['distribution']['centro']['total'],
                'cost_distribution_oriente' => $calc['costs_breakdown']['distribution']['oriente']['total'],
                'cost_distribution_total' => $calc['costs_breakdown']['distribution']['total'],
                'cost_commission_total' => $calc['costs_breakdown']['commissions']['total'],
                'cost_provisions_and_bank' => $calc['costs_breakdown']['provisions_and_bank']['total'],
                'cost_variable_total' => $calc['costs_breakdown']['total_variable'],
                'contribution_margin' => $calc['contribution_margin']['amount'],
                'contribution_margin_pct' => $calc['contribution_margin']['pct'],
                'fixed_costs_assigned_total' => $calc['fixed_costs_assigned']['total'],
                'taxes_sales_and_services' => $calc['taxes']['sales_and_services'],
                'tax_territorial_contribution' => $calc['taxes']['territorial_contribution'],
                'profit_before_tax' => $calc['net_results']['profit_before_tax'],
                'estimated_income_tax' => $calc['taxes']['estimated_income_tax'],
                'net_profit' => $calc['net_results']['net_profit'],
                'net_margin_pct' => $calc['net_results']['net_margin_pct'],
                'cost_per_kg' => $calc['net_results']['cost_per_kg'],
                'revenue_per_kg' => $calc['revenues']['revenue_per_kg'],
                'profit_before_tax_per_kg' => $calc['net_results']['profit_before_tax_per_kg'],
                'breakeven_kg' => $calc['breakeven']['breakeven_kg'],
                'breakeven_tariff_usd_kg' => $calc['breakeven']['breakeven_tariff_usd_kg'],
                'decision_verdict' => $calc['decision']['verdict'],
                'decision_summary' => $calc['decision']['summary'],
                'regional_breakdown' => $calc['regional_breakdown'],
                'applied_parameters' => array_merge($calc, [
                    'operational_metadata' => [
                        'equipment_count' => $operationalData['equipment_count'],
                        'missing_data_flags' => $operationalData['missing_data_flags'],
                        'linked_at' => now()->toIso8601String(),
                        'linked_by' => $user?->id,
                    ],
                ]),
                'created_by' => $user?->id,
            ]
        );

        $this->profitabilityService->generateAndPersistAlerts($analysis, $calc);

        return $analysis;
    }
}
