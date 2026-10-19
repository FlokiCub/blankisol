<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Enums\UserRole;
use App\Models\Equipment;
use App\Models\RegionalWarehouse;
use App\Models\User;
use App\Services\ManifestImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class Sprint32ManifestExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $despachoUser;
    protected User $unauthorizedUser;
    protected RegionalWarehouse $warehouseHabana;
    protected RegionalWarehouse $warehouseSantiago;
    protected ManifestImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importService = app(ManifestImportService::class);

        $this->director = User::factory()->create(['role' => UserRole::DIRECTOR]);
        $this->despachoUser = User::factory()->create(['role' => UserRole::DESPACHO_PUERTO]);
        $this->unauthorizedUser = User::factory()->create(['role' => UserRole::CAJERO_REGIONAL]);

        $this->warehouseHabana = RegionalWarehouse::factory()->create([
            'name' => 'Centro Distribución La Habana',
            'province' => 'La Habana',
        ]);

        $this->warehouseSantiago = RegionalWarehouse::factory()->create([
            'name' => 'Almacén Regional Santiago',
            'province' => 'Santiago de Cuba',
        ]);
    }

    /**
     * Helper to create a temporary .xlsx file with the 22 user columns.
     */
    protected function createSampleExcelFile(array $dataRows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'ESTADO',
            'No CONTENEDOR',
            'No CHASSIS',
            'No MOTOR',
            'COLOR',
            'MARCA',
            'MODELO',
            'AÑO DE FABRICACION',
            'TIPO DE ALIMENTACION',
            'PAIS DE ORIGEN',
            'CODIGO DE ENVIO',
            'CONTENIDO',
            'FECHA DE CARGA',
            'PRIMER NOMBRE',
            'SEGUNDO NOMBRE',
            'PRIMER APELLIDO',
            'SEGUNDO APELLIDO',
            'CI',
            'PAS',
            'No TELEFONO',
            'PROVINCIA',
            'OBSERVACIONES',
        ];

        $rows = array_merge([$headers], $dataRows);
        $sheet->fromArray($rows, null, 'A1');

        $filePath = tempnam(sys_get_temp_dir(), 'test_manifest_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Test: RBAC — Solo Despacho, Aduana y Director pueden acceder a importar manifiestos.
     */
    public function test_manifest_import_rbac_protection(): void
    {
        // Director y Despacho tienen acceso
        $this->actingAs($this->director)->get(route('manifests.import.index'))->assertStatus(200);
        $this->actingAs($this->despachoUser)->get(route('manifests.import.index'))->assertStatus(200);

        // Cajero es bloqueado
        $this->actingAs($this->unauthorizedUser)->get(route('manifests.import.index'))->assertStatus(403);
    }

    /**
     * Test: Descarga de plantilla oficial .xlsx.
     */
    public function test_download_official_manifest_template(): void
    {
        $response = $this->actingAs($this->director)->get(route('manifests.import.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test: ManifestImportService parsea y crea correctamente equipos con sus 22 atributos.
     */
    public function test_service_parses_and_imports_excel_manifest(): void
    {
        $filePath = $this->createSampleExcelFile([
            [
                'ENTREGADO',
                'TLLU5148616',
                'VIN-2026-YADEA-99881',
                'MOT-72V-3000W',
                'Gris Titanio',
                'Yadea',
                'G5 Pro',
                '2025',
                'ELECTRICA',
                'CHINA',
                'SC-2026-MIA-00921',
                '1 MOTO ELECTRICA Y ACCESORIOS',
                '2025-01-28',
                'CARLOS',
                'ALBERTO',
                'GOMEZ',
                'RODRIGUEZ',
                '96101412345',
                '',
                '52345678',
                'LA HABANA',
                'Calle 23 #456 entre G y H, Vedado',
            ],
            [
                'EN PUERTO',
                'GESU602128',
                'VIN-2026-HUA-55441',
                'INV-10KW-DC',
                'Blanco',
                'Huawei Solar',
                'Smart Energy 10kW',
                '2024',
                'SOLAR FOTOVOLTAICO',
                'CHINA',
                'SC-2026-MIA-00955',
                'SISTEMA SOLAR FOTOVOLTAICO',
                '2025-01-28',
                'MARIA',
                'ELENA',
                'SANCHEZ',
                'DIAZ',
                '88052044321',
                '',
                '57778899',
                'SANTIAGO DE CUBA',
                'Reparto Vista Alegre',
            ],
        ]);

        $parsed = $this->importService->parseFile($filePath);
        $this->assertEquals(2, $parsed['total_rows']);

        $result = $this->importService->importRows($parsed['rows'], $this->director);

        $this->assertEquals(2, $result['imported']);
        $this->assertEquals(0, $result['failed']);

        // Verificar Equipo 1 en BD
        $this->assertDatabaseHas('equipments', [
            'vin_serial' => 'VIN-2026-YADEA-99881',
            'solve_cargo_tracking_id' => 'SC-2026-MIA-00921',
            'brand' => 'Yadea',
            'model' => 'G5 Pro',
            'current_warehouse_id' => $this->warehouseHabana->id,
            'equipment_type' => EquipmentType::VEHICLE->value,
        ]);

        // Verificar Equipo 2 en BD
        $this->assertDatabaseHas('equipments', [
            'vin_serial' => 'VIN-2026-HUA-55441',
            'solve_cargo_tracking_id' => 'SC-2026-MIA-00955',
            'brand' => 'Huawei Solar',
            'current_warehouse_id' => $this->warehouseSantiago->id,
            'equipment_type' => EquipmentType::SOLAR_SYSTEM->value,
        ]);

        $eq1 = Equipment::where('vin_serial', 'VIN-2026-YADEA-99881')->first();
        $this->assertEquals('CARLOS ALBERTO GOMEZ RODRIGUEZ', $eq1->client_data['name']);
        $this->assertEquals('96101412345', $eq1->client_data['id_card']);
        $this->assertEquals('52345678', $eq1->client_data['phone']);
        $this->assertEquals('TLLU5148616', $eq1->technical_specs['container_number']);
        $this->assertEquals('MOT-72V-3000W', $eq1->technical_specs['motor_number']);

        @unlink($filePath);
    }

    /**
     * Test: Flujo completo HTTP de previsualización y ejecución de importación.
     */
    public function test_manifest_import_http_upload_preview_and_execution_flow(): void
    {
        $filePath = $this->createSampleExcelFile([
            [
                'EN PUERTO',
                'TLLU999111',
                'VIN-HTTP-TEST-001',
                'MOT-100',
                'Rojo',
                'Taizhou',
                'Titan 125',
                '2025',
                'ELECTRICA',
                'CHINA',
                'SC-HTTP-001',
                'MOTO ELECTRICA',
                '2025-01-29',
                'PEDRO',
                '',
                'PABLO',
                '',
                '90010111223',
                '',
                '53334455',
                'LA HABANA',
                'Playa, La Habana',
            ],
        ]);

        $uploadedFile = new UploadedFile($filePath, 'manifiesto_test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // 1. Subir archivo a previsualización
        $previewResponse = $this->actingAs($this->director)->post(route('manifests.import.preview'), [
            'file' => $uploadedFile,
            'send_whatsapp' => 0,
        ]);

        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('VIN-HTTP-TEST-001');
        $previewResponse->assertSee('PEDRO PABLO');
        $previewResponse->assertSee('TLLU999111');

        // 2. Confirmar ejecución de importación
        $executeResponse = $this->actingAs($this->director)->post(route('manifests.import.execute'));

        $executeResponse->assertRedirect(route('manifests.import.index'));
        $executeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('equipments', [
            'vin_serial' => 'VIN-HTTP-TEST-001',
            'solve_cargo_tracking_id' => 'SC-HTTP-001',
            'brand' => 'Taizhou',
        ]);

        @unlink($filePath);
    }
}
