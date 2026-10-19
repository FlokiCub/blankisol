<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Enums\UserRole;
use App\Models\Equipment;
use App\Models\RegionalDispatchRoute;
use App\Models\RegionalWarehouse;
use App\Models\User;
use App\Services\RegionalLogisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class Sprint23LogisticsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $driver;
    protected User $regionalCashier;
    protected RegionalWarehouse $originWarehouse;
    protected RegionalWarehouse $destinationWarehouse;
    protected Equipment $readyEquipment1;
    protected Equipment $readyEquipment2;
    protected RegionalLogisticsService $logisticsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logisticsService = app(RegionalLogisticsService::class);

        // 1. Usuarios con roles RBAC válidos
        $this->director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'name' => 'Director General',
        ]);

        $this->driver = User::factory()->create([
            'role' => UserRole::LOGISTICA_CHOFER,
            'name' => 'Chofer Manuel',
        ]);

        $this->originWarehouse = RegionalWarehouse::factory()->create([
            'code' => 'WH-MARIEL',
            'name' => 'Patio Central Mariel / Habana',
            'province' => 'Artemisa',
        ]);

        $this->destinationWarehouse = RegionalWarehouse::factory()->create([
            'code' => 'STG-01',
            'name' => 'Almacén Regional Santiago de Cuba',
            'province' => 'Santiago de Cuba',
        ]);

        $this->regionalCashier = User::factory()->create([
            'role' => UserRole::CAJERO_REGIONAL,
            'name' => 'Cajera Santiago',
            'regional_warehouse_id' => $this->destinationWarehouse->id,
        ]);

        // 2. Equipos en estado READY_FOR_DISPATCH
        $this->readyEquipment1 = Equipment::factory()->readyForDispatch()->create([
            'tracking_pin' => 'BK-ROUTE-01',
            'vin_serial' => 'VIN-ROUTE-001',
            'brand' => 'Yadea',
            'model' => 'G5 Pro',
            'current_warehouse_id' => $this->originWarehouse->id,
        ]);

        $this->readyEquipment2 = Equipment::factory()->readyForDispatch()->create([
            'tracking_pin' => 'BK-ROUTE-02',
            'vin_serial' => 'VIN-ROUTE-002',
            'brand' => 'Huawei Solar',
            'model' => 'Smart Inverter 10kW',
            'current_warehouse_id' => $this->originWarehouse->id,
        ]);
    }

    /**
     * Test: Crear una hoja de ruta con equipos asignados inicialmente en estado borrador.
     */
    public function test_can_create_a_regional_dispatch_route_with_equipments(): void
    {
        $routeData = [
            'origin_warehouse_id' => $this->originWarehouse->id,
            'destination_warehouse_id' => $this->destinationWarehouse->id,
            'driver_name' => 'Chofer Manuel',
            'driver_phone' => '+53 5 555 1234',
            'truck_license_plate' => 'B 123 456',
            'notes' => 'Carga matutina hacia Oriente',
        ];

        $route = $this->logisticsService->createRoute(
            data: $routeData,
            equipmentIds: [$this->readyEquipment1->id, $this->readyEquipment2->id],
            creator: $this->director
        );

        $this->assertInstanceOf(RegionalDispatchRoute::class, $route);
        $this->assertEquals('draft', $route->status);
        $this->assertEquals('B 123 456', $route->truck_license_plate);
        $this->assertEquals($this->destinationWarehouse->id, $route->destination_warehouse_id);
        $this->assertCount(2, $route->equipments);

        $this->assertDatabaseHas('regional_dispatch_routes', [
            'id' => $route->id,
            'status' => 'draft',
            'truck_license_plate' => 'B 123 456',
            'destination_warehouse_id' => $this->destinationWarehouse->id,
        ]);

        $this->assertDatabaseHas('dispatch_route_equipments', [
            'dispatch_route_id' => $route->id,
            'equipment_id' => $this->readyEquipment1->id,
            'reception_status' => 'assigned',
        ]);
    }

    /**
     * Test: Agregar equipo a hoja de ruta borrador mediante escaneo de PIN o VIN.
     */
    public function test_can_add_equipment_to_draft_route_by_scan(): void
    {
        $route = $this->logisticsService->createRoute(
            data: [
                'origin_warehouse_id' => $this->originWarehouse->id,
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 999 888',
            ],
            equipmentIds: [],
            creator: $this->director
        );

        $this->assertCount(0, $route->equipments);

        // Escanear y agregar por tracking PIN
        $result = $this->logisticsService->addEquipmentToRoute($route, $this->readyEquipment1->tracking_pin);
        $this->assertTrue($result['success']);

        $this->assertDatabaseHas('dispatch_route_equipments', [
            'dispatch_route_id' => $route->id,
            'equipment_id' => $this->readyEquipment1->id,
            'reception_status' => 'assigned',
        ]);
    }

    /**
     * Test: Remover equipo de una hoja de ruta en estado borrador.
     */
    public function test_can_remove_equipment_from_draft_route(): void
    {
        $route = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [$this->readyEquipment1->id],
            creator: $this->director
        );

        $this->assertCount(1, $route->equipments);

        $this->logisticsService->removeEquipmentFromRoute($route, $this->readyEquipment1);

        $this->assertDatabaseMissing('dispatch_route_equipments', [
            'dispatch_route_id' => $route->id,
            'equipment_id' => $this->readyEquipment1->id,
        ]);
    }

    /**
     * Test: Regla de negocio — No se puede agregar a la ruta un equipo que no esté en READY_FOR_DISPATCH.
     */
    public function test_cannot_add_equipment_not_ready_for_dispatch(): void
    {
        $notReadyEquipment = Equipment::factory()->inCustoms()->create([
            'tracking_pin' => 'BK-CUST-99',
        ]);

        $route = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [],
            creator: $this->director
        );

        $this->expectException(InvalidArgumentException::class);
        $this->logisticsService->addEquipmentToRoute($route, $notReadyEquipment->tracking_pin);
    }

    /**
     * Test: Regla de negocio — No se puede despachar una hoja de ruta vacía.
     */
    public function test_cannot_dispatch_empty_route(): void
    {
        $emptyRoute = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [],
            creator: $this->director
        );

        $this->expectException(InvalidArgumentException::class);
        $this->logisticsService->dispatchRoute($emptyRoute, $this->driver);
    }

    /**
     * Test: Despachar hoja de ruta transiciona ruta a 'in_transit' y equipos a 'REGIONAL_TRANSIT'.
     */
    public function test_dispatch_route_transitions_equipments_to_regional_transit(): void
    {
        $route = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [$this->readyEquipment1->id, $this->readyEquipment2->id],
            creator: $this->director
        );

        $dispatchedRoute = $this->logisticsService->dispatchRoute($route, $this->driver);

        $this->assertEquals('in_transit', $dispatchedRoute->status);
        $this->assertNotNull($dispatchedRoute->dispatched_at);

        $this->readyEquipment1->refresh();
        $this->readyEquipment2->refresh();

        $this->assertEquals(EquipmentStatus::REGIONAL_TRANSIT, $this->readyEquipment1->current_status);
        $this->assertEquals(EquipmentStatus::REGIONAL_TRANSIT, $this->readyEquipment2->current_status);

        // Bitácora de auditoría registrada
        $this->assertDatabaseHas('equipment_status_histories', [
            'equipment_id' => $this->readyEquipment1->id,
            'to_status' => EquipmentStatus::REGIONAL_TRANSIT->value,
            'changed_by_user_id' => $this->driver->id,
        ]);
    }

    /**
     * Test: Recepción completa del convoy en almacén regional de destino.
     */
    public function test_receive_route_at_destination_transitions_equipments_to_in_regional_warehouse(): void
    {
        $route = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [$this->readyEquipment1->id, $this->readyEquipment2->id],
            creator: $this->director
        );

        $dispatchedRoute = $this->logisticsService->dispatchRoute($route, $this->driver);

        $receivedRoute = $this->logisticsService->receiveRouteAtDestination(
            route: $dispatchedRoute,
            regionalReceiver: $this->regionalCashier
        );

        $this->assertEquals('arrived_destination', $receivedRoute->status);
        $this->assertNotNull($receivedRoute->arrived_at);

        $this->readyEquipment1->refresh();
        $this->readyEquipment2->refresh();

        $this->assertEquals(EquipmentStatus::IN_REGIONAL_WAREHOUSE, $this->readyEquipment1->current_status);
        $this->assertEquals($this->destinationWarehouse->id, $this->readyEquipment1->current_warehouse_id);

        $this->assertEquals(EquipmentStatus::IN_REGIONAL_WAREHOUSE, $this->readyEquipment2->current_status);
        $this->assertEquals($this->destinationWarehouse->id, $this->readyEquipment2->current_warehouse_id);

        $this->assertDatabaseHas('equipment_status_histories', [
            'equipment_id' => $this->readyEquipment1->id,
            'to_status' => EquipmentStatus::IN_REGIONAL_WAREHOUSE->value,
            'changed_by_user_id' => $this->regionalCashier->id,
        ]);
    }

    /**
     * Test: Recepción individual por escaneo en destino.
     */
    public function test_receive_single_equipment_by_scan_at_destination(): void
    {
        $route = $this->logisticsService->createRoute(
            data: [
                'destination_warehouse_id' => $this->destinationWarehouse->id,
                'driver_name' => 'Chofer Manuel',
                'truck_license_plate' => 'B 123 456',
            ],
            equipmentIds: [$this->readyEquipment1->id, $this->readyEquipment2->id],
            creator: $this->director
        );

        $dispatchedRoute = $this->logisticsService->dispatchRoute($route, $this->driver);

        // Escanear solo el equipo 1 en ventanilla de destino
        $result = $this->logisticsService->receiveSingleEquipmentAtDestination(
            route: $dispatchedRoute,
            barcodeOrPin: $this->readyEquipment1->tracking_pin,
            regionalReceiver: $this->regionalCashier
        );

        $this->assertTrue($result['success']);

        $this->readyEquipment1->refresh();
        $this->readyEquipment2->refresh();

        $this->assertEquals(EquipmentStatus::IN_REGIONAL_WAREHOUSE, $this->readyEquipment1->current_status);
        $this->assertEquals($this->destinationWarehouse->id, $this->readyEquipment1->current_warehouse_id);

        // El equipo 2 aún no escaneado sigue en REGIONAL_TRANSIT
        $this->assertEquals(EquipmentStatus::REGIONAL_TRANSIT, $this->readyEquipment2->current_status);
    }

    /**
     * Test: Rutas web RBAC para controlador de logística regional.
     */
    public function test_logistics_routes_accessible_by_authorized_roles(): void
    {
        $response = $this->actingAs($this->director)->get(route('logistics.routes.index'));
        $response->assertStatus(200);

        $responseDriver = $this->actingAs($this->driver)->get(route('logistics.routes.index'));
        $responseDriver->assertStatus(200);
    }
}
