<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\AssetDepreciationEntry;
use App\Models\CommercialAgent;
use App\Models\CommercialLiquidation;
use App\Models\CommercialReconciliation;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAlert;
use App\Models\EconomicAuditLog;
use App\Models\FixedCostItem;
use App\Models\FixedCostMonthlyEntry;
use App\Models\MonthlyClosing;
use App\Models\SimulationScenario;
use App\Models\User;
use Database\Seeders\CommercialAgentsSeeder;
use Database\Seeders\EconomicParametersSeeder;
use Database\Seeders\FixedCostItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint6EconomicDataModelTest extends TestCase
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

    public function test_jefe_economico_role_and_permissions(): void
    {
        $this->assertEquals('jefe_economico', UserRole::JEFE_ECONOMICO->value);
        $this->assertStringContainsString('Jefe Económico', UserRole::JEFE_ECONOMICO->label());
        $this->assertNotEmpty(UserRole::JEFE_ECONOMICO->description());

        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        $this->assertTrue($jefe->isJefeEconomico());
        $this->assertTrue($jefe->canAccessWarehouse(null));
        $this->assertTrue($jefe->hasRole(UserRole::JEFE_ECONOMICO));

        $response = $this->actingAs($jefe)->get(route('profitability.index'));
        $response->assertStatus(200);
    }

    public function test_fixed_cost_items_and_entries_relations(): void
    {
        $this->assertEquals(30, FixedCostItem::count());

        $cf01 = FixedCostItem::where('code', 'CF01')->first();
        $this->assertNotNull($cf01);
        $this->assertTrue($cf01->is_salary);
        $this->assertEquals('GENERAL', $cf01->cost_center);

        $entry = FixedCostMonthlyEntry::create([
            'fixed_cost_item_id' => $cf01->id,
            'year_month' => '2026-09',
            'actual_amount' => 65.00,
            'observations' => 'Horas extras chofer',
        ]);

        $this->assertCount(1, $cf01->monthlyEntries);
        $this->assertEquals($cf01->id, $entry->fixedCostItem->id);
    }

    public function test_assets_and_depreciation_relations_and_calculation(): void
    {
        $this->assertEquals(2, Asset::count());

        $truck = Asset::where('asset_number', 'VEH-PUERTO-01')->first();
        $this->assertNotNull($truck);
        $this->assertEquals(45000.00, $truck->acquisition_value);
        $this->assertEquals(5000.00, $truck->residual_value);
        $this->assertEquals(8, $truck->useful_life_years);

        // Theoretical monthly depreciation: (45000 - 5000) / 8 / 12 = 416.67
        $this->assertEquals(416.67, $truck->getTheoreticalMonthlyDepreciation());

        $depEntry = AssetDepreciationEntry::create([
            'asset_id' => $truck->id,
            'year_month' => '2026-09',
            'monthly_depreciation' => 416.67,
            'accumulated_depreciation' => 416.67 * 9,
        ]);

        $this->assertCount(1, $truck->depreciationEntries);
        $this->assertEquals($truck->id, $depEntry->asset->id);
    }

    public function test_commercial_agents_and_liquidations(): void
    {
        $this->assertEquals(21, CommercialAgent::count());

        $agent08 = CommercialAgent::where('territory', 'La Habana')->first();
        $this->assertNotNull($agent08);
        $this->assertEquals('Occidente', $agent08->region);
        $this->assertEquals(50.00, $agent08->monthly_fixed_fee);

        $liq = CommercialLiquidation::create([
            'commercial_agent_id' => $agent08->id,
            'year_month' => '2026-09',
            'monthly_fixed_fee' => 50.00,
            'variable_base' => 3334.74,
            'variable_rate' => 0.03,
            'calculated_commission' => 100.04,
            'total_payable' => 150.04,
            'status' => 'liquidado',
        ]);

        $this->assertCount(1, $agent08->liquidations);
        $this->assertEquals($agent08->id, $liq->commercialAgent->id);
    }

    public function test_monthly_closings_and_audit_and_alerts(): void
    {
        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        $closing = MonthlyClosing::create([
            'year_month' => '2026-09',
            'status' => 'cerrado',
            'checklist' => ['step_1' => true, 'step_2' => true],
            'closed_by' => $jefe->id,
            'closed_at' => now(),
        ]);

        $this->assertTrue($closing->isClosed());
        $this->assertEquals($jefe->id, $closing->closer->id);

        $analysis = ContainerProfitabilityAnalysis::create([
            'container_id' => 'TEST-CLOSING-01',
            'arrival_date' => '2026-09-08',
            'economic_status' => 'cerrado',
            'closed_by' => $jefe->id,
            'closed_at' => now(),
            'applied_rates_snapshot' => ['diesel_price_usd_l' => 2.0],
        ]);

        $this->assertEquals($jefe->id, $analysis->closer->id);

        $alert = EconomicAlert::create([
            'container_profitability_analysis_id' => $analysis->id,
            'alert_type' => 'combustible_desvio',
            'severity' => 'alta',
            'description' => 'Desvío de consumo en Mariel superior al 10%',
            'is_investigated' => true,
            'justification' => 'Demora en cola de pesaje con motor en marcha',
            'resolved_by' => $jefe->id,
            'resolved_at' => now(),
        ]);

        $this->assertCount(1, $analysis->alerts);
        $this->assertEquals($jefe->id, $alert->resolver->id);

        $audit = EconomicAuditLog::create([
            'entity_type' => 'monthly_closings',
            'entity_id' => (string) $closing->id,
            'action' => 'cerrar_mes',
            'user_id' => $jefe->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals($jefe->id, $audit->user->id);
    }

    public function test_regional_vehicle_wear_calculation_when_using_own_fleet(): void
    {
        $service = app(\App\Services\Finance\ContainerProfitabilityService::class);

        $input = [
            'container_id' => 'CONT-OWN-FLEET-01',
            'arrival_date' => '2026-09-08',
            'transitaria' => 'ENCI',
            'kg_occidente' => 5000,
            'kg_centro' => 5000,
            'kg_oriente' => 5000,
            'apply_regional_vehicle_wear' => true,
        ];

        $res = $service->calculate($input);

        // Occidente: 1 trip * 150 km * 0.12 USD/km = 18.00 USD
        $this->assertEquals(18.00, $res['costs_breakdown']['distribution']['occidente']['vehicle_wear_cost']);

        // Centro: 1 trip * (300 tras + 200 dist) km * 0.12 USD/km = 60.00 USD
        $this->assertEquals(60.00, $res['costs_breakdown']['distribution']['centro']['vehicle_wear_cost']);

        // Oriente: 1 trip * (800 tras + 250 dist) km * 0.12 USD/km = 126.00 USD
        $this->assertEquals(126.00, $res['costs_breakdown']['distribution']['oriente']['vehicle_wear_cost']);

        // Total regional vehicle wear = 18 + 60 + 126 = 204.00 USD
        $this->assertEquals(204.00, $res['costs_breakdown']['distribution']['total_vehicle_wear']);
    }
}

