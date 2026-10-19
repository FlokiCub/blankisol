<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\FixedCostItem;
use Illuminate\Database\Seeder;

class FixedCostItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Activos fijos base del Excel (Hoja ACTIVOS)
        Asset::updateOrCreate(
            ['description' => 'Camión porta-contenedor (recogida en el puerto)'],
            [
                'asset_number' => 'VEH-PUERTO-01',
                'asset_type' => 'Vehículo propio',
                'registration_date' => '2025-01-01',
                'acquisition_value' => 45000.00,
                'residual_value' => 5000.00,
                'useful_life_years' => 8,
                'monthly_maintenance' => 150.00,
                'annual_insurance' => 1020.00,
                'notes' => 'Flota propia para transfer Mariel - Transitaria',
                'is_active' => true,
            ]
        );

        Asset::updateOrCreate(
            ['description' => 'Computadoras y equipos de oficina'],
            [
                'asset_number' => 'EQ-OFICINA-01',
                'asset_type' => 'Equipo',
                'registration_date' => '2025-12-01',
                'acquisition_value' => 1500.00,
                'residual_value' => 0.00,
                'useful_life_years' => 5,
                'monthly_maintenance' => 0.00,
                'annual_insurance' => 0.00,
                'notes' => 'Equipos de cómputo y control administrativo',
                'is_active' => true,
            ]
        );

        // 2. ~30 Conceptos de costos fijos individuales (Hoja COSTOS_FIJOS)
        $items = [
            [
                'code' => 'CF01',
                'name' => 'Salario del chofer del vehículo propio (puerto)',
                'cost_center' => 'GENERAL',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => true,
                'monthly_base_value' => 60.00,
                'description' => 'Salario base chofer plantilla propia',
            ],
            [
                'code' => 'CF02',
                'name' => 'Salarios de dirección, economía y contabilidad',
                'cost_center' => 'GENERAL',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => true,
                'monthly_base_value' => 250.00,
                'description' => 'Personal administrativo y de dirección central',
            ],
            [
                'code' => 'CF03',
                'name' => 'Comercialización – parte fija (21 gestores, desde COMERCIAL)',
                'cost_center' => 'GENERAL',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => true,
                'monthly_base_value' => 1050.00,
                'description' => 'Pago fijo mensual de 21 gestores comerciales (50 USD c/u)',
            ],
            [
                'code' => 'CF04',
                'name' => 'Alquiler de local / almacén',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 200.00,
                'description' => 'Arrendamiento de base de operaciones central',
            ],
            [
                'code' => 'CF05',
                'name' => 'Electricidad y agua',
                'cost_center' => 'GENERAL',
                'classification' => 'Energía',
                'is_salary' => false,
                'monthly_base_value' => 40.00,
                'description' => 'Servicios básicos de oficina y almacén',
            ],
            [
                'code' => 'CF06',
                'name' => 'Comunicaciones (teléfono, internet)',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 30.00,
                'description' => 'Líneas fijas, móviles de datos y conectividad',
            ],
            [
                'code' => 'CF07',
                'name' => 'Materiales de oficina y limpieza',
                'cost_center' => 'GENERAL',
                'classification' => 'Materias primas y materiales',
                'is_salary' => false,
                'monthly_base_value' => 15.00,
                'description' => 'Insumos de oficina, papelería y aseo',
            ],
            [
                'code' => 'CF08',
                'name' => 'Servicios contables, legales y otros externos',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 50.00,
                'description' => 'Asesoría legal, contable y auditorías externas',
            ],
            [
                'code' => 'CF09',
                'name' => 'Gastos bancarios fijos (mantenimiento de cuenta)',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 10.00,
                'description' => 'Tarifas mensuales por gestión bancaria',
            ],
            [
                'code' => 'CF10',
                'name' => 'Depreciación de activos fijos (desde ACTIVOS)',
                'cost_center' => 'GENERAL',
                'classification' => 'Depreciación y amortización',
                'is_salary' => false,
                'monthly_base_value' => 441.67,
                'description' => 'Amortización lineal de flota propia y equipos',
            ],
            [
                'code' => 'CF11',
                'name' => 'Mantenimiento preventivo de la flota propia (desde ACTIVOS)',
                'cost_center' => 'GENERAL',
                'classification' => 'Materias primas y materiales',
                'is_salary' => false,
                'monthly_base_value' => 150.00,
                'description' => 'Mantenimiento mensual programado de vehículos propios',
            ],
            [
                'code' => 'CF12',
                'name' => 'Seguros, licencias e inspecciones de la flota (desde ACTIVOS)',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 85.00,
                'description' => 'Pólizas y permisos de circulación prorrateados mes',
            ],
            [
                'code' => 'CF13',
                'name' => 'Otros gastos fijos generales',
                'cost_center' => 'GENERAL',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => 'Imprevistos y gastos generales adicionales',
            ],
            [
                'code' => 'CF14',
                'name' => 'Occidente – chofer (servicio contratado)',
                'cost_center' => 'OCC',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 120.00,
                'description' => 'Chofer contratado para distribución occidental',
            ],
            [
                'code' => 'CF15',
                'name' => 'Occidente – ayudante (servicio contratado)',
                'cost_center' => 'OCC',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 80.00,
                'description' => 'Auxiliar de carga/descarga distribución occidental',
            ],
            [
                'code' => 'CF16',
                'name' => 'Occidente – otros gastos fijos',
                'cost_center' => 'OCC',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => 'Gastos fijos específicos de Occidente',
            ],
            [
                'code' => 'CF17',
                'name' => 'Centro – chofer (servicio contratado)',
                'cost_center' => 'CEN',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 120.00,
                'description' => 'Chofer contratado para distribución central',
            ],
            [
                'code' => 'CF18',
                'name' => 'Centro – ayudante (servicio contratado)',
                'cost_center' => 'CEN',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 80.00,
                'description' => 'Auxiliar de carga/descarga distribución central',
            ],
            [
                'code' => 'CF19',
                'name' => 'Centro – otros gastos fijos (centro transitorio)',
                'cost_center' => 'CEN',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => 'Gastos fijos centro de transferencia Centro',
            ],
            [
                'code' => 'CF20',
                'name' => 'Oriente – chofer (servicio contratado)',
                'cost_center' => 'ORI',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 120.00,
                'description' => 'Chofer contratado para distribución oriental',
            ],
            [
                'code' => 'CF21',
                'name' => 'Oriente – ayudante (servicio contratado)',
                'cost_center' => 'ORI',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 80.00,
                'description' => 'Auxiliar de carga/descarga distribución oriental',
            ],
            [
                'code' => 'CF22',
                'name' => 'Oriente – otros gastos fijos (centro transitorio)',
                'cost_center' => 'ORI',
                'classification' => 'Otros gastos monetarios',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => 'Gastos fijos centro de transferencia Oriente',
            ],
            [
                'code' => 'SS-GENERAL',
                'name' => 'Contribución a la seguridad social – GENERAL',
                'cost_center' => 'GENERAL',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 190.40,
                'description' => '14% sobre salarios propios generales (CF01+CF02+CF03)',
            ],
            [
                'code' => 'FT-GENERAL',
                'name' => 'Impuesto por utilización de la fuerza de trabajo – GENERAL',
                'cost_center' => 'GENERAL',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 68.00,
                'description' => '5% sobre salarios propios generales (CF01+CF02+CF03)',
            ],
            [
                'code' => 'SS-OCC',
                'name' => 'Contribución a la seguridad social – OCC',
                'cost_center' => 'OCC',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '14% sobre salarios propios de Occidente (si aplica)',
            ],
            [
                'code' => 'FT-OCC',
                'name' => 'Impuesto por utilización de la fuerza de trabajo – OCC',
                'cost_center' => 'OCC',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '5% sobre salarios propios de Occidente (si aplica)',
            ],
            [
                'code' => 'SS-CEN',
                'name' => 'Contribución a la seguridad social – CEN',
                'cost_center' => 'CEN',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '14% sobre salarios propios de Centro (si aplica)',
            ],
            [
                'code' => 'FT-CEN',
                'name' => 'Impuesto por utilización de la fuerza de trabajo – CEN',
                'cost_center' => 'CEN',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '5% sobre salarios propios de Centro (si aplica)',
            ],
            [
                'code' => 'SS-ORI',
                'name' => 'Contribución a la seguridad social – ORI',
                'cost_center' => 'ORI',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '14% sobre salarios propios de Oriente (si aplica)',
            ],
            [
                'code' => 'FT-ORI',
                'name' => 'Impuesto por utilización de la fuerza de trabajo – ORI',
                'cost_center' => 'ORI',
                'classification' => 'Gastos de fuerza de trabajo',
                'is_salary' => false,
                'monthly_base_value' => 0.00,
                'description' => '5% sobre salarios propios de Oriente (si aplica)',
            ],
        ];

        foreach ($items as $item) {
            FixedCostItem::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
