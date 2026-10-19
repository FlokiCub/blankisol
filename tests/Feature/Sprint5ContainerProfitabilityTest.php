<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicParameter;
use App\Models\Equipment;
use App\Models\FixedCostMonthly;
use App\Models\FuelAndTariffRate;
use App\Models\RegionalDispatchRoute;
use App\Models\RegionalWarehouse;
use App\Models\User;
use App\Services\Finance\ContainerProfitabilityService;
use Database\Seeders\EconomicParametersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint5ContainerProfitabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EconomicParametersSeeder::class);
    }

    /**
     * Valida que los parámetros económicos, tarifas CUPET y costos fijos se persistan correctamente.
     */
    public function test_economic_parameters_and_seeders_exist_in_database(): void
    {
        $this->assertDatabaseHas('economic_parameters', ['code' => 'Std_Viajes_Puerto', 'value' => '1']);
        $this->assertDatabaseHas('economic_parameters', ['code' => 'Litros_Puerto_Viaje', 'value' => '60']);
        $this->assertDatabaseHas('economic_parameters', ['code' => 'Imp_Ventas_Serv', 'value' => '0.10']);
        $this->assertDatabaseHas('economic_parameters', ['code' => 'Imp_Utilidades', 'value' => '0.35']);

        $rateCard = FuelAndTariffRate::getRateForDate('2026-09-08');
        $this->assertNotNull($rateCard);
        $this->assertEquals(2.0000, $rateCard->diesel_price_usd_l);
        $this->assertEquals(0.8000, $rateCard->tariff_occidente_usd_kg);

        $fixedCost = FixedCostMonthly::getForMonth('2026-09');
        $this->assertEquals(2640.07, $fixedCost->general_fixed_usd);
    }


    /**
     * Valida el benchmark oficial del Excel (Contenedor de Prueba 01):
     * Carga: Occ 5200 kg, Cen 2600 kg, Ori 2300 kg (Total 10,100 kg), Arribo Sep 2026, Transitaria ENCI.
     */
    public function test_container_profitability_calculation_matches_excel_benchmark_container_01(): void
    {
        $service = app(ContainerProfitabilityService::class);

        $input = [
            'container_id' => 'CONT-EJEMPLO-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'kg_occidente' => 5200,
            'kg_centro' => 2600,
            'kg_oriente' => 2300,
            'trips_port' => 1.0,
            'liters_port' => 62.0, // Benchmark Excel: 62 L aplicados
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

        $res = $service->calculate($input);

        // 1. Ingresos
        $this->assertEquals(10100.0, $res['weights']['kg_total']);
        $this->assertEquals(4160.0, $res['revenues']['occidente']);
        $this->assertEquals(2080.0, $res['revenues']['centro']);
        $this->assertEquals(1840.0, $res['revenues']['oriente']);
        $this->assertEquals(8080.0, $res['revenues']['total']);

        // 2. Costos de las 4 Etapas
        // Etapa 1: Puerto = $10 cita + $124 combustible (62*2) + $10 dieta + $21.60 desgaste = $165.60
        $this->assertEquals(165.60, $res['costs_breakdown']['port']['total']);

        // Etapa 2: Transitaria = 11 jornales * 15 = $165.00
        $this->assertEquals(165.00, $res['costs_breakdown']['transitaria']['total']);

        // Etapa 3: Distribución = Occ 480 + Cen 860 + Ori 1305 = $2,645.00
        $this->assertEquals(480.00, $res['costs_breakdown']['distribution']['occidente']['total']);
        $this->assertEquals(860.00, $res['costs_breakdown']['distribution']['centro']['total']);
        $this->assertEquals(1305.00, $res['costs_breakdown']['distribution']['oriente']['total']);
        $this->assertEquals(2645.00, $res['costs_breakdown']['distribution']['total']);

        // Etapa 4: Comercialización y Deducciones = 242.40 + 40.40 + 80.80 = $363.60
        $this->assertEquals(242.40, $res['costs_breakdown']['commissions']['total']);
        $this->assertEquals(40.40, $res['costs_breakdown']['provisions_and_bank']['claims']);
        $this->assertEquals(80.80, $res['costs_breakdown']['provisions_and_bank']['bank']);

        // Total Costos Variables = $3,339.20
        $this->assertEquals(3339.20, $res['costs_breakdown']['total_variable']);

        // 3. Margen de Contribución = 8080 - 3339.20 = $4,740.80 (58.7%)
        $this->assertEquals(4740.80, $res['contribution_margin']['amount']);
        $this->assertEquals(58.67, $res['contribution_margin']['pct']);

        // 4. Costos Fijos Asignados = 1,367.42 + 106.12 + 110.64 + 92.00 = $1,676.18
        $this->assertEquals(1676.18, $res['fixed_costs_assigned']['total']);

        // 5. Impuestos sobre ingresos = $808.00 ventas + $80.80 territorial = $888.80
        $this->assertEquals(808.00, $res['taxes']['sales_and_services']);
        $this->assertEquals(80.80, $res['taxes']['territorial_contribution']);

        // 6. Utilidad antes de impuestos = 4740.80 - 1676.18 - 888.80 = $2,175.82
        $this->assertEquals(2175.82, $res['net_results']['profit_before_tax']);

        // 7. Impuesto sobre utilidades (35%) = $761.54
        $this->assertEquals(761.54, $res['taxes']['estimated_income_tax']);

        // 8. Utilidad Neta Estimada = $1,414.28 (17.5% margen)
        $this->assertEquals(1414.28, $res['net_results']['net_profit']);
        $this->assertEquals(17.50, $res['net_results']['net_margin_pct']);

        // 9. Veredicto
        $this->assertEquals('FACTIBLE', $res['decision']['verdict']);
        $this->assertStringContainsString('Altamente Factible', $res['decision']['summary']);

        // 10. Cuadre Regional Inmutable
        $this->assertEquals(0.00, $res['regional_breakdown']['reconciliation_diff']);
    }

    /**
     * Valida que un contenedor con carga insuficiente hacia Oriente se detecte como déficit y no factible.
     */
    public function test_unfeasible_underloaded_container_yields_deficit_and_alarming_verdict(): void
    {
        $service = app(ContainerProfitabilityService::class);

        // Envío de solo 600 kg hacia Oriente (el viaje de 450 L traslado + 180 L reparto cuesta más de $1,200 solo en diésel)
        $input = [
            'container_id' => 'CONT-DEFICIT-01',
            'arrival_date' => '2026-09-10',
            'transitaria' => 'ENCI',
            'kg_occidente' => 0,
            'kg_centro' => 0,
            'kg_oriente' => 600,
        ];

        $res = $service->calculate($input);

        $this->assertEquals(600.0, $res['weights']['kg_total']);
        $this->assertLessThan(0, $res['net_results']['profit_before_tax']);
        $this->assertEquals('NO_FACTIBLE', $res['decision']['verdict']);
        $this->assertStringContainsString('🔴 Contenedor Antieconómico', $res['decision']['summary']);
        $this->assertStringContainsString('kg adicionales', $res['decision']['summary']);
    }

    /**
     * Valida la evaluación de viabilidad de una Hoja de Ruta antes del despacho del convoy.
     */
    public function test_route_viability_evaluation_detects_underloaded_convoy(): void
    {
        $origin = RegionalWarehouse::factory()->create([
            'name' => 'Terminal Central Mariel',
            'province' => 'Artemisa',
        ]);

        $destination = RegionalWarehouse::factory()->create([
            'name' => 'HUB Santiago de Cuba',
            'province' => 'Santiago de Cuba',
        ]);

        $route = RegionalDispatchRoute::create([
            'route_code' => 'HR-2026-TEST',
            'origin_warehouse_id' => $origin->id,
            'destination_warehouse_id' => $destination->id,
            'driver_name' => 'Carlos Pérez',
            'truck_license_plate' => 'B987654',
            'status' => 'draft',
        ]);

        $service = app(ContainerProfitabilityService::class);
        $viability = $service->evaluateRouteViability($route);


        // Ruta vacía sin equipos
        $this->assertEquals(0.0, $viability['total_kg']);
        $this->assertEquals('NO_FACTIBLE', $viability['verdict']);
        $this->assertStringContainsString('🔴 Convoy Antieconómico', $viability['summary']);
        $this->assertGreaterThan(0, $viability['min_kg_required']);
    }

    /**
     * Valida el control de acceso RBAC a las rutas de rentabilidad.
     */
    public function test_profitability_controller_access_and_rbac(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'is_active' => true,
        ]);

        $auditor = User::factory()->create([
            'role' => UserRole::AUDITOR,
            'is_active' => true,
        ]);

        $driver = User::factory()->create([
            'role' => UserRole::LOGISTICA_CHOFER,
            'is_active' => true,
        ]);

        // 1. Director puede acceder al index y al simulador
        $response = $this->actingAs($director)->get(route('profitability.index'));
        $response->assertStatus(200);

        $responseSim = $this->actingAs($director)->get(route('profitability.simulator'));
        $responseSim->assertStatus(200);

        // 2. Auditor puede acceder
        $responseAuditor = $this->actingAs($auditor)->get(route('profitability.index'));
        $responseAuditor->assertStatus(200);

        // 3. Jefe Económico puede acceder
        $jefeEconomico = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);
        $responseJefe = $this->actingAs($jefeEconomico)->get(route('profitability.index'));
        $responseJefe->assertStatus(200);

        // 4. Chofer no tiene permisos (403)
        $responseDriver = $this->actingAs($driver)->get(route('profitability.index'));
        $responseDriver->assertStatus(403);
    }

    /**
     * Valida el endpoint JSON para el simulador interactivo en tiempo real.
     */
    public function test_live_simulation_endpoint_returns_dynamic_json(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'is_active' => true,
        ]);

        $payload = [
            'kg_occidente' => 5000,
            'kg_centro' => 2500,
            'kg_oriente' => 2500,
            'custom_diesel_price' => 2.50,
            'custom_tariff_rate' => 0.85,
        ];

        $response = $this->actingAs($director)->postJson(route('profitability.simulate-live'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'revenues',
                'costs_breakdown',
                'contribution_margin',
                'net_results',
                'breakeven',
                'decision',
            ],
        ]);
    }
}
