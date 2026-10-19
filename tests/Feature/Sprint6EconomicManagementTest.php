<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetDepreciationEntry;
use App\Models\CommercialAgent;
use App\Models\CommercialLiquidation;
use App\Models\CommercialReconciliation;
use App\Models\EconomicAuditLog;
use App\Models\EconomicParameter;
use App\Models\FixedCostItem;
use App\Models\FixedCostMonthly;
use App\Models\FixedCostMonthlyEntry;
use App\Models\FuelAndTariffRate;
use App\Models\MonthlyClosing;
use App\Models\User;
use Database\Seeders\CommercialAgentsSeeder;
use Database\Seeders\EconomicParametersSeeder;
use Database\Seeders\FixedCostItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint6EconomicManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $jefeEconomico;
    protected User $operador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            EconomicParametersSeeder::class,
            FixedCostItemsSeeder::class,
            CommercialAgentsSeeder::class,
        ]);

        $this->jefeEconomico = User::factory()->create([
            'role' => 'jefe_economico',
            'name' => 'Lic. Roberto Gómez',
        ]);

        $this->operador = User::factory()->create([
            'role' => 'logistica_chofer',
            'name' => 'Chofer Logístico',
        ]);
    }

    public function test_management_routes_access_control(): void
    {
        // Unauthenticated -> redirect to login
        $this->get(route('profitability.management.fixed-costs'))->assertRedirect(route('login'));

        // Unauthorized operator -> 403 Forbidden
        $this->actingAs($this->operador)
            ->get(route('profitability.management.fixed-costs'))
            ->assertForbidden();

        // Jefe Económico -> 200 OK
        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.management.fixed-costs'))
            ->assertOk()
            ->assertSee('Catálogo de Costos Fijos', false);

        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.management.assets'))
            ->assertOk()
            ->assertSee('Catálogo de Activos Fijos', false);

        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.management.commercial'))
            ->assertOk()
            ->assertSee('Red Comercial', false);

        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.management.parameters'))
            ->assertOk()
            ->assertSee('Parámetros del Sistema', false);

        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.management.closings'))
            ->assertOk()
            ->assertSee('Checklist Oficial de Cierre Mensual', false);
    }

    public function test_fixed_costs_crud_and_monthly_entries(): void
    {
        $this->actingAs($this->jefeEconomico);

        // 1. Create new FixedCostItem
        $res = $this->post(route('profitability.management.fixed-costs.items.store'), [
            'code' => 'CF_TEST',
            'name' => 'Servicio de Internet Satelital',
            'cost_center' => 'GENERAL',
            'classification' => 'Comunicaciones',
            'is_salary' => false,
            'monthly_base_value' => 150.00,
            'description' => 'Enlace Starlink backup',
        ]);

        $res->assertSessionHasNoErrors();
        $this->assertDatabaseHas('fixed_cost_items', [
            'code' => 'CF_TEST',
            'monthly_base_value' => 150.00,
        ]);

        $item = FixedCostItem::where('code', 'CF_TEST')->first();

        // 2. Update FixedCostItem
        $this->put(route('profitability.management.fixed-costs.items.update', $item), [
            'name' => 'Servicio Starlink Actualizado',
            'cost_center' => 'GENERAL',
            'classification' => 'Comunicaciones',
            'is_salary' => false,
            'monthly_base_value' => 180.00,
            'is_active' => true,
            'description' => 'Aumento de ancho de banda',
        ])->assertSessionHasNoErrors();

        $this->assertEquals(180.00, $item->fresh()->monthly_base_value);

        // 3. Save Monthly Real Entries
        $this->post(route('profitability.management.fixed-costs.monthly.save'), [
            'year_month' => '2026-10',
            'entries' => [
                $item->id => [
                    'amount' => 175.50,
                    'observations' => 'Descuento por facturación anual',
                ],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fixed_cost_monthly_entries', [
            'fixed_cost_item_id' => $item->id,
            'year_month' => '2026-10',
            'actual_amount' => 175.50,
        ]);

        $this->assertDatabaseHas('fixed_costs_monthly', [
            'year_month' => '2026-10',
        ]);
    }

    public function test_cannot_modify_fixed_costs_in_closed_period(): void
    {
        $this->actingAs($this->jefeEconomico);

        MonthlyClosing::create([
            'year_month' => '2026-09',
            'status' => 'cerrado',
            'closed_by' => $this->jefeEconomico->id,
            'closed_at' => now(),
        ]);

        $item = FixedCostItem::first();

        $this->post(route('profitability.management.fixed-costs.monthly.save'), [
            'year_month' => '2026-09',
            'entries' => [
                $item->id => ['amount' => 999.00],
            ],
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('fixed_cost_monthly_entries', [
            'fixed_cost_item_id' => $item->id,
            'year_month' => '2026-09',
            'actual_amount' => 999.00,
        ]);
    }

    public function test_assets_crud_and_depreciation_generation(): void
    {
        $this->actingAs($this->jefeEconomico);

        // 1. Create asset
        $this->post(route('profitability.management.assets.store'), [
            'asset_number' => 'ACT-NEW-01',
            'description' => 'Montacargas Toyota 3.5T',
            'asset_type' => 'EQUIPO',
            'registration_date' => '2026-01-01',
            'acquisition_value' => 24000.00,
            'residual_value' => 2400.00,
            'useful_life_years' => 10,
            'monthly_maintenance' => 100.00,
            'annual_insurance' => 500.00,
        ])->assertSessionHasNoErrors();

        $asset = Asset::where('asset_number', 'ACT-NEW-01')->first();
        $this->assertNotNull($asset);

        // 2. Check theoretical depreciation: (24000 - 2400) / (10 * 12) = 21600 / 120 = 180.00
        $this->assertEquals(180.00, $asset->getTheoreticalMonthlyDepreciation());

        // 3. Generate monthly depreciation
        $this->post(route('profitability.management.assets.depreciation'), [
            'year_month' => '2026-10',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asset_depreciation_entries', [
            'asset_id' => $asset->id,
            'year_month' => '2026-10',
            'monthly_depreciation' => 180.00,
        ]);
    }

    public function test_commercial_network_liquidation_and_reconciliation(): void
    {
        $this->actingAs($this->jefeEconomico);

        $agent = CommercialAgent::first();
        $this->assertNotNull($agent);

        // 1. Update Agent
        $this->put(route('profitability.management.commercial.update', $agent), [
            'name' => $agent->name . ' Editado',
            'group' => $agent->group,
            'territory' => $agent->territory,
            'region' => $agent->region,
            'monthly_fixed_fee' => 120.00,
            'commission_mode' => '% DEL INGRESO',
            'is_active' => true,
        ])->assertSessionHasNoErrors();

        // 2. Liquidate commercial month
        $this->post(route('profitability.management.commercial.liquidate'), [
            'year_month' => '2026-10',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('commercial_liquidations', [
            'commercial_agent_id' => $agent->id,
            'year_month' => '2026-10',
        ]);

        // 3. Reconcile commercial month
        $this->post(route('profitability.management.commercial.reconcile'), [
            'year_month' => '2026-10',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('commercial_reconciliations', [
            'year_month' => '2026-10',
        ]);
    }

    public function test_parameters_update_and_rate_versioning(): void
    {
        $this->actingAs($this->jefeEconomico);

        // 1. Update Economic Parameter
        $this->post(route('profitability.management.parameters.update'), [
            'parameters' => [
                'Std_Viajes_Puerto' => '2',
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('economic_parameters', [
            'code' => 'Std_Viajes_Puerto',
            'value' => '2',
        ]);

        // Verify audit log created
        $this->assertDatabaseHas('economic_audit_logs', [
            'action' => 'update_economic_parameter',
            'user_id' => $this->jefeEconomico->id,
        ]);

        // 2. Publish New Rate Card Version
        $this->post(route('profitability.management.rates.store'), [
            'effective_from' => '2026-11-01',
            'tariff_occidente_usd_kg' => 0.85,
            'tariff_centro_usd_kg' => 0.85,
            'tariff_oriente_usd_kg' => 0.85,
            'diesel_price_usd_l' => 1.25,
            'port_appointment_usd' => 130.00,
            'driver_diet_usd' => 55.00,
            'labor_shift_transitaria_usd' => 125.00,
            'labor_shift_transitory_center_usd' => 105.00,
            'transitaria_service_enci_usd' => 460.00,
            'transitaria_service_av_usd' => 510.00,
            'commercial_commission_pct' => 5.50,
            'commercial_commission_usd_kg' => 0.045,
            'vehicle_wear_usd_km' => 0.13,
            'support_document_note' => 'Resolución de Tarifas Noviembre 2026',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('fuel_and_tariff_rates', [
            'effective_from' => '2026-11-01',
            'diesel_price_usd_l' => 1.25,
        ]);

        // Historical rates still exist
        $this->assertTrue(FuelAndTariffRate::count() >= 2);
    }

    public function test_monthly_closing_execution_and_authorized_reopening(): void
    {
        $this->actingAs($this->jefeEconomico);

        // 1. Execute Monthly Closing
        $this->post(route('profitability.management.closings.execute'), [
            'year_month' => '2026-10',
            'notes' => 'Cierre ordinario de fin de mes',
            'checklist' => ['1' => true, '2' => true],
        ])->assertSessionHasNoErrors();

        $closing = MonthlyClosing::where('year_month', '2026-10')->first();
        $this->assertNotNull($closing);
        $this->assertEquals('cerrado', $closing->status);
        $this->assertTrue($closing->isClosed());

        // 2. Reopen without enough justification -> fails validation
        $this->post(route('profitability.management.closings.reopen', $closing), [
            'reason' => 'corta',
        ])->assertSessionHasErrors('reason');

        // 3. Reopen with valid justification -> success
        $this->post(route('profitability.management.closings.reopen', $closing), [
            'reason' => 'Ajuste de factura ENCI autorizada por Dirección General',
        ])->assertSessionHasNoErrors();

        $closing->refresh();
        $this->assertEquals('reabierto', $closing->status);
        $this->assertFalse($closing->isClosed());
        $this->assertEquals('Ajuste de factura ENCI autorizada por Dirección General', $closing->reopen_reason);
        $this->assertEquals($this->jefeEconomico->id, $closing->reopened_by);

        // Verify audit log
        $this->assertDatabaseHas('economic_audit_logs', [
            'action' => 'monthly_closing_reopened',
            'user_id' => $this->jefeEconomico->id,
        ]);
    }

    public function test_monthly_summary_resumen_mes_endpoint(): void
    {
        // Operator is forbidden
        $this->actingAs($this->operador)
            ->get(route('profitability.monthly-summary'))
            ->assertForbidden();

        // Jefe Económico has access
        $this->actingAs($this->jefeEconomico)
            ->get(route('profitability.monthly-summary'))
            ->assertOk()
            ->assertSee('Resumen Económico Mensual Consolidado', false)
            ->assertSee('Facturación Bruta (YTD)', false);
    }
}
