<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShopDemoSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. USUARIO ADMINISTRADOR DE LA TIENDA (BLANKISOL SHOP)
        // =========================================================================
        $shopAdminPassword = Hash::make('Password123!');

        $shopAdmin = User::updateOrCreate(
            ['email' => 'admin.shop@blankisol.com'],
            [
                'name' => 'Administrador Tienda Blankisol',
                'username' => 'admin_shop',
                'email' => 'admin.shop@blankisol.com',
                'password' => $shopAdminPassword,
                'role' => UserRole::ADMINISTRADOR_TIENDA,
                'phone' => '+53 5 888 7777',
                'regional_warehouse_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // =========================================================================
        // 2. CATEGORÍAS PRINCIPALES DEL CATÁLOGO COMERCIAL
        // =========================================================================
        $categoriesData = [
            [
                'name' => 'Motos y Movilidad Eléctrica',
                'slug' => 'motos-movilidad-electrica',
                'description' => 'Motos eléctricas de alto rendimiento, scooters plegables, trimóviles de carga y baterías de litio para transporte urbano en Cuba.',
                'icon' => 'fa-solid fa-motorcycle',
                'sort_order' => 1,
            ],
            [
                'name' => 'Energía Solar y Baterías Litio',
                'slug' => 'energia-solar-baterias',
                'description' => 'Kits solares híbridos completos, inversores de onda pura, baterías LiFePO4 de 48V y paneles solares monocristalinos de alta eficiencia.',
                'icon' => 'fa-solid fa-solar-panel',
                'sort_order' => 2,
            ],
            [
                'name' => 'Generadores y Motores',
                'slug' => 'generadores-motores',
                'description' => 'Plantas eléctricas diésel insonorizadas, generadores inverter silenciosos para apagones y motobombas industriales de alto caudal.',
                'icon' => 'fa-solid fa-bolt',
                'sort_order' => 3,
            ],
            [
                'name' => 'Equipamiento Industrial & Ferretería',
                'slug' => 'equipamiento-industrial',
                'description' => 'Soldadoras inverter multiproceso, hidrolavadoras de alta presión, compresores bicilíndricos y herramientas pesadas para talleres STI.',
                'icon' => 'fa-solid fa-gears',
                'sort_order' => 4,
            ],
            [
                'name' => 'Repuestos & Mantenimiento STI',
                'slug' => 'repuestos-sti',
                'description' => 'Neumáticos tubeless reforzados, cargadores rápidos inteligentes, controladores y piezas de recambio garantizadas con stock en Cuba.',
                'icon' => 'fa-solid fa-wrench',
                'sort_order' => 5,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = ShopCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => true])
            );
        }

        // =========================================================================
        // 3. PRODUCTOS DE DEMOSTRACIÓN DE DIVERSAS CATEGORÍAS
        // =========================================================================
        $productsData = [
            // Categ 1: Movilidad Eléctrica
            [
                'category_slug' => 'motos-movilidad-electrica',
                'sku' => 'MOTO-LITH-72V45',
                'slug' => 'moto-electrica-lithium-72v-45ah',
                'name' => 'Moto Eléctrica Lithium 72V 45Ah - Autonomía 120km',
                'short_description' => 'Moto eléctrica urbana con motor brushless de 3000W, frenos de disco hidráulicos y batería extraíble de litio.',
                'description' => "La moto eléctrica Blankisol 72V 45Ah está diseñada específicamente para las exigencias viales de Cuba. Equipada con batería de litio de ciclo profundo LiFePO4 con más de 2000 ciclos de carga, velocidad máxima de 75 km/h, display digital a color, alarma antirrobo con bloqueo de motor y luces full LED.\n\nIncluye cargador inteligente con corte automático de energía.",
                'price' => 1650.00,
                'sale_price' => 1450.00,
                'cost_price' => 1100.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 12,
                'min_stock_alert' => 2,
                'estimated_delivery_days' => '24 a 48 horas en La Habana y Occidente',
                'weight_kg' => 115.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'motos-movilidad-electrica',
                'sku' => 'TRI-CARGA-1500',
                'slug' => 'trimovil-electrico-carga-1500kg',
                'name' => 'Trimóvil Eléctrico de Carga Pesada 1.5 Toneladas',
                'short_description' => 'Vehículo de carga para pequeñas y medianas empresas o cooperativas agropecuarias. Tolva basculante reforzada.',
                'description' => "Potente triciclo de carga eléctrica con eje posterior de reducción para cuestas empinadas, capacidad de carga útil de 1500 kg, pack de baterías reforzado de 72V 60Ah y chasis de viga doble de acero estructural.\n\nModalidad bajo pedido con entrega directa en contenedor nacionalizado en Mariel.",
                'price' => 3200.00,
                'sale_price' => 2850.00,
                'cost_price' => 2200.00,
                'inventory_type' => 'on_demand',
                'stock_quantity' => 0,
                'min_stock_alert' => 0,
                'estimated_delivery_days' => '15 a 20 días hábiles (Despacho Mariel)',
                'weight_kg' => 380.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'motos-movilidad-electrica',
                'sku' => 'SCOOT-500W-HT',
                'slug' => 'scooter-urbano-high-torque-500w',
                'name' => 'Scooter Urbano Plegable 500W High Torque con Doble Suspensión',
                'short_description' => 'Patinete de aluminio aeroespacial con neumáticos tubeless de 10 pulgadas y batería de 36V 15Ah.',
                'description' => "Solución ágil y ligera para desplazamientos rápidos. Autonomía real de 45 km por carga, frenada regenerativa, plegado en 3 segundos y resistencia IPX5 contra lluvia tropical.",
                'price' => 550.00,
                'sale_price' => 490.00,
                'cost_price' => 350.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 20,
                'min_stock_alert' => 3,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 18.50,
                'is_featured' => false,
            ],
            [
                'category_slug' => 'motos-movilidad-electrica',
                'sku' => 'BAT-LITH-72V35',
                'slug' => 'bateria-litio-extraible-72v-35ah',
                'name' => 'Batería Extraíble de Litio LiFePO4 72V 35Ah para Moto',
                'short_description' => 'Módulo de batería plug-and-play con BMS inteligente integrado para motos eléctricas tipo Bucatti, Águila y Mishozuki.',
                'description' => "Reemplazo premium para baterías de plomo o litio desgastadas. Caja metálica estanca con asa ergonómica, conector Anderson de alta corriente y display indicador de voltaje.",
                'price' => 580.00,
                'sale_price' => null,
                'cost_price' => 420.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 15,
                'min_stock_alert' => 3,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 22.00,
                'is_featured' => false,
            ],

            // Categ 2: Energía Solar y Baterías
            [
                'category_slug' => 'energia-solar-baterias',
                'sku' => 'SOLAR-KIT-5KW',
                'slug' => 'kit-solar-hibrido-5kw-onda-pura',
                'name' => 'Kit Solar Híbrido 5kW Onda Pura + 4 Baterías Gel + 8 Paneles 550W',
                'short_description' => 'Sistema solar fotovoltaico autónomo para viviendas o negocios. Respaldo continuo de 24 horas ante cortes eléctricos.',
                'description' => "Kit solar integral preconfigurado listo para instalar. Incluye inversor híbrido cargador de 5000W 48V, 8 paneles solares monocristalinos Tier-1 de 550W (4.4 kWp pico), banco de 4 baterías de ciclo profundo 12V 200Ah, protecciones DC/AC y cableado solar certificado.",
                'price' => 2990.00,
                'sale_price' => 2650.00,
                'cost_price' => 2050.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 6,
                'min_stock_alert' => 1,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 310.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'energia-solar-baterias',
                'sku' => 'PANEL-550W-MONO',
                'slug' => 'panel-solar-monocristalino-550w',
                'name' => 'Panel Solar Monocristalino Tier-1 550W Half-Cell Alta Eficiencia',
                'short_description' => 'Módulo fotovoltaico de 144 celdas partidas monocristalinas PERC con eficiencia del 21.5% y marco de aluminio anodizado.',
                'description' => "Excelente rendimiento en condiciones de alta temperatura y radiación solar tropical. Vidrio templado antirreflectante de 3.2 mm y caja de conexiones IP68 con diodos bypass.",
                'price' => 195.00,
                'sale_price' => 175.00,
                'cost_price' => 125.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 60,
                'min_stock_alert' => 10,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 27.50,
                'is_featured' => false,
            ],
            [
                'category_slug' => 'energia-solar-baterias',
                'sku' => 'BAT-RACK-48V100',
                'slug' => 'bateria-litio-lifepo4-48v-100ah',
                'name' => 'Batería de Litio LiFePO4 48V 100Ah (5.12 kWh) Montaje en Rack',
                'short_description' => 'Módulo de almacenamiento solar de última generación con comunicación CAN/RS485 compatible con inversores Deye, Growatt y SRNE.',
                'description' => "Más de 6000 ciclos de vida útil al 80% DoD. Permite conexión en paralelo de hasta 15 módulos para alcanzar 76.8 kWh de reserva energética industrial.",
                'price' => 1350.00,
                'sale_price' => 1280.00,
                'cost_price' => 980.00,
                'inventory_type' => 'on_demand',
                'stock_quantity' => 0,
                'min_stock_alert' => 0,
                'estimated_delivery_days' => '15 a 20 días hábiles (Despacho Mariel)',
                'weight_kg' => 46.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'energia-solar-baterias',
                'sku' => 'INV-HIB-3500-24',
                'slug' => 'inversor-hibrido-cargador-3500w-24v',
                'name' => 'Inversor Cargador Solar Híbrido 3.5kW 24V MPPT 100A',
                'short_description' => 'Inversor inteligente de onda senoidal pura con regulador solar MPPT de hasta 500V DC y cargador AC de red.',
                'description' => "Funciona con o sin baterías. Soporta entrada de paneles de hasta 4000W y permite programar prioridad solar, batería o red eléctrica.",
                'price' => 690.00,
                'sale_price' => null,
                'cost_price' => 510.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 9,
                'min_stock_alert' => 2,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 11.20,
                'is_featured' => false,
            ],

            // Categ 3: Generadores y Motores
            [
                'category_slug' => 'generadores-motores',
                'sku' => 'GEN-DIESEL-12KVA',
                'slug' => 'planta-electrica-diesel-12kva-insonorizada',
                'name' => 'Planta Eléctrica Diésel Insonorizada 12kVA 110V/220V Bifásica',
                'short_description' => 'Generador diésel cabinado super silencioso con módulo ATS de arranque automático ante fallos de red eléctrica.',
                'description' => "Equipo para servicio continuo o de emergencia en comercios, panaderías, clínicas y residencias. Motor diésel de 4 tiempos refrigerado por aire forzado, tanque de 30 litros con 12 horas de autonomía continua y panel digital multifunción con voltímetro, horómetro y frecuencímetro.",
                'price' => 3800.00,
                'sale_price' => 3450.00,
                'cost_price' => 2650.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 4,
                'min_stock_alert' => 1,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 235.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'generadores-motores',
                'sku' => 'GEN-INV-3500W',
                'slug' => 'generador-inverter-gasolina-3500w',
                'name' => 'Generador Inverter a Gasolina 3.5kW Ultrasilencioso 58dB',
                'short_description' => 'Planta eléctrica inverter portátil con tecnología de onda senoidal limpia apta para aires acondicionados y equipos electrónicos sensibles.',
                'description' => "Consumo ultra bajo gracias al modo Eco. Arranque eléctrico con botón y mando a distancia, salida USB para carga rápida de teléfonos, ruedas de transporte y asa telescópica.",
                'price' => 880.00,
                'sale_price' => 790.00,
                'cost_price' => 590.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 14,
                'min_stock_alert' => 2,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 32.00,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'generadores-motores',
                'sku' => 'BOMBA-RIEGO-3IN',
                'slug' => 'motobomba-alta-presion-3-pulgadas-7hp',
                'name' => 'Motobomba de Riego y Alta Presión 3 Pulgadas 7HP Gasolina',
                'short_description' => 'Bomba de agua centrífuga autocebante para fincas, agricultura y vaciado de cisternas.',
                'description' => "Caudal máximo de 60.000 litros/hora con altura manométrica de 30 metros. Chasis tubular de protección contra impactos y sensor de bajo nivel de aceite.",
                'price' => 360.00,
                'sale_price' => null,
                'cost_price' => 260.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 28.00,
                'is_featured' => false,
            ],

            // Categ 4: Equipamiento Industrial & Ferretería
            [
                'category_slug' => 'equipamiento-industrial',
                'sku' => 'SOLD-MIG-250A',
                'slug' => 'soldadora-inverter-mig-tig-mma-250a',
                'name' => 'Máquina de Soldar Inverter MIG/MAG/TIG/MMA 250A Multiproceso',
                'short_description' => 'Soldadora digital multiproceso con soporte de alambre sin gas (Flux Core) y con gas. Conexión 110V/220V automática.',
                'description' => "Ideal para talleres de pailería y mantenimiento automotriz. Ajuste sinérgico de voltaje y velocidad de alambre, tecnología IGBT de última generación y antorcha Euro-conector incluida.",
                'price' => 480.00,
                'sale_price' => 420.00,
                'cost_price' => 310.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 16,
                'min_stock_alert' => 3,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 14.50,
                'is_featured' => false,
            ],
            [
                'category_slug' => 'equipamiento-industrial',
                'sku' => 'HIDRO-3200-PSI',
                'slug' => 'hidrolavadora-industrial-3200-psi',
                'name' => 'Hidrolavadora Industrial de Alta Presión 3200 PSI con Motor Gasolina',
                'short_description' => 'Lavadora de presión para autolavados, limpieza de maquinaria pesada y fachadas comerciales.',
                'description' => "Bomba axial de latón de alta durabilidad, 5 boquillas de conexión rápida con diferentes ángulos de pulverización y manguera de alta resistencia de 8 metros.",
                'price' => 650.00,
                'sale_price' => null,
                'cost_price' => 470.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 7,
                'min_stock_alert' => 1,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 34.00,
                'is_featured' => false,
            ],
            [
                'category_slug' => 'equipamiento-industrial',
                'sku' => 'COMP-100L-3HP',
                'slug' => 'compresor-aire-100l-3hp-bicilindrico',
                'name' => 'Compresor de Aire Profesional 100 Litros 3HP Bicilíndrico 115 PSI',
                'short_description' => 'Compresor de transmisión por correa con calderín reforzado para talleres de pintura y llantas.',
                'description' => "Cabezal de hierro fundido con doble pistón para disipación térmica superior, presostato automático de seguridad y doble salida regulada de aire comprimido.",
                'price' => 580.00,
                'sale_price' => null,
                'cost_price' => 410.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 5,
                'min_stock_alert' => 1,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 68.00,
                'is_featured' => false,
            ],

            // Categ 5: Repuestos & Mantenimiento STI
            [
                'category_slug' => 'repuestos-sti',
                'sku' => 'LLANTA-120-70-12',
                'slug' => 'neumatico-tubeless-120-70-12-moto',
                'name' => 'Neumático Tubeless Reforzado 120/70-12 Pista / Ciudad 6 Lonas',
                'short_description' => 'Goma reforzada para motos eléctricas con compuesto antideslizante para asfalto mojado.',
                'description' => "Carcasa reforzada de 6 lonas de nailon para resistir baches e impactos. Banda de rodadura con surcos profundos de evacuación rápida de agua.",
                'price' => 45.00,
                'sale_price' => null,
                'cost_price' => 28.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 40,
                'min_stock_alert' => 5,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 3.80,
                'is_featured' => false,
            ],
            [
                'category_slug' => 'repuestos-sti',
                'sku' => 'CARG-72V-5A-DIG',
                'slug' => 'cargador-rapido-inteligente-72v-5a',
                'name' => 'Cargador Rápido Inteligente 72V 5A con Display Digital y Ventilador',
                'short_description' => 'Cargador con corte automático por temperatura para baterías de litio y plomo-gel.',
                'description' => "Carga un 40% más rápido que los cargadores comunes. Display LCD integrado que indica el voltaje de entrega, amperaje en tiempo real y porcentaje de carga acumulada.",
                'price' => 65.00,
                'sale_price' => null,
                'cost_price' => 39.00,
                'inventory_type' => 'controlled_stock',
                'stock_quantity' => 35,
                'min_stock_alert' => 5,
                'estimated_delivery_days' => '24 a 48 horas',
                'weight_kg' => 1.10,
                'is_featured' => false,
            ],
        ];

        foreach ($productsData as $pData) {
            $catSlug = $pData['category_slug'];
            unset($pData['category_slug']);

            $category = $categories[$catSlug] ?? null;
            if (!$category) {
                continue;
            }

            ShopProduct::updateOrCreate(
                ['slug' => $pData['slug']],
                array_merge($pData, [
                    'category_id' => $category->id,
                    'is_active' => true,
                    'seo_title' => $pData['name'] . ' | Entrega en Cuba USD',
                    'seo_description' => Str::limit($pData['short_description'], 155),
                ])
            );
        }

        // =========================================================================
        // 4. ZONAS Y TARIFAS DE ENVÍO OFICIALES EN CUBA (USD)
        // =========================================================================
        $shippingZonesData = [
            [
                'name' => 'La Habana',
                'provinces' => ['La Habana'],
                'rates' => [
                    [
                        'name' => 'Entrega Puerta a Puerta Estándar (La Habana)',
                        'price' => 25.00,
                        'estimated_days' => '24 a 48 horas',
                    ],
                    [
                        'name' => 'Entrega Express Prioritaria Habana',
                        'price' => 40.00,
                        'estimated_days' => '12 a 24 horas',
                    ],
                ],
            ],
            [
                'name' => 'Occidente (Artemisa, Mayabeque, Matanzas, Pinar del Río)',
                'provinces' => ['Artemisa', 'Mayabeque', 'Matanzas', 'Pinar del Río'],
                'rates' => [
                    [
                        'name' => 'Envío Provincial Directo Occidente',
                        'price' => 45.00,
                        'estimated_days' => '48 a 72 horas',
                    ],
                ],
            ],
            [
                'name' => 'Centro (Cienfuegos, Villa Clara, Sancti Spíritus, Ciego de Ávila, Camagüey)',
                'provinces' => ['Cienfuegos', 'Villa Clara', 'Sancti Spíritus', 'Ciego de Ávila', 'Camagüey'],
                'rates' => [
                    [
                        'name' => 'Envío Interprovincial Región Central',
                        'price' => 75.00,
                        'estimated_days' => '3 a 5 días hábiles',
                    ],
                ],
            ],
            [
                'name' => 'Oriente (Las Tunas, Holguín, Granma, Santiago de Cuba, Guantánamo)',
                'provinces' => ['Las Tunas', 'Holguín', 'Granma', 'Santiago de Cuba', 'Guantánamo'],
                'rates' => [
                    [
                        'name' => 'Envío Nacional Región Oriental',
                        'price' => 95.00,
                        'estimated_days' => '4 a 6 días hábiles',
                    ],
                ],
            ],
            [
                'name' => 'Retiro Presencial en Almacén HUB Mariel',
                'provinces' => ['Artemisa', 'La Habana', 'Mayabeque', 'Matanzas', 'Pinar del Río', 'Villa Clara', 'Cienfuegos', 'Sancti Spíritus', 'Ciego de Ávila', 'Camagüey', 'Las Tunas', 'Holguín', 'Granma', 'Santiago de Cuba', 'Guantánamo', 'Isla de la Juventud'],
                'rates' => [
                    [
                        'name' => 'Retiro Personal Gratuito en Almacén HUB Mariel',
                        'price' => 0.00,
                        'estimated_days' => 'Inmediato (Previa coordinación de retiro)',
                    ],
                ],
            ],
        ];

        foreach ($shippingZonesData as $zoneData) {
            $rates = $zoneData['rates'];
            unset($zoneData['rates']);

            $zone = ShopShippingZone::updateOrCreate(
                ['name' => $zoneData['name']],
                [
                    'provinces' => $zoneData['provinces'],
                    'is_active' => true,
                ]
            );

            foreach ($rates as $rateData) {
                ShopShippingRate::updateOrCreate(
                    [
                        'zone_id' => $zone->id,
                        'name' => $rateData['name'],
                    ],
                    [
                        'price' => $rateData['price'],
                        'estimated_days' => $rateData['estimated_days'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // =========================================================================
        // 5. CUPONES DE PROMOCIÓN COMERCIAL EN USD
        // =========================================================================
        ShopCoupon::updateOrCreate(
            ['code' => 'BIENVENIDA10'],
            [
                'name' => 'Descuento de Bienvenida 10%',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_order_amount' => 50.00,
                'max_discount_amount' => 200.00,
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addMonths(6),
                'usage_limit' => 500,
                'used_count' => 0,
                'is_active' => true,
            ]
        );

        ShopCoupon::updateOrCreate(
            ['code' => 'PROMOENVIOS'],
            [
                'name' => 'Bono de Descuento en Flete USD $20',
                'discount_type' => 'fixed',
                'discount_value' => 20.00,
                'min_order_amount' => 150.00,
                'max_discount_amount' => 20.00,
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addMonths(6),
                'usage_limit' => 300,
                'used_count' => 0,
                'is_active' => true,
            ]
        );
    }
}
