<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Models\Equipment;
use App\Models\EquipmentStatusHistory;
use App\Models\RegionalWarehouse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ManifestImportService
{
    public function __construct(
        protected WhatsAppNotificationService $whatsAppService
    ) {}

    /**
     * Mapeo de columnas normalizadas del Excel oficial.
     */
    protected array $columnAliases = [
        'estado' => ['estado', 'status'],
        'contenedor' => ['no contenedor', 'nocontenedor', 'contenedor', 'container', 'no. contenedor'],
        'chasis' => ['no chassis', 'nochassis', 'chassis', 'chasis', 'no chasis', 'vin', 'serial', 'no. chassis'],
        'motor' => ['no motor', 'nomotor', 'motor', 'no. motor', 'motor serial'],
        'color' => ['color'],
        'marca' => ['marca', 'brand'],
        'modelo' => ['modelo', 'model'],
        'anio' => ['año de fabricacion', 'ano de fabricacion', 'anio de fabricacion', 'año', 'ano', 'year'],
        'alimentacion' => ['tipo de alimentacion', 'alimentacion', 'tipo alimentacion', 'combustible'],
        'pais' => ['pais de origen', 'pais origen', 'origen', 'country'],
        'codigo_envio' => ['codigo de envio', 'codigo envio', 'tracking', 'guia', 'bl', 'solve cargo', 'solvecargo id'],
        'contenido' => ['contenido', 'descripcion', 'mercancia', 'producto'],
        'fecha_carga' => ['fecha de carga', 'fecha carga', 'fecha'],
        'primer_nombre' => ['primer nombre', '1er nombre', 'nombre 1', 'nombre'],
        'segundo_nombre' => ['segundo nombre', '2do nombre', 'nombre 2'],
        'primer_apellido' => ['primer apellido', '1er apellido', 'apellido 1', 'apellido'],
        'segundo_apellido' => ['segundo apellido', '2do apellido', 'apellido 2'],
        'ci' => ['ci', 'carnet', 'carnet de identidad', 'cedula'],
        'pas' => ['pas', 'pasaporte', 'passport'],
        'telefono' => ['no telefono', 'notelefono', 'telefono', 'celular', 'phone', 'no. telefono'],
        'provincia' => ['provincia', 'destino', 'ciudad', 'province'],
        'observaciones' => ['observaciones', 'observacion', 'direccion', 'notas', 'address'],
    ];

    /**
     * Lee y parsea un archivo Excel (.xlsx, .xls) o CSV a una estructura normalizada de bultos.
     *
     * @return array{headers: array<int, string>, rows: array<int, array<string, mixed>>, total_rows: int}
     */
    public function parseFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        $rowsData = $worksheet->rangeToArray("A1:{$highestColumn}{$highestRow}", null, true, true, true);

        if (empty($rowsData)) {
            return ['headers' => [], 'rows' => [], 'total_rows' => 0];
        }

        // Buscar fila de encabezados (primera fila no vacía)
        $headerRowIndex = null;
        $headers = [];

        foreach ($rowsData as $rowIndex => $row) {
            $nonEmpty = array_filter($row, fn ($val) => $val !== null && trim((string) $val) !== '');
            if (count($nonEmpty) >= 3) {
                $headerRowIndex = $rowIndex;
                $headers = $row;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return ['headers' => [], 'rows' => [], 'total_rows' => 0];
        }

        // Mapear índices de columnas a campos canónicos
        $columnMap = $this->resolveColumnMap($headers);

        $parsedRows = [];

        for ($r = $headerRowIndex + 1; $r <= $highestRow; $r++) {
            $row = $rowsData[$r] ?? [];
            $nonEmpty = array_filter($row, fn ($val) => $val !== null && trim((string) $val) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $extracted = $this->extractRowData($row, $columnMap);

            // Solo agregar si tiene al menos Chasis, Código de Envío o Cliente
            if (!empty($extracted['chasis']) || !empty($extracted['codigo_envio']) || !empty($extracted['cliente_nombre'])) {
                $extracted['_row_index'] = $r;
                $parsedRows[] = $extracted;
            }
        }

        return [
            'headers' => array_values(array_filter($headers, fn ($h) => $h !== null)),
            'rows' => $parsedRows,
            'total_rows' => count($parsedRows),
        ];
    }

    /**
     * Mapea los encabezados del archivo contra los alias permitidos de forma precisa.
     */
    protected function resolveColumnMap(array $headers): array
    {
        $map = [];

        // Pre-sanitizar encabezados
        $sanitized = [];
        foreach ($headers as $colKey => $headerText) {
            if (empty($headerText)) {
                continue;
            }
            $clean = strtolower(trim((string) $headerText));
            $clean = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $clean);
            $clean = preg_replace('/[^a-z0-9]/', ' ', $clean);
            $clean = preg_replace('/\s+/', ' ', trim($clean));
            $sanitized[$colKey] = $clean;
        }

        // Pase 1: Coincidencia Exacta (Máxima Prioridad)
        foreach ($sanitized as $colKey => $cleanHeader) {
            foreach ($this->columnAliases as $canonicalKey => $aliases) {
                if (isset($map[$canonicalKey])) {
                    continue;
                }
                foreach ($aliases as $alias) {
                    $cleanAlias = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], strtolower($alias));
                    $cleanAlias = preg_replace('/[^a-z0-9]/', ' ', $cleanAlias);
                    $cleanAlias = preg_replace('/\s+/', ' ', trim($cleanAlias));

                    if ($cleanHeader === $cleanAlias) {
                        $map[$canonicalKey] = $colKey;
                        break 2;
                    }
                }
            }
        }

        // Pase 2: Coincidencia por palabra completa (\b) para llaves aún no mapeadas
        foreach ($sanitized as $colKey => $cleanHeader) {
            if (in_array($colKey, $map, true)) {
                continue;
            }

            foreach ($this->columnAliases as $canonicalKey => $aliases) {
                if (isset($map[$canonicalKey])) {
                    continue;
                }

                foreach ($aliases as $alias) {
                    $cleanAlias = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], strtolower($alias));
                    $cleanAlias = preg_replace('/[^a-z0-9]/', ' ', $cleanAlias);
                    $cleanAlias = preg_replace('/\s+/', ' ', trim($cleanAlias));

                    if (preg_match('/\b' . preg_quote($cleanAlias, '/') . '\b/', $cleanHeader)) {
                        $map[$canonicalKey] = $colKey;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Extrae y sanitiza los datos de una fila según el mapa de columnas.
     */
    protected function extractRowData(array $row, array $map): array
    {
        $get = fn (string $key) => isset($map[$key]) && isset($row[$map[$key]]) ? trim((string) $row[$map[$key]]) : '';

        $primerNombre = $get('primer_nombre');
        $segundoNombre = $get('segundo_nombre');
        $primerApellido = $get('primer_apellido');
        $segundoApellido = $get('segundo_apellido');

        $fullName = trim("{$primerNombre} {$segundoNombre} {$primerApellido} {$segundoApellido}");
        $fullName = preg_replace('/\s+/', ' ', $fullName);

        return [
            'estado_origen' => $get('estado'),
            'contenedor' => strtoupper($get('contenedor')),
            'chasis' => strtoupper($get('chasis')),
            'motor' => strtoupper($get('motor')),
            'color' => $get('color'),
            'marca' => $get('marca'),
            'modelo' => $get('modelo'),
            'anio' => $get('anio'),
            'alimentacion' => $get('alimentacion'),
            'pais_origen' => $get('pais'),
            'codigo_envio' => strtoupper($get('codigo_envio')),
            'contenido' => $get('contenido'),
            'fecha_carga' => $get('fecha_carga'),
            'cliente_nombre' => $fullName,
            'ci' => $get('ci'),
            'pasaporte' => $get('pas'),
            'telefono' => $get('telefono'),
            'provincia' => $get('provincia'),
            'observaciones' => $get('observaciones'),
        ];
    }

    /**
     * Importa masivamente las filas parseadas a la base de datos con transacciones atómicas.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param User $importer
     * @param array<string, mixed> $options
     * @return array{imported: int, updated: int, failed: int, errors: array<int, string>}
     */
    public function importRows(array $rows, User $importer, array $options = []): array
    {
        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $errors = [];

        $defaultStatus = $options['initial_status'] ?? EquipmentStatus::PORT_ARRIVAL;
        $sendWhatsApp = $options['send_whatsapp'] ?? false;

        $warehouses = RegionalWarehouse::all();

        foreach ($rows as $index => $row) {
            try {
                DB::transaction(function () use (
                    $row,
                    $importer,
                    $defaultStatus,
                    $sendWhatsApp,
                    $warehouses,
                    &$importedCount,
                    &$updatedCount
                ) {
                    $vin = !empty($row['chasis']) ? $row['chasis'] : 'VIN-' . strtoupper(Str::random(12));
                    $solveCargoId = !empty($row['codigo_envio']) ? $row['codigo_envio'] : null;

                    // Buscar si ya existe por VIN o SolveCargo ID
                    $equipment = null;
                    if (!empty($row['chasis'])) {
                        $equipment = Equipment::where('vin_serial', $row['chasis'])->first();
                    }
                    if (!$equipment && $solveCargoId) {
                        $equipment = Equipment::where('solve_cargo_tracking_id', $solveCargoId)->first();
                    }

                    // Emparejar almacén por provincia
                    $matchedWarehouse = null;
                    if (!empty($row['provincia'])) {
                        $prov = strtolower($row['provincia']);
                        $matchedWarehouse = $warehouses->first(function ($wh) use ($prov) {
                            return str_contains(strtolower($wh->province), $prov) || str_contains($prov, strtolower($wh->province));
                        });
                    }

                    // Determinar Tipo de Equipo
                    $equipmentType = $this->inferEquipmentType($row['contenido'] . ' ' . $row['alimentacion']);

                    // Construir JSONs
                    $clientData = [
                        'name' => $row['cliente_nombre'] ?: 'Consignatario General',
                        'phone' => $row['telefono'] ?: null,
                        'id_card' => $row['ci'] ?: null,
                        'passport' => $row['pasaporte'] ?: null,
                        'province' => $row['provincia'] ?: null,
                        'delivery_address' => $row['observaciones'] ?: null,
                    ];

                    $technicalSpecs = [
                        'motor_number' => $row['motor'] ?: null,
                        'manufacture_year' => $row['anio'] ?: null,
                        'power_type' => $row['alimentacion'] ?: null,
                        'origin_country' => $row['pais_origen'] ?: null,
                        'container_number' => $row['contenedor'] ?: null,
                        'load_date' => $row['fecha_carga'] ?: null,
                        'content_description' => $row['contenido'] ?: null,
                    ];

                    if ($equipment) {
                        // Actualizar equipo existente
                        $equipment->update([
                            'brand' => $row['marca'] ?: $equipment->brand,
                            'model' => $row['modelo'] ?: $equipment->model,
                            'color' => $row['color'] ?: $equipment->color,
                            'solve_cargo_tracking_id' => $solveCargoId ?: $equipment->solve_cargo_tracking_id,
                            'client_data' => array_merge($equipment->client_data ?? [], array_filter($clientData)),
                            'technical_specs' => array_merge($equipment->technical_specs ?? [], array_filter($technicalSpecs)),
                            'current_warehouse_id' => $matchedWarehouse?->id ?? $equipment->current_warehouse_id,
                        ]);

                        $updatedCount++;
                    } else {
                        // Crear nuevo equipo con PIN único generado
                        $trackingPin = 'BK-' . strtoupper(Str::random(5));
                        while (Equipment::where('tracking_pin', $trackingPin)->exists()) {
                            $trackingPin = 'BK-' . strtoupper(Str::random(5));
                        }

                        $equipment = Equipment::create([
                            'tracking_pin' => $trackingPin,
                            'vin_serial' => $vin,
                            'solve_cargo_tracking_id' => $solveCargoId,
                            'equipment_type' => $equipmentType,
                            'business_type' => BusinessType::B2C,
                            'brand' => $row['marca'] ?: 'Desconocida',
                            'model' => $row['modelo'] ?: 'Estándar',
                            'color' => $row['color'] ?: 'Gris',
                            'current_status' => $defaultStatus,
                            'customs_status' => 'pending',
                            'declared_value_usd' => 1500.00,
                            'duty_amount_usd' => 150.00,
                            'shipping_fee_usd' => 50.00,
                            'home_delivery_requested' => !empty($row['observaciones']),
                            'duty_paid_abroad' => false,
                            'delivery_paid_abroad' => false,
                            'customs_entry_at' => now(),
                            'current_warehouse_id' => $matchedWarehouse?->id,
                            'current_location_note' => $matchedWarehouse ? "Asignado a Almacén {$matchedWarehouse->name}" : 'Arribo a Puerto Mariel',
                            'client_data' => $clientData,
                            'technical_specs' => $technicalSpecs,
                            'created_by_user_id' => $importer->id,
                        ]);

                        EquipmentStatusHistory::create([
                            'equipment_id' => $equipment->id,
                            'from_status' => EquipmentStatus::SUPPLIER_DISPATCHED->value,
                            'to_status' => $defaultStatus->value,
                            'location' => 'Puerto del Mariel — Manifiesto Carga',
                            'notes' => "Importado masivamente vía Manifiesto Excel. Contenedor: {$row['contenedor']}.",
                            'changed_by_user_id' => $importer->id,
                            'recorded_at' => now(),
                        ]);

                        if ($sendWhatsApp && !empty($row['telefono'])) {
                            try {
                                $this->whatsAppService->sendStatusUpdateNotification($equipment);
                            } catch (\Throwable $e) {
                                Log::error("Error enviando WhatsApp en importación: {$e->getMessage()}");
                            }
                        }

                        $importedCount++;
                    }
                });
            } catch (Throwable $e) {
                $failedCount++;
                $rowNum = $row['_row_index'] ?? ($index + 2);
                $errors[] = "Fila #{$rowNum} (Chasis: {$row['chasis']}): {$e->getMessage()}";
                Log::error("Error importando fila #{$rowNum}: {$e->getMessage()}");
            }
        }

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'failed' => $failedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Deduce el tipo de equipo basado en el texto del contenido.
     */
    protected function inferEquipmentType(string $text): EquipmentType
    {
        $lower = strtolower($text);

        if (str_contains($lower, 'moto') || str_contains($lower, 'auto') || str_contains($lower, 'carro') || str_contains($lower, 'vehiculo') || str_contains($lower, 'triciclo') || str_contains($lower, 'cuadriciclo') || str_contains($lower, 'scooter') || str_contains($lower, 'camion')) {
            return EquipmentType::VEHICLE;
        }

        if (str_contains($lower, 'solar') || str_contains($lower, 'inversor') || str_contains($lower, 'panel') || str_contains($lower, 'fotovoltaico')) {
            return EquipmentType::SOLAR_SYSTEM;
        }

        if (str_contains($lower, 'bateria') || str_contains($lower, 'battery') || str_contains($lower, 'acumulador')) {
            return EquipmentType::BATTERY_PACK;
        }

        if (str_contains($lower, 'repuesto') || str_contains($lower, 'accesorio') || str_contains($lower, 'pieza') || str_contains($lower, 'generador') || str_contains($lower, 'planta')) {
            return EquipmentType::ACCESSORY;
        }

        return EquipmentType::VEHICLE;
    }

    /**
     * Genera y retorna un archivo Excel (.xlsx) con la plantilla oficial del modelo.
     */
    public function generateTemplateExcel(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Manifiesto_Carga');

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

        $sampleRow = [
            'EN PUERTO',
            'TLLU5148616',
            'YAD-2026-CH8899',
            'MOT-72V-3000W',
            'Azul Marino',
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
        ];

        // Escribir encabezados y datos de ejemplo
        $sheet->fromArray([$headers, $sampleRow], null, 'A1');

        $tempFile = tempnam(sys_get_temp_dir(), 'manifest_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return $content ?: '';
    }
}
