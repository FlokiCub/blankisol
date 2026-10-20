<?php

namespace App\Http\Controllers;

use App\Models\TechServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TechServiceController extends Controller
{
    /**
     * Show the commercial landing page for Servicios Tecnológicos Integrales.
     */
    public function index()
    {
        $categories = $this->getCatalogCategories();
        $sectors = $this->getSectors();

        return view('tech_services.index', compact('categories', 'sectors'));
    }

    /**
     * Process quote request for technological services.
     */
    public function storeQuoteRequest(Request $request)
    {
        // Simple honeypot check against bot spam
        if ($request->filled('empresa_url_check')) {
            return back()->with('tech_success', 'Su solicitud ha sido recibida correctamente.');
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['required', 'string', 'max:50'],
            'servicio_interes' => ['required', 'string', 'max:200'],
            'tipo_instalacion' => ['nullable', 'string', 'max:200'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'superficie_volumen' => ['nullable', 'string', 'max:100'],
            'nivel_complejidad' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['required', 'string', 'min:5', 'max:3000'],
            'plazo_deseado' => ['nullable', 'string', 'max:100'],
            'aceptado_privacidad' => ['accepted'],
        ], [
            'nombre.required' => 'Por favor introduzca su nombre y apellidos.',
            'email.required' => 'El correo electrónico es indispensable para remitir la propuesta técnica.',
            'email.email' => 'Introduzca una dirección de correo electrónico válida.',
            'telefono.required' => 'Indique un número telefónico o WhatsApp de contacto.',
            'servicio_interes.required' => 'Seleccione el servicio o categoría técnica de su interés.',
            'descripcion.required' => 'Describa brevemente la necesidad o alcance preliminar del servicio.',
            'aceptado_privacidad.accepted' => 'Debe aceptar los términos de tratamiento de datos para procesar la solicitud.',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['aceptado_privacidad'] = true;

        $record = TechServiceRequest::create($validated);

        // Document requirements for notification: Logged locally until a corporate mail server is provided
        Log::info('Nueva solicitud comercial de Servicios Tecnológicos Integrales registrada.', [
            'id' => $record->id,
            'nombre' => $record->nombre,
            'empresa' => $record->empresa,
            'servicio' => $record->servicio_interes,
            'telefono' => $record->telefono,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Solicitud recibida! Nuestro equipo técnico-comercial evaluará su requerimiento para preparar un presupuesto personalizado.',
                'data' => [
                    'id' => $record->id,
                    'servicio' => $record->servicio_interes,
                ],
            ]);
        }

        return redirect()->to(url()->previous() . '#solicitar-presupuesto')
            ->with('tech_success', '¡Gracias por confiar en BLANKISOL! Su solicitud de presupuesto para Servicios Tecnológicos Integrales ha sido registrada correctamente. Un asesor técnico se comunicará con usted a la brevedad.');
    }

    /**
     * Definitive Catalog of Services structured by official categories.
     */
    public function getCatalogCategories(): array
    {
        return [
            'cat_a' => [
                'id' => 'cat_a',
                'name' => 'Categoría A. Limpieza e higienización',
                'short_name' => 'Limpieza e Higienización',
                'icon' => 'fa-solid fa-pump-medical',
                'description' => 'Tratamientos profundos de saneamiento, desinfección preventiva y recuperación de superficies e interiores.',
                'services' => [
                    [
                        'id' => 'higienizacion-equipos-areas',
                        'name' => 'Servicios de higienización de equipos y áreas generales',
                        'description' => 'Desinfección e higienización profunda de áreas de trabajo y equipamiento operativo para asegurar condiciones sanitarias óptimas.',
                        'applicability' => 'Oficinas, talleres, naves operativas y equipos mecánicos.',
                        'unit' => 'm² / Área / Equipo',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'limpieza-naves-almacenes',
                        'name' => 'Limpieza, desinfección e higienización de naves para almacenes generales',
                        'description' => 'Saneamiento integral de grandes volúmenes de almacenamiento, estructuras de techumbre y pavimentos industriales.',
                        'applicability' => 'Almacenes secos, centros de acopio y naves logísticas.',
                        'unit' => 'm²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-superficies-techos-paredes',
                        'name' => 'Limpieza especializada de superficies, techos, paredes y pisos',
                        'description' => 'Tratamiento técnico desengrasante y decapado para erradicar adherencias, grasa y residuos en paramentos.',
                        'applicability' => 'Instalaciones industriales, pisos epóxicos y paredes lavables.',
                        'unit' => 'm²',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'restauracion-muebles-alfombras',
                        'name' => 'Limpieza y restauración de muebles y alfombras',
                        'description' => 'Lavado profundo con inyección-extracción y productos protectores de fibras para mobiliario y alfombrados.',
                        'applicability' => 'Hotelería, centros de convenciones y sedes corporativas.',
                        'unit' => 'Unidad / m²',
                        'complexity' => 'Estándar',
                    ],
                ],
            ],
            'cat_b' => [
                'id' => 'cat_b',
                'name' => 'Categoría B. Sistemas de extracción y cocinas industriales',
                'short_name' => 'Extracción y Cocinas',
                'icon' => 'fa-solid fa-utensils',
                'description' => 'Mantenimiento preventivo y desengrase de alta exigencia para campanas, ductos y tecnología gastronómica.',
                'services' => [
                    [
                        'id' => 'limpieza-extraccion-inyeccion',
                        'name' => 'Limpieza profunda y mantenimiento de sistemas mecánicos de extracción e inyección',
                        'description' => 'Desengrase técnico de campanas, ductos horizontales y verticales, turbinas y filtros de aire.',
                        'applicability' => 'Cocinas de hoteles, restaurantes, hospitales e industrias alimentarias.',
                        'unit' => 'ml / Sistema',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'montaje-desmontaje-extraccion',
                        'name' => 'Montaje y desmontaje de sistemas de extracción e inyección en cocinas industriales',
                        'description' => 'Desarme controlado para intervención exhaustiva o renovación de componentes de ventilación gastronómica.',
                        'applicability' => 'Campanas y ducterías gastronómicas de mediana y gran escala.',
                        'unit' => 'Servicio técnico',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'limpieza-equipos-coccion',
                        'name' => 'Limpieza de equipos de cocción',
                        'description' => 'Decapado térmico y químico de planchas, freidoras, marmitas, hornos y quemadores de uso continuo.',
                        'applicability' => 'Bloques de cocción gastronómica profesional.',
                        'unit' => 'Unidad',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'revision-reparacion-cocinas',
                        'name' => 'Revisión y reparación de sistemas técnicos de cocinas',
                        'description' => 'Mantenimiento correctivo puntual en sistemas de impulsión, correas, sellos y ventiladores asociados a la extracción.',
                        'applicability' => 'Instalaciones técnicas gastronómicas.',
                        'unit' => 'Servicio técnico',
                        'complexity' => 'A evaluar',
                    ],
                ],
            ],
            'cat_c' => [
                'id' => 'cat_c',
                'name' => 'Categoría C. Redes hidráulicas y saneamiento',
                'short_name' => 'Redes y Saneamiento',
                'icon' => 'fa-solid fa-faucet-drip',
                'description' => 'Desobstrucción, destupición técnica y mantenimiento sanitario de conducciones y trampas de retención.',
                'services' => [
                    [
                        'id' => 'destupicion-redes-hidraulico-sanitario',
                        'name' => 'Limpieza y destupición de redes de los sistemas hidráulico y sanitario',
                        'description' => 'Sondeo y desobstrucción hidromecánica de tuberías interiores y bajantes sanitarias.',
                        'applicability' => 'Edificaciones comerciales, hoteleras y centros de producción.',
                        'unit' => 'ml',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'destupicion-trampas-grasa-alcantarillados',
                        'name' => 'Limpieza, destupición y acarreo de sólidos en alcantarillados, atarjeas, trampas de grasa, registros y depósitos',
                        'description' => 'Vaciado, raspado de paredes, extracción de lodos/grasas y disposición final de sólidos con desinfección integral.',
                        'applicability' => 'Trampas de grasa comerciales, registros pluviales y alcantarillados.',
                        'unit' => 'm³ / Registro',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'limpieza-sistemas-drenaje',
                        'name' => 'Limpieza de sistemas de drenaje',
                        'description' => 'Remoción de sedimentos, hojas y restos minerales en canaletas perimetrales y zanjas de drenaje.',
                        'applicability' => 'Patios industriales, cubiertas y vías de acceso.',
                        'unit' => 'ml',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'limpieza-sistemas-bombeo',
                        'name' => 'Limpieza de sistemas de bombeo',
                        'description' => 'Mantenimiento de fosas de succión, descalcificación y acondicionamiento higiénico del entorno de bombas.',
                        'applicability' => 'Salas de bombas, cisternas y rebombeos.',
                        'unit' => 'Unidad',
                        'complexity' => 'Media',
                    ],
                ],
            ],
            'cat_d' => [
                'id' => 'cat_d',
                'name' => 'Categoría D. Limpieza industrial especializada',
                'short_name' => 'Industrial Especializada',
                'icon' => 'fa-solid fa-gears',
                'description' => 'Intervenciones técnicas en maquinaria pesada, silos, líneas de proceso y estructuras de gran peralto.',
                'services' => [
                    [
                        'id' => 'limpieza-maquinaria-industrial',
                        'name' => 'Limpieza de maquinaria industrial',
                        'description' => 'Descontaminación minuciosa de motores, bancadas, sistemas de engranaje y chasis industriales.',
                        'applicability' => 'Plantas de manufactura, molinos y talleres pesados.',
                        'unit' => 'Equipo / Unidad',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-silos-tecnologicos',
                        'name' => 'Limpieza de silos tecnológicos',
                        'description' => 'Acceso vertical y aspirado de residuos secos, harinas, granos o polvos industriales adheridos.',
                        'applicability' => 'Silos de almacenamiento vertical y tolvas de dosificación.',
                        'unit' => 'm³ / Unidad',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'limpieza-rodillos-esteras-envasado',
                        'name' => 'Limpieza de rodillos, esteras, líneas de soplado y envasado',
                        'description' => 'Higienización de líneas continuas sin agredir materiales plásticos o guías de precisión mecánica.',
                        'applicability' => 'Fábricas de bebidas, embotelladoras y empaque continuo.',
                        'unit' => 'ml / Línea',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-contenedores-colectores-cisternas',
                        'name' => 'Limpieza de contenedores, colectores, cisternas y piscinas',
                        'description' => 'Extracción de sedimentos, desincrustado de algas y desinfección apta para depósitos de agua.',
                        'applicability' => 'Cisternas de agua potable, estanques técnicos y piscinas.',
                        'unit' => 'm³ / m²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-fachadas-elementos-arquitectonicos',
                        'name' => 'Limpieza de fachadas y elementos arquitectónicos',
                        'description' => 'Lavado técnico a presión regulada de cristales, mármol, piedra natural y paneles compuestos.',
                        'applicability' => 'Edificios institucionales, hoteles y frentes comerciales.',
                        'unit' => 'm²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-conjuntos-monumentales',
                        'name' => 'Limpieza de conjuntos monumentales y escultóricos',
                        'description' => 'Tratamiento no abrasivo respetuoso con el patrimonio histórico, bronces, granitos y cantería.',
                        'applicability' => 'Monumentos urbanos, plazas y elementos escultóricos.',
                        'unit' => 'm² / Conjunto',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'limpieza-estructuras-metalicas-porticos',
                        'name' => 'Limpieza de estructuras metálicas de gran peralto y pórticos',
                        'description' => 'Trabajo vertical en altura con equipo de protección para despolvar vigas, cerchas y puentes grúa.',
                        'applicability' => 'Naves industriales, pórticos y terminales logísticas.',
                        'unit' => 'ml / m²',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'limpieza-transportes',
                        'name' => 'Limpieza de transportes',
                        'description' => 'Lavado, desengrase y acondicionamiento higiénico de flotas de carga, furgones y camiones tolva.',
                        'applicability' => 'Flotas vehiculares pesadas y furgones térmicos.',
                        'unit' => 'Unidad',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'limpieza-condensadores-torres-enfriamiento',
                        'name' => 'Limpieza de condensadores evaporativos y torres de enfriamiento',
                        'description' => 'Remoción de biopelículas, sarro y lodos en paquetes de relleno y bandejas de condensación.',
                        'applicability' => 'Centrales de climatización y refrigeración industrial.',
                        'unit' => 'Unidad',
                        'complexity' => 'Alta complejidad',
                    ],
                ],
            ],
            'cat_e' => [
                'id' => 'cat_e',
                'name' => 'Categoría E. Energía y superficies especiales',
                'short_name' => 'Energía y Superficies',
                'icon' => 'fa-solid fa-solar-panel',
                'description' => 'Mantenimiento de generación solar fotovoltaica, remoción de sales y áreas refrigeradas.',
                'services' => [
                    [
                        'id' => 'limpieza-desincrustacion-paneleria-solar',
                        'name' => 'Limpieza y desincrustación de panelería solar',
                        'description' => 'Lavado con agentes neutros y agua desmineralizada para maximizar el rendimiento energético fotovoltaico sin rayar el vidrio.',
                        'applicability' => 'Parques solares, cubiertas comerciales y kits solares residenciales.',
                        'unit' => 'm² / Módulo',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'desincrustacion-sales-calcio-magnesio',
                        'name' => 'Desincrustación de sales de calcio y magnesio en superficies',
                        'description' => 'Tratamiento químico regulado para eliminar incrustaciones calcáreas severas en intercambiadores y superficies expuestas.',
                        'applicability' => 'Sistemas térmicos, líneas de vapor y cerámicas de proceso.',
                        'unit' => 'm²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-camaras-frias-neveras',
                        'name' => 'Limpieza, desinfección e higienización de cámaras frías y neveras industriales',
                        'description' => 'Intervención compatible con temperaturas bajo cero, eliminando escarchas contaminadas y sanitizando paneles aislantes.',
                        'applicability' => 'Cámaras frigoríficas, almacenes congelados y obradores.',
                        'unit' => 'm³ / m²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'limpieza-desincrustacion-torres-enfriamiento-e',
                        'name' => 'Limpieza y desincrustación de torres de enfriamiento',
                        'description' => 'Decapado mineral de toberas, eliminadores de gotas y circuitos de recirculación.',
                        'applicability' => 'Plantas energéticas e instalaciones frigoríficas industriales.',
                        'unit' => 'Unidad',
                        'complexity' => 'Alta complejidad',
                    ],
                ],
            ],
            'cat_f' => [
                'id' => 'cat_f',
                'name' => 'Categoría F. Construcción, mantenimiento y servicios complementarios',
                'short_name' => 'Construcción y Mantenimiento',
                'icon' => 'fa-solid fa-helmet-safety',
                'description' => 'Obras civiles, mantenimiento correctivo de inmuebles, terminaciones e instalaciones especializadas.',
                'scope_note' => 'Actividades desarrolladas conforme a la disponibilidad operativa de equipos y brigadas técnicas.',
                'services' => [
                    [
                        'id' => 'construccion-civil-montaje',
                        'name' => 'Construcción civil y montaje de obras',
                        'description' => 'Ejecución de trabajos de albañilería, cimentaciones ligeras y montaje de estructuras auxiliares.',
                        'applicability' => 'Instalaciones comerciales, naves auxiliares y reformas.',
                        'unit' => 'Presupuesto por alcance',
                        'complexity' => 'A evaluar',
                    ],
                    [
                        'id' => 'demolicion-desmontaje',
                        'name' => 'Demolición y desmontaje',
                        'description' => 'Retirada controlada de tabiquería, falsos techos, cerramientos y estructuras en desuso con acarreo.',
                        'applicability' => 'Espacios en fase de reconversión y reformas.',
                        'unit' => 'm³ / m²',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'remodelacion-restauracion-rehabilitacion',
                        'name' => 'Remodelación, restauración, reconstrucción y rehabilitación',
                        'description' => 'Renovación de inmuebles empresariales, locales de venta y espacios de atención al público.',
                        'applicability' => 'Sedes corporativas, locales y edificaciones patrimoniales.',
                        'unit' => 'Proyecto',
                        'complexity' => 'Alta complejidad',
                    ],
                    [
                        'id' => 'reparacion-mantenimiento-constructivo',
                        'name' => 'Reparación y mantenimiento constructivo',
                        'description' => 'Subsanación de grietas, impermeabilización de cubiertas, enmasillado y sellado de juntas.',
                        'applicability' => 'Cubiertas transitables, muros perimetrales y losas.',
                        'unit' => 'm²',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'terminacion-acabado-edificaciones',
                        'name' => 'Terminación y acabado de edificaciones',
                        'description' => 'Pintura técnica de interiores y exteriores, masillas de acabado y colocación de rodapiés.',
                        'applicability' => 'Edificios en fase de entrega o puesta en marcha.',
                        'unit' => 'm²',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'instalaciones-electricas-fontaneria-clima',
                        'name' => 'Instalaciones eléctricas, fontanería y climatización',
                        'description' => 'Revisión y adecuación de acometidas eléctricas, canalizaciones de agua y mantenimiento de split/consolas.',
                        'applicability' => 'Oficinas, cuartos técnicos y locales comerciales.',
                        'unit' => 'Punto / Servicio',
                        'complexity' => 'Media',
                    ],
                    [
                        'id' => 'jardineria-areas-verdes-paisajismo',
                        'name' => 'Jardinería, mantenimiento de áreas verdes y paisajismo',
                        'description' => 'Poda controlada, corte de césped, desyerbe y mantenimiento de jardines corporativos.',
                        'applicability' => 'Entornos de sedes empresariales, parques y perímetros de naves.',
                        'unit' => 'm²',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'carpinteria-trabajos-asociados',
                        'name' => 'Carpintería y trabajos especializados asociados',
                        'description' => 'Ajuste, fabricación y reparación de puertas, marcos, estanterías y mobiliario a medida.',
                        'applicability' => 'Instalaciones de almacenamiento y oficinas.',
                        'unit' => 'Servicio / Pieza',
                        'complexity' => 'Estándar',
                    ],
                    [
                        'id' => 'materiales-elementos-constructivos',
                        'name' => 'Producción y comercialización de materiales constructivos',
                        'description' => 'Suministro de componentes y elementos constructivos bajo disponibilidad comercial efectiva.',
                        'applicability' => 'Proyectos contratados con provisión de materiales.',
                        'unit' => 'Según suministro',
                        'complexity' => 'A coordinar',
                    ],
                    [
                        'id' => 'transporte-manipulacion-carga',
                        'name' => 'Transporte de carga por carretera y manipulación de carga',
                        'description' => 'Traslado de escombros, materiales y maquinarias asociado a servicios técnicos y obras contratadas.',
                        'applicability' => 'Logística de soporte a brigadas operativas.',
                        'unit' => 'Viaje / Ton',
                        'complexity' => 'Operativo',
                    ],
                ],
            ],
        ];
    }

    /**
     * Sectors targeted by the technical services division.
     */
    public function getSectors(): array
    {
        return [
            ['name' => 'Hoteles', 'icon' => 'fa-solid fa-hotel', 'desc' => 'Cocinas, áreas públicas, cámaras frigoríficas y textiles.'],
            ['name' => 'Industrias', 'icon' => 'fa-solid fa-industry', 'desc' => 'Maquinaria pesada, silos, líneas continuas y naves fabriles.'],
            ['name' => 'Inmuebles', 'icon' => 'fa-solid fa-building', 'desc' => 'Edificios corporativos, fachadas, redes hidrosanitarias y mantenimiento.'],
            ['name' => 'Escuelas', 'icon' => 'fa-solid fa-graduation-cap', 'desc' => 'Comedores, aulas, áreas recreativas e higiene integral.'],
            ['name' => 'Aeropuertos', 'icon' => 'fa-solid fa-plane-departure', 'desc' => 'Terminales de pasajeros, hangares, ductería y pistas de carga.'],
            ['name' => 'Hospitales', 'icon' => 'fa-solid fa-hospital', 'desc' => 'Higienización profunda de áreas de soporte, cocinas y salas técnicas.'],
            ['name' => 'Transportes', 'icon' => 'fa-solid fa-truck-moving', 'desc' => 'Flotas de carga pesada, tolvas, furgones y terminales.'],
            ['name' => 'Parques', 'icon' => 'fa-solid fa-tree', 'desc' => 'Áreas verdes, mobiliario exterior, canalizaciones y senderos.'],
            ['name' => 'Tanques y cisternas', 'icon' => 'fa-solid fa-oil-well', 'desc' => 'Depósitos de agua, reservorios técnicos y desinfección sanitaria.'],
            ['name' => 'Instalaciones comerciales', 'icon' => 'fa-solid fa-shop', 'desc' => 'Centros de compras, almacenes minoristas y locales de servicio.'],
        ];
    }
}
