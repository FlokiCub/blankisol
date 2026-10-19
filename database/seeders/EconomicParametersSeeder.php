<?php

namespace Database\Seeders;

use App\Models\EconomicParameter;
use App\Models\FixedCostMonthly;
use App\Models\FuelAndTariffRate;
use Illuminate\Database\Seeder;

class EconomicParametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parameters = [
            [
                'code' => 'Std_Viajes_Puerto',
                'name' => 'Viajes al puerto por contenedor',
                'value' => '1',
                'unit' => 'viajes',
                'category' => 'puerto',
                'description' => 'Un contenedor = un viaje del camión propio al puerto.',
            ],
            [
                'code' => 'Litros_Puerto_Viaje',
                'name' => 'Combustible por viaje al puerto',
                'value' => '60',
                'unit' => 'L/viaje',
                'category' => 'puerto',
                'description' => 'Norma de consumo técnico informado por la empresa hacia puerto Mariel.',
            ],
            [
                'code' => 'Km_Puerto_Viaje',
                'name' => 'Km por viaje al puerto (ida y vuelta)',
                'value' => '180',
                'unit' => 'km/viaje',
                'category' => 'puerto',
                'description' => 'Distancia estándar en hoja de ruta Mariel ida y vuelta.',
            ],
            [
                'code' => 'Rend_Desagrupe',
                'name' => 'Rendimiento del desagrupe en la transitaria',
                'value' => '1000',
                'unit' => 'kg/jornal',
                'category' => 'transitaria',
                'description' => 'Jornales = kg ÷ rendimiento (redondeado hacia arriba).',
            ],
            [
                'code' => 'Rend_Desagrupe_CT',
                'name' => 'Rendimiento del desagrupe en centros transitorios (Centro/Oriente)',
                'value' => '800',
                'unit' => 'kg/jornal',
                'category' => 'transitaria',
                'description' => 'Productividad de cuadrilla en nodos intermedios de distribución.',
            ],
            [
                'code' => 'Cap_Camion_OCC',
                'name' => 'Capacidad útil camión distribución Occidente',
                'value' => '5000',
                'unit' => 'kg/viaje',
                'category' => 'distribucion',
                'description' => 'Carga útil por camión en zona occidental.',
            ],
            [
                'code' => 'Cap_Camion_CEN',
                'name' => 'Capacidad útil camión traslado Centro',
                'value' => '5000',
                'unit' => 'kg/viaje',
                'category' => 'distribucion',
                'description' => 'Carga útil por camión hacia el nodo central.',
            ],
            [
                'code' => 'Cap_Camion_ORI',
                'name' => 'Capacidad útil camión traslado Oriente',
                'value' => '5000',
                'unit' => 'kg/viaje',
                'category' => 'distribucion',
                'description' => 'Carga útil por camión hacia el nodo oriental.',
            ],
            [
                'code' => 'L_Dist_OCC',
                'name' => 'Combustible de distribución en Occidente',
                'value' => '120',
                'unit' => 'L/viaje',
                'category' => 'distribucion',
                'description' => 'Consumo diésel para distribución puerta a puerta en Occidente.',
            ],
            [
                'code' => 'L_Tras_CEN',
                'name' => 'Combustible de traslado al centro transitorio Centro',
                'value' => '250',
                'unit' => 'L/viaje',
                'category' => 'distribucion',
                'description' => 'Consumo diésel traslado troncal Habana-Santa Clara.',
            ],
            [
                'code' => 'L_Dist_CEN',
                'name' => 'Combustible de distribución en Centro',
                'value' => '150',
                'unit' => 'L/viaje',
                'category' => 'distribucion',
                'description' => 'Consumo diésel última milla en provincias centrales.',
            ],
            [
                'code' => 'L_Tras_ORI',
                'name' => 'Combustible de traslado al centro transitorio Oriente',
                'value' => '450',
                'unit' => 'L/viaje',
                'category' => 'distribucion',
                'description' => 'Consumo diésel traslado troncal Habana-Santiago/Holguín.',
            ],
            [
                'code' => 'L_Dist_ORI',
                'name' => 'Combustible de distribución en Oriente',
                'value' => '180',
                'unit' => 'L/viaje',
                'category' => 'distribucion',
                'description' => 'Consumo diésel última milla en provincias orientales.',
            ],
            [
                'code' => 'Pct_Reclamaciones',
                'name' => 'Provisión para pérdidas, daños y reclamaciones',
                'value' => '0.005',
                'unit' => '% ingreso',
                'category' => 'general',
                'description' => '0.5% del ingreso bruto para contingencias.',
            ],
            [
                'code' => 'Pct_Banco',
                'name' => 'Comisiones bancarias y costo de cobro',
                'value' => '0.01',
                'unit' => '% ingreso',
                'category' => 'general',
                'description' => '1.0% del ingreso bruto por pasarelas y comisiones.',
            ],
            [
                'code' => 'Imp_Ventas_Serv',
                'name' => 'Impuesto sobre las ventas y los servicios',
                'value' => '0.10',
                'unit' => '% ingreso',
                'category' => 'impuestos',
                'description' => '10% Ley 113 del Sistema Tributario vigente en 2026.',
            ],
            [
                'code' => 'Contrib_Territorial',
                'name' => 'Contribución territorial para el desarrollo local',
                'value' => '0.01',
                'unit' => '% ingreso',
                'category' => 'impuestos',
                'description' => '1% Ley 113 para el desarrollo territorial.',
            ],
            [
                'code' => 'Imp_Utilidades',
                'name' => 'Impuesto sobre las utilidades',
                'value' => '0.35',
                'unit' => '% utilidad',
                'category' => 'impuestos',
                'description' => '35% estimación de tasa de impuesto de utilidades corporativas.',
            ],
            [
                'code' => 'Tolerancia_Comb',
                'name' => 'Tolerancia del desvío de combustible frente a la norma',
                'value' => '0.10',
                'unit' => '%',
                'category' => 'general',
                'description' => '10% umbral para disparar alertas operativas de sobreconsumo.',
            ],
            [
                'code' => 'Metodo_Prorrateo',
                'name' => 'Base de reparto de costos fijos entre contenedores',
                'value' => 'KG',
                'unit' => 'criterio',
                'category' => 'general',
                'description' => 'KG = proporcional al peso; CONTENEDOR = partes iguales.',
            ],
            [
                'code' => 'Base_Comision',
                'name' => 'Base del pago variable de comercialización',
                'value' => '% DEL INGRESO',
                'unit' => 'criterio',
                'category' => 'general',
                'description' => '% DEL INGRESO o USD POR KG.',
            ],
            [
                'code' => 'Contrib_SS',
                'name' => 'Contribución a la seguridad social (empleador)',
                'value' => '0.14',
                'unit' => '% salarios',
                'category' => 'impuestos',
                'description' => '14% solo sobre salarios de trabajadores propios.',
            ],
            [
                'code' => 'Pct_SS',
                'name' => 'Porcentaje de seguridad social (alias)',
                'value' => '0.14',
                'unit' => '% salarios',
                'category' => 'impuestos',
                'description' => '14% solo sobre salarios de trabajadores propios.',
            ],
            [
                'code' => 'Imp_Fuerza_Trabajo',
                'name' => 'Impuesto por la utilización de la fuerza de trabajo',
                'value' => '0.05',
                'unit' => '% salarios',
                'category' => 'impuestos',
                'description' => '5% solo sobre salarios de trabajadores propios.',
            ],
            [
                'code' => 'Pct_Fuerza_Trabajo',
                'name' => 'Porcentaje de fuerza de trabajo (alias)',
                'value' => '0.05',
                'unit' => '% salarios',
                'category' => 'impuestos',
                'description' => '5% solo sobre salarios de trabajadores propios.',
            ],
            [
                'code' => 'Km_Dist_OCC',
                'name' => 'Km de distribución en Occidente (ida y vuelta)',
                'value' => '150',
                'unit' => 'km/viaje',
                'category' => 'distribucion',
                'description' => 'Distancia estándar distribución en zona occidental.',
            ],
            [
                'code' => 'Km_Tras_CEN',
                'name' => 'Km de traslado al centro transitorio Centro',
                'value' => '300',
                'unit' => 'km/viaje',
                'category' => 'distribucion',
                'description' => 'Distancia troncal Habana-Santa Clara.',
            ],
            [
                'code' => 'Km_Dist_CEN',
                'name' => 'Km de distribución en Centro',
                'value' => '200',
                'unit' => 'km/viaje',
                'category' => 'distribucion',
                'description' => 'Distancia última milla en provincias centrales.',
            ],
            [
                'code' => 'Km_Tras_ORI',
                'name' => 'Km de traslado al centro transitorio Oriente',
                'value' => '800',
                'unit' => 'km/viaje',
                'category' => 'distribucion',
                'description' => 'Distancia troncal Habana-Santiago/Holguín.',
            ],
            [
                'code' => 'Km_Dist_ORI',
                'name' => 'Km de distribución en Oriente',
                'value' => '250',
                'unit' => 'km/viaje',
                'category' => 'distribucion',
                'description' => 'Distancia última milla en provincias orientales.',
            ],
            [
                'code' => 'Empresa',
                'name' => 'Nombre de la empresa',
                'value' => 'Empresa de Paquetería',
                'unit' => 'texto',
                'category' => 'general',
                'description' => 'Nombre de la entidad para encabezados e informes oficiales.',
            ],
            [
                'code' => 'Fecha_Inicio',
                'name' => 'Primer mes del período de análisis',
                'value' => '2026-09-01',
                'unit' => 'fecha',
                'category' => 'general',
                'description' => 'Primer mes de registro para proyecciones y análisis.',
            ],
        ];

        foreach ($parameters as $param) {
            EconomicParameter::updateOrCreate(
                ['code' => $param['code']],
                $param
            );
        }

        // Tasas y precios de referencia vigentes 2026
        FuelAndTariffRate::updateOrCreate(
            ['effective_from' => '2026-01-01'],
            [
                'tariff_occidente_usd_kg' => 0.8000,
                'tariff_centro_usd_kg' => 0.8000,
                'tariff_oriente_usd_kg' => 0.8000,
                'diesel_price_usd_l' => 2.0000, // CUPET oficial 2026
                'port_appointment_usd' => 10.00,
                'driver_diet_usd' => 10.00,
                'labor_shift_transitaria_usd' => 15.00,
                'labor_shift_transitory_center_usd' => 15.00,
                'transitaria_service_enci_usd' => 0.00,
                'transitaria_service_av_usd' => 0.00,
                'commercial_commission_pct' => 0.0300,
                'commercial_commission_usd_kg' => 0.0300,
                'vehicle_wear_usd_km' => 0.1200,
                'support_document_note' => 'Cita 10 USD, Diésel 2.00 USD/L referencia CUPET 2026',
                'is_active' => true,
            ]
        );

        // Costos fijos base
        FixedCostMonthly::updateOrCreate(
            ['year_month' => '2026-09'],
            [
                'general_fixed_usd' => 2640.07,
                'occidente_fixed_usd' => 200.00,
                'centro_fixed_usd' => 200.00,
                'oriente_fixed_usd' => 200.00,
                'notes' => 'Estructura base mensual de costos fijos de operaciones',
            ]
        );

    }
}
