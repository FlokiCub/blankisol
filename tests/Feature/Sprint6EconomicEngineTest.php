<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\CommercialAgent;
use App\Models\CommercialLiquidation;
use App\Models\CommercialReconciliation;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAuditLog;
use App\Models\EconomicParameter;
use App\Models\FixedCostItem;
use App\Models\FixedCostMonthly;
use App\Models\FuelAndTariffRate;
use App\Models\User;
use App\Services\Finance\CommercialManagementService;
use App\Services\Finance\ContainerProfitabilityService;
use Database\Seeders\CommercialAgentsSeeder;
use Database\Seeders\EconomicParametersSeeder;
use Database\Seeders\FixedCostItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint6EconomicEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            EconomicParametersSeeder::class,
            FixedCostItemsSeeder::class,
            CommercialAgentsSeeder::class,
        ]);
    }

    public function test_dynamic_fixed_costs_from_items_with_salary_taxes(): void
    {
        // 14% SS y 5% FT calculados sobre nómina base propia ($1,360)
        $costs = FixedCostItem::calculateMonthlyFixedCosts('2026-09');

        $this->assertEquals(1360.00, $costs['salary_bases']['GENERAL']);
        $this->assertEquals(190.40, $costs['social_security_taxes']['GENERAL']);
        $this->assertEquals(68.00, $costs['labor_force_taxes']['GENERAL']);

        // Totales por centro idénticos a COSTOS_FIJOS del Excel
        $this->assertEquals(2640.07, $costs['general_fixed_usd']);
        $this->assertEquals(200.00, $costs['occidente_fixed_usd']);
        $this->assertEquals(200.00, $costs['centro_fixed_usd']);
        $this->assertEquals(200.00, $costs['oriente_fixed_usd']);
        $this->assertEquals(3240.07, $costs['total_fixed_usd']);

        // Sincronización automática con FixedCostMonthly
        $monthly = FixedCostMonthly::getForMonth('2026-09');
        $this->assertEquals(2640.07, $monthly->general_fixed_usd);
        $this->assertEquals(200.00, $monthly->occidente_fixed_usd);
    }

    public function test_automatic_asset_depreciation_hook_in_fixed_costs(): void
    {
        // Camión: (45000 - 5000) / 8 / 12 = 416.67
        // Oficina: (1500 - 0) / 5 / 12 = 25.00
        // Total = 441.67
        $deprec = Asset::calculateTotalMonthlyDepreciation('2026-09');
        $this->assertEquals(441.67, $deprec);

        $entries = Asset::generateMonthlyDepreciationEntries('2026-09');
        $this->assertCount(2, $entries);
        $this->assertDatabaseHas('asset_depreciation_entries', [
            'year_month' => '2026-09',
            'monthly_depreciation' => 416.67,
        ]);
    }

    public function test_unabsorbed_fixed_costs_calculation(): void
    {
        $service = app(ContainerProfitabilityService::class);

        // Crear contenedor 01 con datos benchmark en 2026-09
        $benchmarkData = [
            'container_id' => 'CONT-UNABSORBED-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'kg_occidente' => 5200,
            'kg_centro' => 2600,
            'kg_oriente' => 2300,
            'other_revenue' => 0,
            'other_direct_costs' => 0,
            'total_month_kg' => 19500,
            'total_month_occ_kg' => 9800,
            'total_month_cen_kg' => 4700,
            'total_month_ori_kg' => 5000,
        ];

        $calc = $service->calculate($benchmarkData);

        ContainerProfitabilityAnalysis::create([
            'container_id' => 'CONT-UNABSORBED-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'status' => 'Cerrado',
            'economic_status' => 'abierto',
            'kg_occidente' => 5200,
            'kg_centro' => 2600,
            'kg_oriente' => 2300,
            'kg_total' => 10100,
            'revenue_occidente' => 4160,
            'revenue_centro' => 2080,
            'revenue_oriente' => 1840,
            'revenue_total' => 8080,
            'cost_port_total' => $calc['costs_breakdown']['port']['total'],
            'cost_transitaria_total' => $calc['costs_breakdown']['transitaria']['total'],
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
            'applied_rates_snapshot' => [
                'fixed_costs_assigned' => [
                    'general' => $calc['fixed_costs_assigned']['general'],
                    'occidente' => $calc['fixed_costs_assigned']['occidente'],
                    'centro' => $calc['fixed_costs_assigned']['centro'],
                    'oriente' => $calc['fixed_costs_assigned']['oriente'],
                ],
            ],
            'applied_parameters' => $calc,
        ]);

        $unabsorbed = $service->calculateUnabsorbedFixedCosts('2026-09');

        $this->assertEquals(3240.07, $unabsorbed['total_month_fixed']);
        $this->assertEquals(1676.18, $unabsorbed['total_assigned_fixed']);
        $this->assertEquals(1563.89, $unabsorbed['unabsorbed_fixed']);
        $this->assertEquals(51.73, $unabsorbed['absorption_rate_pct']);
    }

    public function test_commercial_agents_liquidations_and_reconciliation(): void
    {
        $commercialService = app(CommercialManagementService::class);

        $agentLaHabana = CommercialAgent::where('territory', 'La Habana')->first();
        $this->assertNotNull($agentLaHabana);

        // 1. Cálculo de comisión % del ingreso: 3% de $3,334.74 = $100.04
        $commPct = $commercialService->calculateCommission($agentLaHabana, 3334.74);
        $this->assertEquals(100.04, $commPct);

        // 2. Liquidación masiva del mes para los 21 gestores
        // Asignamos base solo al gestor de La Habana
        $liquidations = $commercialService->liquidateMonth('2026-09', [
            $agentLaHabana->agent_number => 3334.74,
        ]);

        $this->assertCount(21, $liquidations);

        // El gestor de La Habana recibe $50 fijo + $100.04 comisión = $150.04
        $liqHabana = CommercialLiquidation::where('commercial_agent_id', $agentLaHabana->id)
            ->where('year_month', '2026-09')
            ->first();
        $this->assertEquals(50.00, $liqHabana->monthly_fixed_fee);
        $this->assertEquals(100.04, $liqHabana->calculated_commission);
        $this->assertEquals(150.04, $liqHabana->total_payable);

        // 3. Conciliación comercial contra operaciones
        $reconciliation = $commercialService->reconcileMonth('2026-09');
        $this->assertEquals(100.04, $reconciliation->agents_total_amount);
        $this->assertEquals(0.00, $reconciliation->operations_total_amount);
        $this->assertEquals(100.04, $reconciliation->difference);
        $this->assertEquals('con_diferencia', $reconciliation->status);
    }

    public function test_economic_analysis_close_with_snapshot_and_audit(): void
    {
        $service = app(ContainerProfitabilityService::class);

        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        $analysis = ContainerProfitabilityAnalysis::create([
            'container_id' => 'CONT-CLOSING-AUDIT-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'status' => 'Cerrado',
            'economic_status' => 'abierto',
            'fixed_costs_assigned_total' => 1676.18,
            'applied_parameters' => [
                'fixed_costs_assigned' => [
                    'general' => 1367.42,
                    'occidente' => 106.12,
                    'centro' => 110.64,
                    'oriente' => 92.00,
                ],
            ],
        ]);

        // Cerrar análisis económico
        $closed = $service->closeEconomicAnalysis($analysis, $jefe, 'Cierre económico mensual validado');

        $this->assertEquals('cerrado', $closed->economic_status);
        $this->assertEquals($jefe->id, $closed->closed_by);
        $this->assertNotNull($closed->closed_at);
        $this->assertIsArray($closed->applied_rates_snapshot);
        $this->assertArrayHasKey('rates', $closed->applied_rates_snapshot);
        $this->assertArrayHasKey('parameters', $closed->applied_rates_snapshot);
        $this->assertArrayHasKey('fixed_costs_detail', $closed->applied_rates_snapshot);

        // Registro de auditoría
        $this->assertDatabaseHas('economic_audit_logs', [
            'action' => 'close_analysis',
            'entity_id' => (string) $analysis->id,
            'user_id' => $jefe->id,
        ]);

        // Reapertura autorizada
        $reopened = $service->reopenEconomicAnalysis($closed, $jefe, 'Ajuste de flete transitaria por recalificación');
        $this->assertEquals('en_revision', $reopened->economic_status);
        $this->assertEquals('Ajuste de flete transitaria por recalificación', $reopened->reopened_reason);

        $this->assertDatabaseHas('economic_audit_logs', [
            'action' => 'reopen_analysis',
            'entity_id' => (string) $analysis->id,
            'user_id' => $jefe->id,
        ]);
    }

    public function test_margin_of_safety_formula_and_excel_benchmark_container_01(): void
    {
        $service = app(ContainerProfitabilityService::class);

        $benchmark = [
            'container_id' => 'CONT-BENCHMARK-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'status' => 'Cerrado',
            'kg_occidente' => 5200,
            'kg_centro' => 2600,
            'kg_oriente' => 2300,
            'trips_port' => 1.0,
            'liters_port' => 62.0,
            'diet_port' => 10.0,
            'labor_transitaria' => 11.0,
            'service_transitaria' => 0.0,
            'storage_transitaria' => 0.0,
            'liters_dist_occ' => 240.0,
            'liters_tras_cen' => 250.0,
            'liters_dist_cen' => 150.0,
            'labor_shifts_cen' => 4.0,
            'liters_tras_ori' => 450.0,
            'liters_dist_ori' => 180.0,
            'labor_shifts_ori' => 3.0,
            'other_revenue' => 0.0,
            'other_direct_costs' => 0.0,
            'total_month_kg' => 19500.0,
            'total_month_occ_kg' => 9800.0,
            'total_month_cen_kg' => 4700.0,
            'total_month_ori_kg' => 5000.0,
        ];

        $res = $service->calculate($benchmark);

        // 1. Margen de Seguridad: (kg_real - kg_equilibrio) / kg_real * 100
        // Real: 10,100 kg | Breakeven: ~5,464.54 kg (tolerancia ≤ $0.50)
        // Margen de seguridad: (10,100 - 5464.38) / 10100 * 100 = 45.90%
        $this->assertEqualsWithDelta(5464.54, $res['breakeven']['breakeven_kg'], 0.50);
        $this->assertEqualsWithDelta(4635.46, $res['breakeven']['safety_margin_kg'], 0.50);
        $this->assertEquals(45.90, $res['breakeven']['safety_margin_pct']);

        // 2. Validación de todas las cifras del Excel CALCULO con tolerancia ≤ $0.50
        $this->assertEquals(8080.00, $res['revenues']['total']);
        $this->assertEquals(165.60, $res['costs_breakdown']['port']['total']);
        $this->assertEquals(165.00, $res['costs_breakdown']['transitaria']['total']);
        $this->assertEquals(2645.00, $res['costs_breakdown']['distribution']['total']);
        $this->assertEquals(242.40, $res['costs_breakdown']['commissions']['total']);
        $this->assertEquals(121.20, $res['costs_breakdown']['provisions_and_bank']['total']);
        $this->assertEquals(3339.20, $res['costs_breakdown']['total_variable']);
        $this->assertEquals(4740.80, $res['contribution_margin']['amount']);
        $this->assertEquals(58.67, $res['contribution_margin']['pct']);
        $this->assertEquals(1676.18, $res['fixed_costs_assigned']['total']);
        $this->assertEquals(888.80, $res['taxes']['total_on_revenue']);
        $this->assertEquals(2175.82, $res['net_results']['profit_before_tax']);
        $this->assertEquals(1414.28, $res['net_results']['net_profit']);
        $this->assertEquals(17.50, $res['net_results']['net_margin_pct']);
        $this->assertEquals('FACTIBLE', $res['decision']['verdict']);
    }
}
