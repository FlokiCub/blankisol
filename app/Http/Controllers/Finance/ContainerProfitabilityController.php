<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAlert;
use App\Models\EconomicParameter;
use App\Models\FuelAndTariffRate;
use App\Services\Finance\ContainerOperationalIntegrationService;
use App\Services\Finance\ContainerProfitabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContainerProfitabilityController extends Controller
{
    public function __construct(
        protected ContainerProfitabilityService $profitabilityService,
        protected ContainerOperationalIntegrationService $integrationService
    ) {}

    /**
     * Construye la consulta filtrada multicriterio compartida entre listado y exportaciones.
     */
    protected function buildFilteredQuery(Request $request): Builder
    {
        $baseQuery = ContainerProfitabilityAnalysis::query();

        if ($request->filled('period')) {
            $baseQuery->where('arrival_date', 'like', $request->input('period') . '%');
        }

        if ($request->filled('transitaria')) {
            $baseQuery->where('transitaria', $request->input('transitaria'));
        }

        if ($request->filled('verdict')) {
            $baseQuery->where('decision_verdict', $request->input('verdict'));
        }

        if ($request->filled('economic_status')) {
            $baseQuery->where('economic_status', $request->input('economic_status'));
        }

        if ($request->filled('region')) {
            $reg = $request->input('region');
            if ($reg === 'OCC') {
                $baseQuery->where('kg_occidente', '>', 0);
            } elseif ($reg === 'CEN') {
                $baseQuery->where('kg_centro', '>', 0);
            } elseif ($reg === 'ORI') {
                $baseQuery->where('kg_oriente', '>', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $baseQuery->where('container_id', 'like', '%' . $search . '%');
        }

        return $baseQuery;
    }

    /**
     * Listado general y KPIs ejecutivos de rentabilidad de contenedores.
     */
    public function index(Request $request): View
    {
        $baseQuery = $this->buildFilteredQuery($request);

        // Paginación con parámetros de búsqueda
        $analyses = (clone $baseQuery)->with(['creator', 'equipments'])->latest('arrival_date')->paginate(12)->withQueryString();

        // 18+ Indicadores Ejecutivos Consolidados (Calculados en Servidor)
        $totalContainers = (clone $baseQuery)->count();
        $totalKg = (float) (clone $baseQuery)->sum('kg_total');
        $avgKgPerContainer = $totalContainers > 0 ? round($totalKg / $totalContainers, 1) : 0.0;

        $totalRevenue = (float) (clone $baseQuery)->sum('revenue_total');
        $avgRevenuePerKg = $totalKg > 0 ? round($totalRevenue / $totalKg, 4) : 0.0;

        $totalVariableCost = (float) (clone $baseQuery)->sum('cost_variable_total');
        $avgVariableCostPerKg = $totalKg > 0 ? round($totalVariableCost / $totalKg, 4) : 0.0;

        $totalContributionMargin = (float) (clone $baseQuery)->sum('contribution_margin');
        $contributionMarginRatio = $totalRevenue > 0 ? round(($totalContributionMargin / $totalRevenue) * 100, 1) : 0.0;

        $totalFixedAssigned = (float) (clone $baseQuery)->sum('fixed_costs_assigned_total');
        $totalProfitBeforeTax = (float) (clone $baseQuery)->sum('profit_before_tax');

        $totalTaxSalesServices = (float) (clone $baseQuery)->sum('taxes_sales_and_services');
        $totalTaxTerritorial = (float) (clone $baseQuery)->sum('tax_territorial_contribution');
        $totalEstimatedIncomeTax = (float) (clone $baseQuery)->sum('estimated_income_tax');

        $totalNetProfit = (float) (clone $baseQuery)->sum('net_profit');
        $avgNetMargin = $totalRevenue > 0 ? round(($totalNetProfit / $totalRevenue) * 100, 1) : 0.0;

        $avgBreakevenKg = $totalContainers > 0 ? round((float) (clone $baseQuery)->avg('breakeven_kg'), 0) : 0.0;
        $avgSafetyMarginPct = $totalKg > 0 ? round((($totalKg - ($avgBreakevenKg * $totalContainers)) / $totalKg) * 100, 1) : 0.0;

        // Veredictos de Despacho
        $feasibleCount = (clone $baseQuery)->where('decision_verdict', 'FACTIBLE')->count();
        $marginalCount = (clone $baseQuery)->where('decision_verdict', 'MARGINAL')->count();
        $unfeasibleCount = (clone $baseQuery)->where('decision_verdict', 'NO_FACTIBLE')->count();

        // Estados Económicos del Ciclo
        $openCount = (clone $baseQuery)->where('economic_status', 'abierto')->count();
        $inReviewCount = (clone $baseQuery)->where('economic_status', 'en_revision')->count();
        $closedCount = (clone $baseQuery)->where('economic_status', 'cerrado')->count();

        // Gastos Fijos no Absorbidos para el Período
        $selectedPeriod = $request->input('period', now()->format('Y-m'));
        $unabsorbed = $this->profitabilityService->calculateUnabsorbedFixedCosts($selectedPeriod);

        // 6.5.2 — Gráfico de Evolución Mensual
        $allAnalyses = ContainerProfitabilityAnalysis::orderBy('arrival_date')->get();
        $monthlyGrouped = $allAnalyses->groupBy(fn ($item) => $item->arrival_date->format('Y-m'));

        $monthlyEvolution = [
            'labels' => [],
            'revenues' => [],
            'costs' => [],
            'net_profits' => [],
            'margins' => [],
        ];

        foreach ($monthlyGrouped as $monthKey => $group) {
            $monthRev = (float) $group->sum('revenue_total');
            $monthCost = (float) ($group->sum('cost_variable_total') + $group->sum('fixed_costs_assigned_total'));
            $monthProfit = (float) $group->sum('net_profit');
            $monthMargin = $monthRev > 0 ? round(($monthProfit / $monthRev) * 100, 1) : 0.0;

            $monthlyEvolution['labels'][] = $monthKey;
            $monthlyEvolution['revenues'][] = round($monthRev, 2);
            $monthlyEvolution['costs'][] = round($monthCost, 2);
            $monthlyEvolution['net_profits'][] = round($monthProfit, 2);
            $monthlyEvolution['margins'][] = $monthMargin;
        }

        // 6.5.3 — Estructura de Costes por Categoría y Etapa
        $costStructure = [
            'puerto' => round((float) (clone $baseQuery)->sum('cost_port_total'), 2),
            'transitaria' => round((float) (clone $baseQuery)->sum('cost_transitaria_total'), 2),
            'distribucion' => round((float) (clone $baseQuery)->sum('cost_distribution_total'), 2),
            'comisiones' => round((float) (clone $baseQuery)->sum('cost_commission_total'), 2),
            'fijos_asignados' => round((float) (clone $baseQuery)->sum('fixed_costs_assigned_total'), 2),
            'tributos' => round((float) ((clone $baseQuery)->sum('taxes_sales_and_services') + (clone $baseQuery)->sum('tax_territorial_contribution') + (clone $baseQuery)->sum('estimated_income_tax')), 2),
        ];

        // 6.5.4 — Comparativa de Rentabilidad por Región
        $regionalComparison = [
            'occidente' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_occidente'), 0),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_occidente'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_occidente'), 2),
                'profit' => round((float) ((clone $baseQuery)->sum('revenue_occidente') - (clone $baseQuery)->sum('cost_distribution_occidente')), 2),
            ],
            'centro' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_centro'), 0),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_centro'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_centro'), 2),
                'profit' => round((float) ((clone $baseQuery)->sum('revenue_centro') - (clone $baseQuery)->sum('cost_distribution_centro')), 2),
            ],
            'oriente' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_oriente'), 0),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_oriente'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_oriente'), 2),
                'profit' => round((float) ((clone $baseQuery)->sum('revenue_oriente') - (clone $baseQuery)->sum('cost_distribution_oriente')), 2),
            ],
        ];

        $activeRate = FuelAndTariffRate::getRateForDate();

        return view('profitability.index', compact(
            'analyses',
            'totalContainers',
            'totalKg',
            'avgKgPerContainer',
            'totalRevenue',
            'avgRevenuePerKg',
            'totalVariableCost',
            'avgVariableCostPerKg',
            'totalContributionMargin',
            'contributionMarginRatio',
            'totalFixedAssigned',
            'totalProfitBeforeTax',
            'totalTaxSalesServices',
            'totalTaxTerritorial',
            'totalEstimatedIncomeTax',
            'totalNetProfit',
            'avgNetMargin',
            'avgBreakevenKg',
            'avgSafetyMarginPct',
            'feasibleCount',
            'marginalCount',
            'unfeasibleCount',
            'openCount',
            'inReviewCount',
            'closedCount',
            'selectedPeriod',
            'unabsorbed',
            'monthlyEvolution',
            'costStructure',
            'regionalComparison',
            'activeRate'
        ));
    }

    /**
     * Formulario para registrar y evaluar un contenedor.
     */
    public function create(): View
    {
        $rateCard = FuelAndTariffRate::getRateForDate();
        $dieselPrice = $rateCard ? (float) $rateCard->diesel_price_usd_l : 2.0000;
        $tariff = $rateCard ? (float) $rateCard->tariff_occidente_usd_kg : 0.8000;

        return view('profitability.create', compact('rateCard', 'dieselPrice', 'tariff'));
    }

    /**
     * Procesa, calcula y guarda el análisis económico de un contenedor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'container_id' => ['required', 'string', 'max:50'],
            'arrival_date' => ['required', 'date'],
            'transitaria' => ['required', 'string', 'in:ENCI,A.V,OTRA'],
            'status' => ['nullable', 'string', 'in:Cerrado,En Proceso,Estimado'],
            'kg_occidente' => ['required', 'numeric', 'min:0'],
            'kg_centro' => ['required', 'numeric', 'min:0'],
            'kg_oriente' => ['required', 'numeric', 'min:0'],
            'other_revenue' => ['nullable', 'numeric', 'min:0'],
            'trips_port' => ['nullable', 'numeric', 'min:0'],
            'liters_port' => ['nullable', 'numeric', 'min:0'],
            'diet_port' => ['nullable', 'numeric', 'min:0'],
            'labor_transitaria' => ['nullable', 'numeric', 'min:0'],
            'service_transitaria' => ['nullable', 'numeric', 'min:0'],
            'storage_transitaria' => ['nullable', 'numeric', 'min:0'],
            'liters_dist_occ' => ['nullable', 'numeric', 'min:0'],
            'liters_tras_cen' => ['nullable', 'numeric', 'min:0'],
            'liters_dist_cen' => ['nullable', 'numeric', 'min:0'],
            'labor_shifts_cen' => ['nullable', 'numeric', 'min:0'],
            'liters_tras_ori' => ['nullable', 'numeric', 'min:0'],
            'liters_dist_ori' => ['nullable', 'numeric', 'min:0'],
            'labor_shifts_ori' => ['nullable', 'numeric', 'min:0'],
            'other_direct_costs' => ['nullable', 'numeric', 'min:0'],
            'custom_diesel_price' => ['nullable', 'numeric', 'min:0'],
            'custom_tariff_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $analysis = $this->profitabilityService->calculateAndSave($validated, Auth::id());

            return redirect()->route('profitability.show', $analysis)
                ->with('success', "Análisis económico del contenedor {$analysis->container_id} calculado y archivado exitosamente.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al calcular rentabilidad: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el desglose económico de las 4 etapas y matriz del contenedor.
     */
    public function show(ContainerProfitabilityAnalysis $analysis): View
    {
        // Si no tiene alertas generadas previamente, evaluar y persistir según el modelo
        if ($analysis->alerts()->count() === 0) {
            $baseData = [
                'container_id' => $analysis->container_id,
                'arrival_date' => $analysis->arrival_date->toDateString(),
                'transitaria' => $analysis->transitaria,
                'status' => $analysis->status,
                'kg_occidente' => $analysis->kg_occidente,
                'kg_centro' => $analysis->kg_centro,
                'kg_oriente' => $analysis->kg_oriente,
                'other_revenue' => $analysis->revenue_other,
                'custom_diesel_price' => $analysis->applied_parameters['diesel_price'] ?? null,
            ];
            $calc = $this->profitabilityService->calculate($baseData);
            $this->profitabilityService->generateAndPersistAlerts($analysis, $calc);
        }

        $analysis->load(['creator', 'closer', 'alerts.resolver']);

        // Sensibilidad precalculada frente a variaciones del diésel y tarifa
        $baseData = [
            'container_id' => $analysis->container_id,
            'arrival_date' => $analysis->arrival_date->toDateString(),
            'transitaria' => $analysis->transitaria,
            'kg_occidente' => $analysis->kg_occidente,
            'kg_centro' => $analysis->kg_centro,
            'kg_oriente' => $analysis->kg_oriente,
            'other_revenue' => $analysis->revenue_other,
        ];

        $sensitivity = $this->profitabilityService->simulateSensitivity(
            $baseData,
            [1.50, 2.00, 2.50, 3.00],
            [0.70, 0.80, 0.90, 1.00]
        );

        return view('profitability.show', compact('analysis', 'sensitivity'));
    }

    /**
     * Simulador interactivo de escenarios de rentabilidad.
     */
    public function simulator(Request $request): View
    {
        $rateCard = FuelAndTariffRate::getRateForDate();
        $defaultDiesel = $rateCard ? (float) $rateCard->diesel_price_usd_l : 2.0000;
        $defaultTariff = $rateCard ? (float) $rateCard->tariff_occidente_usd_kg : 0.8000;

        return view('profitability.simulator', compact('defaultDiesel', 'defaultTariff', 'rateCard'));
    }

    /**
     * API JSON para el simulador interactivo reactivo en tiempo real.
     */
    public function simulateLive(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kg_occidente' => ['required', 'numeric', 'min:0'],
            'kg_centro' => ['required', 'numeric', 'min:0'],
            'kg_oriente' => ['required', 'numeric', 'min:0'],
            'custom_diesel_price' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'custom_tariff_rate' => ['required', 'numeric', 'min:0.1', 'max:5'],
            'transitaria' => ['nullable', 'string'],
        ]);

        $calculation = $this->profitabilityService->calculate($validated);

        return response()->json([
            'success' => true,
            'data' => $calculation,
        ]);
    }

    /**
     * Exportación CSV oficial del informe de rentabilidad del contenedor.
     */
    public function exportCsv(ContainerProfitabilityAnalysis $analysis): StreamedResponse
    {
        $filename = "rentabilidad_contenedor_{$analysis->container_id}_" . now()->format('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($analysis) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($handle, ['SCGI BLANKISOL — INFORME DE RENTABILIDAD Y FACTIBILIDAD ECONÓMICA']);
            fputcsv($handle, ['Contenedor', $analysis->container_id]);
            fputcsv($handle, ['Fecha de Arribo', $analysis->arrival_date->format('d/m/Y')]);
            fputcsv($handle, ['Transitaria', $analysis->transitaria]);
            fputcsv($handle, ['Veredicto', $analysis->decision_verdict]);
            fputcsv($handle, ['Resumen', $analysis->decision_summary]);
            fputcsv($handle, []);

            fputcsv($handle, ['CONCEPTO', 'OCCIDENTE', 'CENTRO', 'ORIENTE', 'TOTAL']);
            fputcsv($handle, [
                'Carga (kg)',
                number_format((float) $analysis->kg_occidente, 2),
                number_format((float) $analysis->kg_centro, 2),
                number_format((float) $analysis->kg_oriente, 2),
                number_format((float) $analysis->kg_total, 2),
            ]);
            fputcsv($handle, [
                'Ingresos (USD)',
                number_format((float) $analysis->revenue_occidente, 2),
                number_format((float) $analysis->revenue_centro, 2),
                number_format((float) $analysis->revenue_oriente, 2),
                number_format((float) $analysis->revenue_total, 2),
            ]);
            fputcsv($handle, []);

            fputcsv($handle, ['DESGLOSE DE COSTOS Y RENTABILIDAD (USD)']);
            fputcsv($handle, ['Etapa 1: Puerto Mariel', '', '', '', number_format((float) $analysis->cost_port_total, 2)]);
            fputcsv($handle, ['Etapa 2: Transitaria y Desagrupe', '', '', '', number_format((float) $analysis->cost_transitaria_total, 2)]);
            fputcsv($handle, ['Etapa 3: Transporte Regional', number_format((float) $analysis->cost_distribution_occidente, 2), number_format((float) $analysis->cost_distribution_centro, 2), number_format((float) $analysis->cost_distribution_oriente, 2), number_format((float) $analysis->cost_distribution_total, 2)]);
            fputcsv($handle, ['Etapa 4: Comercialización y Deducciones', '', '', '', number_format((float) ($analysis->cost_commission_total + $analysis->cost_provisions_and_bank), 2)]);
            fputcsv($handle, ['TOTAL COSTOS VARIABLES', '', '', '', number_format((float) $analysis->cost_variable_total, 2)]);
            fputcsv($handle, ['MARGEN DE CONTRIBUCIÓN (MC)', '', '', '', number_format((float) $analysis->contribution_margin, 2)]);
            fputcsv($handle, ['MC %', '', '', '', number_format((float) $analysis->contribution_margin_pct, 2) . '%']);
            fputcsv($handle, ['Costos Fijos Asignados', '', '', '', number_format((float) $analysis->fixed_costs_assigned_total, 2)]);
            fputcsv($handle, ['Impuestos sobre Ingresos (10% + 1%)', '', '', '', number_format((float) ($analysis->taxes_sales_and_services + $analysis->tax_territorial_contribution), 2)]);
            fputcsv($handle, ['UTILIDAD ANTES DE IMPUESTOS', '', '', '', number_format((float) $analysis->profit_before_tax, 2)]);
            fputcsv($handle, ['Impuesto sobre Utilidades (35%)', '', '', '', number_format((float) $analysis->estimated_income_tax, 2)]);
            fputcsv($handle, ['UTILIDAD NETA ESTIMADA', '', '', '', number_format((float) $analysis->net_profit, 2)]);
            fputcsv($handle, ['Margen Neto %', '', '', '', number_format((float) $analysis->net_margin_pct, 2) . '%']);
            fputcsv($handle, ['Tarifa Mínima de Equilibrio (USD/kg)', '', '', '', '$' . number_format((float) $analysis->breakeven_tariff_usd_kg, 4)]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Vista de listado de contenedores operativos disponibles para análisis (Fase 6.3.2).
     */
    public function operationalContainers(): View
    {
        $containers = $this->integrationService->getAvailableContainersForAnalysis();

        return view('profitability.operational_containers', compact('containers'));
    }

    /**
     * Vincula un contenedor real existente, auto-recupera datos operativos y genera el precálculo (Fase 6.3.7).
     */
    public function linkOperationalContainer(string $containerId): RedirectResponse
    {
        try {
            $analysis = $this->integrationService->linkAndPrecalculateContainer($containerId, Auth::user());

            return redirect()->route('profitability.show', $analysis)
                ->with('success', "Contenedor {$containerId} vinculado exitosamente al módulo económico con precálculo de Etapa 1 generado.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al vincular contenedor operativo: ' . $e->getMessage());
        }
    }

    /**
     * Cierra económicamente el análisis congelando snapshot completo (Fase 6.2.8 / 6.4).
     */
    public function closeAnalysis(Request $request, ContainerProfitabilityAnalysis $analysis): RedirectResponse
    {
        $notes = $request->input('notes');

        try {
            $this->profitabilityService->closeEconomicAnalysis($analysis, Auth::user(), $notes);

            return redirect()->route('profitability.show', $analysis)
                ->with('success', "El análisis económico del contenedor {$analysis->container_id} ha sido cerrado oficialmente con congelación de tarifas.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cerrar análisis económico: ' . $e->getMessage());
        }
    }

    /**
     * Reabre un análisis económico cerrado con justificación requerida (Fase 6.2.8 / 6.4).
     */
    public function reopenAnalysis(Request $request, ContainerProfitabilityAnalysis $analysis): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        try {
            $this->profitabilityService->reopenEconomicAnalysis($analysis, Auth::user(), $request->input('reason'));

            return redirect()->route('profitability.show', $analysis)
                ->with('warning', "El análisis económico del contenedor {$analysis->container_id} ha sido reabierto para revisión.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reabrir análisis: ' . $e->getMessage());
        }
    }

    /**
     * Vista RESUMEN_MES con acumulados y comparación intermensual (Fase 6.5.5).
     */
    public function monthlySummary(Request $request): View
    {
        $allAnalyses = ContainerProfitabilityAnalysis::orderBy('arrival_date')->get();
        $monthlyGroups = $allAnalyses->groupBy(fn ($item) => $item->arrival_date->format('Y-m'));

        $monthsData = [];
        $previousMonth = null;

        foreach ($monthlyGroups as $yearMonth => $items) {
            $count = $items->count();
            $kgTotal = (float) $items->sum('kg_total');
            $revenueTotal = (float) $items->sum('revenue_total');
            $costPort = (float) $items->sum('cost_port_total');
            $costTransitaria = (float) $items->sum('cost_transitaria_total');
            $costDistribution = (float) $items->sum('cost_distribution_total');
            $costCommission = (float) $items->sum('cost_commission_total');
            $costVariable = (float) $items->sum('cost_variable_total');
            $contributionMargin = (float) $items->sum('contribution_margin');
            $contributionMarginRatio = $revenueTotal > 0 ? round(($contributionMargin / $revenueTotal) * 100, 1) : 0.0;
            $fixedAssigned = (float) $items->sum('fixed_costs_assigned_total');
            $profitBeforeTax = (float) $items->sum('profit_before_tax');
            $taxes = (float) ($items->sum('taxes_sales_and_services') + $items->sum('tax_territorial_contribution') + $items->sum('estimated_income_tax'));
            $netProfit = (float) $items->sum('net_profit');
            $netMarginPct = $revenueTotal > 0 ? round(($netProfit / $revenueTotal) * 100, 1) : 0.0;

            $unabsorbed = $this->profitabilityService->calculateUnabsorbedFixedCosts($yearMonth);
            $closing = \App\Models\MonthlyClosing::where('year_month', $yearMonth)->first();

            // Variaciones intermensuales (MoM)
            $momKg = $previousMonth && $previousMonth['kg_total'] > 0
                ? round((($kgTotal - $previousMonth['kg_total']) / $previousMonth['kg_total']) * 100, 1)
                : null;
            $momRevenue = $previousMonth && $previousMonth['revenue_total'] > 0
                ? round((($revenueTotal - $previousMonth['revenue_total']) / $previousMonth['revenue_total']) * 100, 1)
                : null;
            $momProfit = $previousMonth && $previousMonth['net_profit'] != 0
                ? round((($netProfit - $previousMonth['net_profit']) / abs($previousMonth['net_profit'])) * 100, 1)
                : null;

            $currentMonthData = [
                'year_month' => $yearMonth,
                'containers_count' => $count,
                'kg_total' => $kgTotal,
                'revenue_total' => $revenueTotal,
                'cost_port_total' => $costPort,
                'cost_transitaria_total' => $costTransitaria,
                'cost_distribution_total' => $costDistribution,
                'cost_commission_total' => $costCommission,
                'cost_variable_total' => $costVariable,
                'contribution_margin' => $contributionMargin,
                'contribution_margin_ratio' => $contributionMarginRatio,
                'fixed_costs_assigned' => $fixedAssigned,
                'unabsorbed_fixed' => $unabsorbed['unabsorbed_fixed'] ?? 0.0,
                'total_month_fixed' => $unabsorbed['total_month_fixed'] ?? 0.0,
                'profit_before_tax' => $profitBeforeTax,
                'taxes_total' => $taxes,
                'net_profit' => $netProfit,
                'net_margin_pct' => $netMarginPct,
                'is_closed' => $closing ? $closing->isClosed() : false,
                'closing_status' => $closing ? $closing->status : 'abierto',
                'mom_kg' => $momKg,
                'mom_revenue' => $momRevenue,
                'mom_profit' => $momProfit,
            ];

            $monthsData[$yearMonth] = $currentMonthData;
            $previousMonth = $currentMonthData;
        }

        // Totales Acumulados (YTD)
        $ytd = [
            'containers_count' => $allAnalyses->count(),
            'kg_total' => (float) $allAnalyses->sum('kg_total'),
            'revenue_total' => (float) $allAnalyses->sum('revenue_total'),
            'cost_variable_total' => (float) $allAnalyses->sum('cost_variable_total'),
            'contribution_margin' => (float) $allAnalyses->sum('contribution_margin'),
            'fixed_costs_assigned' => (float) $allAnalyses->sum('fixed_costs_assigned_total'),
            'profit_before_tax' => (float) $allAnalyses->sum('profit_before_tax'),
            'taxes_total' => (float) ($allAnalyses->sum('taxes_sales_and_services') + $allAnalyses->sum('tax_territorial_contribution') + $allAnalyses->sum('estimated_income_tax')),
            'net_profit' => (float) $allAnalyses->sum('net_profit'),
        ];
        $ytd['contribution_margin_ratio'] = $ytd['revenue_total'] > 0 ? round(($ytd['contribution_margin'] / $ytd['revenue_total']) * 100, 1) : 0.0;
        $ytd['net_margin_pct'] = $ytd['revenue_total'] > 0 ? round(($ytd['net_profit'] / $ytd['revenue_total']) * 100, 1) : 0.0;

        return view('profitability.monthly_summary', compact('monthsData', 'ytd'));
    }

    /**
     * Exportación de informe ejecutivo en formato imprimible / PDF (Fase 6.5.8).
     */
    public function exportPdfReport(Request $request): View
    {
        $baseQuery = $this->buildFilteredQuery($request);
        $analyses = (clone $baseQuery)->with(['creator', 'equipments'])->orderBy('arrival_date', 'desc')->get();

        $totalContainers = (clone $baseQuery)->count();
        $totalKg = (float) (clone $baseQuery)->sum('kg_total');
        $avgKgPerContainer = $totalContainers > 0 ? round($totalKg / $totalContainers, 1) : 0.0;

        $totalRevenue = (float) (clone $baseQuery)->sum('revenue_total');
        $avgRevenuePerKg = $totalKg > 0 ? round($totalRevenue / $totalKg, 4) : 0.0;

        $totalVariableCost = (float) (clone $baseQuery)->sum('cost_variable_total');
        $avgVariableCostPerKg = $totalKg > 0 ? round($totalVariableCost / $totalKg, 4) : 0.0;

        $totalContributionMargin = (float) (clone $baseQuery)->sum('contribution_margin');
        $contributionMarginRatio = $totalRevenue > 0 ? round(($totalContributionMargin / $totalRevenue) * 100, 1) : 0.0;

        $totalFixedAssigned = (float) (clone $baseQuery)->sum('fixed_costs_assigned_total');
        $totalProfitBeforeTax = (float) (clone $baseQuery)->sum('profit_before_tax');

        $totalTaxSalesServices = (float) (clone $baseQuery)->sum('taxes_sales_and_services');
        $totalTaxTerritorial = (float) (clone $baseQuery)->sum('tax_territorial_contribution');
        $totalEstimatedIncomeTax = (float) (clone $baseQuery)->sum('estimated_income_tax');
        $totalTaxes = $totalTaxSalesServices + $totalTaxTerritorial + $totalEstimatedIncomeTax;

        $totalNetProfit = (float) (clone $baseQuery)->sum('net_profit');
        $avgNetMargin = $totalRevenue > 0 ? round(($totalNetProfit / $totalRevenue) * 100, 1) : 0.0;

        $feasibleCount = (clone $baseQuery)->where('decision_verdict', 'FACTIBLE')->count();
        $marginalCount = (clone $baseQuery)->where('decision_verdict', 'MARGINAL')->count();
        $unfeasibleCount = (clone $baseQuery)->where('decision_verdict', 'NO_FACTIBLE')->count();

        $openCount = (clone $baseQuery)->where('economic_status', 'abierto')->count();
        $inReviewCount = (clone $baseQuery)->where('economic_status', 'en_revision')->count();
        $closedCount = (clone $baseQuery)->where('economic_status', 'cerrado')->count();

        $costStructure = [
            'puerto' => round((float) (clone $baseQuery)->sum('cost_port_total'), 2),
            'transitaria' => round((float) (clone $baseQuery)->sum('cost_transitaria_total'), 2),
            'distribucion' => round((float) (clone $baseQuery)->sum('cost_distribution_total'), 2),
            'comisiones' => round((float) (clone $baseQuery)->sum('cost_commission_total'), 2),
        ];
        $totalCostsSum = array_sum($costStructure) + $totalFixedAssigned;

        $regionalComparison = [
            'occidente' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_occidente'), 2),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_occidente'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_occidente'), 2),
                'profit' => round((float) (clone $baseQuery)->sum('revenue_occidente') - (clone $baseQuery)->sum('cost_distribution_occidente'), 2),
            ],
            'centro' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_centro'), 2),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_centro'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_centro'), 2),
                'profit' => round((float) (clone $baseQuery)->sum('revenue_centro') - (clone $baseQuery)->sum('cost_distribution_centro'), 2),
            ],
            'oriente' => [
                'kg' => round((float) (clone $baseQuery)->sum('kg_oriente'), 2),
                'revenue' => round((float) (clone $baseQuery)->sum('revenue_oriente'), 2),
                'cost' => round((float) (clone $baseQuery)->sum('cost_distribution_oriente'), 2),
                'profit' => round((float) (clone $baseQuery)->sum('revenue_oriente') - (clone $baseQuery)->sum('cost_distribution_oriente'), 2),
            ],
        ];

        return view('profitability.reports.pdf_report', compact(
            'analyses',
            'totalContainers',
            'totalKg',
            'avgKgPerContainer',
            'totalRevenue',
            'avgRevenuePerKg',
            'totalVariableCost',
            'avgVariableCostPerKg',
            'totalContributionMargin',
            'contributionMarginRatio',
            'totalFixedAssigned',
            'totalProfitBeforeTax',
            'totalTaxes',
            'totalNetProfit',
            'avgNetMargin',
            'feasibleCount',
            'marginalCount',
            'unfeasibleCount',
            'openCount',
            'inReviewCount',
            'closedCount',
            'costStructure',
            'totalCostsSum',
            'regionalComparison'
        ));
    }

    /**
     * Exportación Excel (CSV UTF-8 BOM) de los resultados filtrados con fórmulas e indicadores (Fase 6.5.9).
     */
    public function exportExcelReport(Request $request): StreamedResponse
    {
        $baseQuery = $this->buildFilteredQuery($request);
        $analyses = (clone $baseQuery)->orderBy('arrival_date', 'desc')->get();

        $filename = "informe_rentabilidad_contenedores_" . now()->format('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($analyses, $request) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel

            // Membrete corporativo
            fputcsv($handle, ['SCGI BLANKISOL S.R.L. — INFORME OFICIAL DE RENTABILIDAD Y FACTIBILIDAD DE CONTENEDORES']);
            fputcsv($handle, ['Fecha de Emisión', now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['Generado por', Auth::user()?->name ?? 'Sistema']);
            fputcsv($handle, ['Filtros Aplicados', 
                'Período: ' . ($request->input('period') ?: 'Todos') . ' | ' .
                'Transitaria: ' . ($request->input('transitaria') ?: 'Todas') . ' | ' .
                'Región: ' . ($request->input('region') ?: 'Todas') . ' | ' .
                'Estado: ' . ($request->input('economic_status') ?: 'Todos') . ' | ' .
                'Veredicto: ' . ($request->input('verdict') ?: 'Todos')
            ]);
            fputcsv($handle, []);

            // Resumen Ejecutivo Consolidado
            $totalKg = (float) $analyses->sum('kg_total');
            $totalRevenue = (float) $analyses->sum('revenue_total');
            $totalVarCost = (float) $analyses->sum('cost_variable_total');
            $totalMC = (float) $analyses->sum('contribution_margin');
            $totalFixed = (float) $analyses->sum('fixed_costs_assigned_total');
            $totalProfitBeforeTax = (float) $analyses->sum('profit_before_tax');
            $totalNet = (float) $analyses->sum('net_profit');
            $marginPct = $totalRevenue > 0 ? round(($totalNet / $totalRevenue) * 100, 2) : 0;

            fputcsv($handle, ['RESUMEN EJECUTIVO CONSOLIDADO']);
            fputcsv($handle, ['Total Contenedores', $analyses->count()]);
            fputcsv($handle, ['Carga Total (kg)', number_format($totalKg, 2, '.', '')]);
            fputcsv($handle, ['Ingresos Totales (USD)', number_format($totalRevenue, 2, '.', '')]);
            fputcsv($handle, ['Costos Variables Totales (USD)', number_format($totalVarCost, 2, '.', '')]);
            fputcsv($handle, ['Margen de Contribución Total (USD)', number_format($totalMC, 2, '.', '')]);
            fputcsv($handle, ['Costos Fijos Asignados (USD)', number_format($totalFixed, 2, '.', '')]);
            fputcsv($handle, ['Utilidad Antes de Impuestos (USD)', number_format($totalProfitBeforeTax, 2, '.', '')]);
            fputcsv($handle, ['Utilidad Neta Total (USD)', number_format($totalNet, 2, '.', '')]);
            fputcsv($handle, ['Margen Neto Ponderado (%)', $marginPct . '%']);
            fputcsv($handle, []);

            // Cabecera de Columnas Detalladas
            fputcsv($handle, [
                'ID Contenedor',
                'Fecha Arribo',
                'Transitaria',
                'Estado Económico',
                'Veredicto Viabilidad',
                'Kg Occidente',
                'Kg Centro',
                'Kg Oriente',
                'Kg Total',
                'Ingreso Occidente (USD)',
                'Ingreso Centro (USD)',
                'Ingreso Oriente (USD)',
                'Ingreso Otros (USD)',
                'Ingreso Total (USD)',
                'Costo Puerto Mariel (USD)',
                'Costo Transitaria (USD)',
                'Costo Dist. Occidente (USD)',
                'Costo Dist. Centro (USD)',
                'Costo Dist. Oriente (USD)',
                'Costo Dist. Total (USD)',
                'Comisiones Comerciales (USD)',
                'Costos Variables Totales (USD)',
                'Margen de Contribución (USD)',
                'Margen Contribución (%)',
                'Costos Fijos Asignados (USD)',
                'Utilidad Antes Impuestos (USD)',
                'Impuestos Ventas/Servicios 10% (USD)',
                'Contribución Territorial 1% (USD)',
                'Impuesto sobre Utilidades 35% (USD)',
                'Utilidad Neta (USD)',
                'Margen Neto (%)',
                'Tarifa Mínima Equilibrio (USD/kg)',
            ]);

            foreach ($analyses as $a) {
                fputcsv($handle, [
                    $a->container_id,
                    $a->arrival_date->format('d/m/Y'),
                    $a->transitaria,
                    $a->economic_status,
                    $a->decision_verdict,
                    number_format((float) $a->kg_occidente, 2, '.', ''),
                    number_format((float) $a->kg_centro, 2, '.', ''),
                    number_format((float) $a->kg_oriente, 2, '.', ''),
                    number_format((float) $a->kg_total, 2, '.', ''),
                    number_format((float) $a->revenue_occidente, 2, '.', ''),
                    number_format((float) $a->revenue_centro, 2, '.', ''),
                    number_format((float) $a->revenue_oriente, 2, '.', ''),
                    number_format((float) $a->revenue_other, 2, '.', ''),
                    number_format((float) $a->revenue_total, 2, '.', ''),
                    number_format((float) $a->cost_port_total, 2, '.', ''),
                    number_format((float) $a->cost_transitaria_total, 2, '.', ''),
                    number_format((float) $a->cost_distribution_occidente, 2, '.', ''),
                    number_format((float) $a->cost_distribution_centro, 2, '.', ''),
                    number_format((float) $a->cost_distribution_oriente, 2, '.', ''),
                    number_format((float) $a->cost_distribution_total, 2, '.', ''),
                    number_format((float) $a->cost_commission_total, 2, '.', ''),
                    number_format((float) $a->cost_variable_total, 2, '.', ''),
                    number_format((float) $a->contribution_margin, 2, '.', ''),
                    number_format((float) $a->contribution_margin_pct, 2, '.', ''),
                    number_format((float) $a->fixed_costs_assigned_total, 2, '.', ''),
                    number_format((float) $a->profit_before_tax, 2, '.', ''),
                    number_format((float) $a->taxes_sales_and_services, 2, '.', ''),
                    number_format((float) $a->tax_territorial_contribution, 2, '.', ''),
                    number_format((float) $a->estimated_income_tax, 2, '.', ''),
                    number_format((float) $a->net_profit, 2, '.', ''),
                    number_format((float) $a->net_margin_pct, 2, '.', ''),
                    number_format((float) $a->breakeven_tariff_usd_kg, 4, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Investiga y justifica formalmente una alerta económica con trazabilidad (Fase 6.5.10).
     */
    public function justifyAlert(Request $request, EconomicAlert $alert): RedirectResponse
    {
        $validated = $request->validate([
            'justification' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $alert->update([
            'is_investigated' => true,
            'justification' => $validated['justification'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', "Alerta económica de tipo {$alert->alert_type} investigada y justificada exitosamente.");
    }
}
