<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Enums\UserRole;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\Equipment;
use App\Models\RegionalWarehouse;
use App\Models\User;
use App\Services\Finance\ContainerOperationalIntegrationService;
use Database\Seeders\CommercialAgentsSeeder;
use Database\Seeders\EconomicParametersSeeder;
use Database\Seeders\FixedCostItemsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint6OperationalIntegrationTest extends TestCase
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

    public function test_operational_containers_endpoint_and_rbac(): void
    {
        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        // Crear equipos operativos agrupados en 2 contenedores
        Equipment::create([
            'tracking_pin' => 'PIN-CONT1-01',
            'vin_serial' => 'VIN-CONT1-01',
            'solve_cargo_tracking_id' => 'MSCU9876543',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-05 10:00:00',
            'client_data' => ['transitaria' => 'ENCI', 'province' => 'La Habana'],
            'technical_specs' => ['weight_kg' => 120.0],
        ]);

        Equipment::create([
            'tracking_pin' => 'PIN-CONT1-02',
            'vin_serial' => 'VIN-CONT1-02',
            'solve_cargo_tracking_id' => 'MSCU9876543',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-05 10:00:00',
            'client_data' => ['transitaria' => 'ENCI', 'province' => 'Matanzas'],
            'technical_specs' => ['weight_kg' => 130.0],
        ]);

        Equipment::create([
            'tracking_pin' => 'PIN-CONT2-01',
            'vin_serial' => 'VIN-CONT2-01',
            'solve_cargo_tracking_id' => 'CMAU1234567',
            'equipment_type' => EquipmentType::SOLAR_SYSTEM,
            'business_type' => BusinessType::B2C,
            'brand' => 'Growatt',
            'model' => 'SPF-5000',
            'color' => 'Blanco',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-06 14:00:00',
            'client_data' => ['transitaria' => 'A.V', 'province' => 'Holguín'],
            'technical_specs' => ['weight_kg' => 85.0],
        ]);

        $response = $this->actingAs($jefe)->get(route('profitability.operational-containers'));

        $response->assertStatus(200);
        $response->assertSee('MSCU9876543');
        $response->assertSee('CMAU1234567');
        $response->assertSee('Vincular & Precalcular', false);
    }

    public function test_link_and_precalculate_container_from_operational_equipments(): void
    {
        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        $whCentro = RegionalWarehouse::create([
            'name' => 'Almacén Santa Clara Central',
            'code' => 'WH-STACLARA',
            'region' => 'Centro',
            'province' => 'Villa Clara',
            'address' => 'Zona Industrial Santa Clara',
            'is_active' => true,
        ]);

        // Equipo 1: Occidente por provincia de cliente (La Habana)
        Equipment::create([
            'tracking_pin' => 'PIN-AUTO-01',
            'vin_serial' => 'VIN-AUTO-01',
            'solve_cargo_tracking_id' => 'CONT-AUTO-2026',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-08 08:30:00',
            'client_data' => ['transitaria' => 'ENCI', 'province' => 'La Habana'],
            'technical_specs' => ['weight_kg' => 500.0],
        ]);

        // Equipo 2: Centro por almacén actual
        Equipment::create([
            'tracking_pin' => 'PIN-AUTO-02',
            'vin_serial' => 'VIN-AUTO-02',
            'solve_cargo_tracking_id' => 'CONT-AUTO-2026',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'current_warehouse_id' => $whCentro->id,
            'customs_entry_at' => '2026-09-08 08:30:00',
            'client_data' => ['transitaria' => 'ENCI'],
            'technical_specs' => ['weight_kg' => 300.0],
        ]);

        // Equipo 3: Oriente por provincia de cliente (Santiago de Cuba)
        Equipment::create([
            'tracking_pin' => 'PIN-AUTO-03',
            'vin_serial' => 'VIN-AUTO-03',
            'solve_cargo_tracking_id' => 'CONT-AUTO-2026',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-08 08:30:00',
            'client_data' => ['transitaria' => 'ENCI', 'province' => 'Santiago de Cuba'],
            'technical_specs' => ['weight_kg' => 200.0],
        ]);

        $service = app(ContainerOperationalIntegrationService::class);

        // 1. Extraer datos operativos agrupados
        $extracted = $service->extractOperationalData('CONT-AUTO-2026');
        $this->assertEquals(500.0, $extracted['kg_occidente']);
        $this->assertEquals(300.0, $extracted['kg_centro']);
        $this->assertEquals(200.0, $extracted['kg_oriente']);
        $this->assertEquals(1000.0, $extracted['kg_total']);
        $this->assertEquals(3, $extracted['equipment_count']);
        $this->assertTrue($extracted['missing_data_flags']['is_precalculation']);

        // 2. Vincular y precalcular
        $analysis = $service->linkAndPrecalculateContainer('CONT-AUTO-2026', $jefe);

        $this->assertNotNull($analysis);
        $this->assertEquals('CONT-AUTO-2026', $analysis->container_id);
        $this->assertEquals('abierto', $analysis->economic_status);
        $this->assertEquals(1000.0, $analysis->kg_total);
        $this->assertEquals($jefe->id, $analysis->created_by);

        // Verificar relación Eloquent inversa entre equipos y análisis
        $this->assertCount(3, $analysis->equipments);
        $eqSample = Equipment::where('solve_cargo_tracking_id', 'CONT-AUTO-2026')->first();
        $this->assertEquals($analysis->id, $eqSample->profitabilityAnalysis->id);
    }

    public function test_http_link_and_close_reopen_flow(): void
    {
        $jefe = User::factory()->create([
            'role' => UserRole::JEFE_ECONOMICO,
            'is_active' => true,
        ]);

        Equipment::create([
            'tracking_pin' => 'PIN-FLOW-01',
            'vin_serial' => 'VIN-FLOW-01',
            'solve_cargo_tracking_id' => 'CONT-FLOW-01',
            'equipment_type' => EquipmentType::VEHICLE,
            'business_type' => BusinessType::B2C,
            'brand' => 'Taizhou',
            'model' => 'TZ-2026',
            'color' => 'Azul',
            'current_status' => EquipmentStatus::PORT_ARRIVAL,
            'customs_entry_at' => '2026-09-08 12:00:00',
            'client_data' => ['transitaria' => 'ENCI', 'province' => 'La Habana'],
            'technical_specs' => ['weight_kg' => 2500.0],
        ]);

        // 1. Vincular contenedor via HTTP POST
        $linkResponse = $this->actingAs($jefe)->post(route('profitability.link-operational', 'CONT-FLOW-01'));
        $analysis = ContainerProfitabilityAnalysis::where('container_id', 'CONT-FLOW-01')->first();
        $this->assertNotNull($analysis);
        $linkResponse->assertRedirect(route('profitability.show', $analysis));
        $this->assertEquals('abierto', $analysis->economic_status);

        // 2. Cerrar análisis económico oficialmente
        $closeResponse = $this->actingAs($jefe)->post(route('profitability.close', $analysis), [
            'notes' => 'Cierre contable preliminar conforme',
        ]);
        $closeResponse->assertRedirect(route('profitability.show', $analysis));
        $analysis->refresh();
        $this->assertEquals('cerrado', $analysis->economic_status);
        $this->assertEquals($jefe->id, $analysis->closed_by);
        $this->assertNotNull($analysis->applied_rates_snapshot);

        // 3. Reabrir análisis económico con justificación
        $reopenResponse = $this->actingAs($jefe)->post(route('profitability.reopen', $analysis), [
            'reason' => 'Ajuste de jornales transitaria por descarga nocturna',
        ]);
        $reopenResponse->assertRedirect(route('profitability.show', $analysis));
        $analysis->refresh();
        $this->assertEquals('en_revision', $analysis->economic_status);
        $this->assertEquals('Ajuste de jornales transitaria por descarga nocturna', $analysis->reopened_reason);
    }
}
