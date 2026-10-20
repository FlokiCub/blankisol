@extends('layouts.public')

@section('title', 'Servicios Tecnológicos Integrales | BLANKISOL SURL')
@section('meta_description', 'Servicios Tecnológicos Integrales de BLANKISOL SURL: Limpieza industrial especializada, higienización, mantenimiento de sistemas de extracción, redes, climatización y obras.')

@section('content')
    {{-- Header / Navegación Corporativa --}}
    @include('partials.landing.navbar')

    {{-- Hero Section STI --}}
    <section class="relative pt-12 pb-20 lg:pt-20 lg:pb-28 overflow-hidden bg-gradient-corporate-hero border-b border-[#cbcfd4]/10">
        {{-- Elementos Lumínicos de Fondo --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[900px] h-[400px] bg-[#0a3166]/40 blur-[140px] pointer-events-none -z-10"></div>
        <div class="absolute bottom-0 right-10 w-96 h-96 bg-[#f5782b]/8 blur-[130px] pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center space-y-6">
                {{-- Slogan Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#f5782b]/15 border border-[#f5782b]/30 text-[#ff934f] text-xs font-bold uppercase tracking-wider shadow-sm shadow-[#f5782b]/20">
                    <i class="fa-solid fa-award text-sm"></i>
                    <span>Credibilidad y garantía es nuestra meta</span>
                </div>

                {{-- H1 Principal --}}
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Limpieza especializada, mantenimiento y <span class="text-gradient-accent">soluciones integrales</span> para instalaciones empresariales
                </h1>

                {{-- Párrafo Descriptivo --}}
                <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-3xl mx-auto">
                    División técnica de <strong class="text-white font-semibold">BLANKISOL SURL</strong> dedicada a la recuperación, saneamiento, mantenimiento técnico e intervenciones constructivas para industrias, instalaciones hoteleras, naves de almacenaje y edificios corporativos.
                </p>

                {{-- CTAs Principales --}}
                <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                    <a href="#catalogo" class="btn-corporate-primary">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Explorar Catálogo Técnico</span>
                    </a>
                    <a href="#solicitar-presupuesto" class="btn-corporate-secondary">
                        <i class="fa-solid fa-file-invoice-dollar text-[#f5782b]"></i>
                        <span>Solicitar Presupuesto</span>
                    </a>
                </div>

                {{-- Indicadores de Confianza / Pilares --}}
                <div class="pt-10 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <div class="p-4 rounded-2xl bg-[#0a254e]/50 border border-[#cbcfd4]/15 backdrop-blur-sm text-center">
                        <span class="block text-2xl font-black text-[#ff934f]">6</span>
                        <span class="text-xs text-slate-300 font-medium">Categorías Especializadas</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#0a254e]/50 border border-[#cbcfd4]/15 backdrop-blur-sm text-center">
                        <span class="block text-2xl font-black text-white">100%</span>
                        <span class="text-xs text-slate-300 font-medium">Cotizaciones a Medida</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#0a254e]/50 border border-[#cbcfd4]/15 backdrop-blur-sm text-center">
                        <span class="block text-2xl font-black text-[#ff934f]">B2B</span>
                        <span class="text-xs text-slate-300 font-medium">Empresas e Instituciones</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#0a254e]/50 border border-[#cbcfd4]/15 backdrop-blur-sm text-center">
                        <span class="block text-2xl font-black text-white"><i class="fa-solid fa-shield-halved text-emerald-400"></i></span>
                        <span class="text-xs text-slate-300 font-medium">Seguridad Operativa</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Sección Quiénes Somos / Identidad STI --}}
    <section id="nosotros-sti" class="py-20 bg-[#051f42] border-b border-[#cbcfd4]/10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                {{-- Columna Izquierda: Introducción y Misión/Visión --}}
                <div class="lg:col-span-7 space-y-8">
                    <div>
                        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-[#f5782b] mb-3">
                            <span class="w-2 h-2 rounded-full bg-[#f5782b]"></span>
                            <span>División Técnica Especializada</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            Soluciones de alta exigencia para la continuidad y salubridad de sus instalaciones
                        </h2>
                        <p class="mt-4 text-slate-300 text-sm sm:text-base leading-relaxed">
                            Los Servicios Tecnológicos Integrales de BLANKISOL SURL responden a las demandas operativas más rigurosas de la industria y los servicios. Aplicamos procedimientos especializados para el desengrase profundo, la higienización técnica, la rehabilitación constructiva y la conservación de activos, previniendo incidentes y optimizando los costos de reposición.
                        </p>
                    </div>

                    {{-- Misión y Visión STI --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="p-6 rounded-2xl bg-[#0a254e]/60 border border-[#cbcfd4]/15 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-[#0a3166] text-[#f5782b] flex items-center justify-center text-lg mb-4">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h3 class="text-base font-bold text-white mb-2">Misión Técnica</h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Proveer intervenciones tecnológicas integrales en limpieza especializada, saneamiento e infraestructura que garanticen la máxima seguridad, operatividad y durabilidad de las instalaciones empresariales.
                            </p>
                        </div>

                        <div class="p-6 rounded-2xl bg-[#0a254e]/60 border border-[#cbcfd4]/15 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-[#0a3166] text-[#ff934f] flex items-center justify-center text-lg mb-4">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <h3 class="text-base font-bold text-white mb-2">Visión Corporativa</h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Ser la entidad líder en el mercado institucional e industrial por nuestro rigor de ejecución, compromiso medioambiental y confiabilidad técnica en cada intervención contratada.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Valores STI --}}
                <div class="lg:col-span-5">
                    <div class="p-8 rounded-3xl bg-gradient-to-br from-[#0a254e] to-[#020d1c] border border-[#cbcfd4]/20 shadow-xl space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-[#cbcfd4]/15">
                            <div class="w-10 h-10 rounded-xl bg-[#f5782b]/20 text-[#f5782b] flex items-center justify-center text-lg">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Principios y Valores</h3>
                                <p class="text-xs text-slate-400">Pilares de nuestra gestión operativa</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Calidad de ejecución</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Compromiso contractual</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Respeto al entorno</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Innovación técnica</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Responsabilidad</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Confidencialidad</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Honestidad comercial</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-slate-200">
                                <i class="fa-solid fa-circle-check text-[#f5782b]"></i>
                                <span>Seguridad laboral</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#051f42]/70 border border-[#f5782b]/30 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-[#f5782b] text-base mt-0.5"></i>
                            <p class="text-[11px] text-slate-300 leading-relaxed">
                                Cada proyecto cuenta con supervisión técnica en sitio, planificación coordinada de paradas de planta y protocolos de seguridad adaptados a normativas del sector.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Sección Metodología Operativa y Comparador Técnico de Resultados STI --}}
    <section id="metodologia-sti" class="py-20 bg-[#051f42]/95 border-b border-[#cbcfd4]/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-mesh pointer-events-none opacity-40 -z-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="badge-corporate-orange">
                    <i class="fa-solid fa-list-check"></i> Procedimiento Operativo
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    ¿Cómo Ejecutamos un Servicio Técnico en su Instalación?
                </h2>
                <p class="text-sm sm:text-base text-slate-300">
                    Garantizamos mínima interrupción operativa mediante planificación concertada, brigadas certificadas y protocolos estrictos de seguridad industrial.
                </p>
            </div>

            {{-- 4 Fases de Intervención Técnica --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                
                <div class="glass-panel-3d p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">01</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-clipboard-question"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">Diagnóstico en Sitio</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Inspección preliminar de metraje, estado de incrustación de grasas o sedimentos y evaluación de riesgos en altura o espacios confinados.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-[#ff934f] font-semibold">
                        Levantamiento técnico sin costo
                    </div>
                </div>

                <div class="glass-panel-3d p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">02</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">Plan de Parada</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Diseño del pliego de químicos biodegradables o vapor a emplear y coordinación de turnos nocturnos para evitar interrumpir la producción.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-[#ff934f] font-semibold">
                        Cero paro operacional no previsto
                    </div>
                </div>

                <div class="glass-panel-3d p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">03</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-helmet-safety"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">Ejecución Especializada</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Despliegue de cuadrilla equipada con EPI normativo, decapado mecánico, succión industrial de residuos y desinfección profunda.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved"></i> Seguridad industrial estricta
                    </div>
                </div>

                <div class="glass-panel-3d p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">04</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-certificate"></i>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-white mb-2">Certificación & Entrega</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Auditoría lumínica conjunta con el cliente, firma del acta de recepción conforme y emisión de constancia técnica para inspecciones.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-file-circle-check"></i> Acta de conformidad firmada
                    </div>
                </div>

            </div>

            {{-- Comparador Interactivo de Resultados Técnicos (Antes / Después) --}}
            <div class="glass-panel-3d p-6 sm:p-10 max-w-5xl mx-auto space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#cbcfd4]/15">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#f5782b]">Evidencia Operativa</span>
                        <h3 class="text-lg sm:text-xl font-bold text-white">Casos de Intervención Técnica & Recuperación</h3>
                    </div>

                    {{-- Selector de Casos --}}
                    <div class="flex items-center gap-2" id="results-case-selector">
                        <button type="button" 
                                data-case="cocinas" 
                                class="case-btn active px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-[#f5782b] text-white shadow-md">
                            1. Extracción Cocinas
                        </button>
                        <button type="button" 
                                data-case="pisos" 
                                class="case-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all bg-[#0a254e] text-slate-300 hover:text-white">
                            2. Pisos Industriales
                        </button>
                        <button type="button" 
                                data-case="tanques" 
                                class="case-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all bg-[#0a254e] text-slate-300 hover:text-white">
                            3. Tanques & Cisternas
                        </button>
                    </div>
                </div>

                {{-- Contenido Interactivo del Caso --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch" id="results-case-content">
                    
                    {{-- Estado Inicial --}}
                    <div class="p-6 rounded-2xl bg-[#020d1c]/80 border border-rose-500/30 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-md bg-rose-950/80 border border-rose-500/40 text-[10px] font-bold text-rose-300 uppercase tracking-wider">
                                    Estado Crítico Inicial
                                </span>
                                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>
                            </div>
                            <h4 id="case-before-title" class="text-base font-bold text-white mb-2">
                                Campana y Ductos con Grasa Petrificada
                            </h4>
                            <p id="case-before-desc" class="text-xs text-slate-300 leading-relaxed">
                                Acumulación excesiva de grasa combustible en ductos, campana y turbina. Riesgo inminente de ignición por llama de cocina, sobrecalentamiento del extractor y rechazo en auditoría sanitaria.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-rose-500/20 text-[11px] text-rose-300 font-mono">
                            <strong class="text-white">Riesgo:</strong> Incendio en ductería y paralización de cocina.
                        </div>
                    </div>

                    {{-- Resultado BLANKISOL --}}
                    <div class="p-6 rounded-2xl bg-[#020d1c]/80 border border-emerald-500/30 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-md bg-emerald-950/80 border border-emerald-500/40 text-[10px] font-bold text-emerald-300 uppercase tracking-wider">
                                    Resultado Técnico Entregado
                                </span>
                                <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                            </div>
                            <h4 id="case-after-title" class="text-base font-bold text-white mb-2">
                                Acabado Espejo Inoxidable & Desengrase Total
                            </h4>
                            <p id="case-after-desc" class="text-xs text-slate-300 leading-relaxed">
                                Aplicación de desengrasante técnico de inmersión y vapor a temperatura. Recuperación del flujo volumétrico de succión, turbina balanceada y certificado de desinfección expedido.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-emerald-500/20 text-[11px] text-emerald-300 font-mono">
                            <strong class="text-white">Garantía:</strong> Flujo óptimo + Cumplimiento sanitario 100%.
                        </div>
                    </div>

                </div>

                {{-- CTA al formulario con preselección --}}
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#cbcfd4]/10">
                    <span class="text-xs text-slate-300">
                        ¿Requiere evaluar una intervención semejante en sus instalaciones?
                    </span>
                    <a href="#solicitar-presupuesto" class="btn-corporate-primary text-xs !py-2.5 !px-5 whitespace-nowrap">
                        <i class="fa-solid fa-file-pen mr-1.5"></i>
                        <span>Solicitar Inspección Técnica</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    {{-- Catálogo de Servicios con Filtros --}}
    <section id="catalogo" class="py-20 bg-[#020d1c] border-b border-[#cbcfd4]/10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Encabezado de Sección --}}
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
                <span class="badge-corporate-orange">Portafolio Especializado</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Catálogo de Servicios Técnicos
                </h2>
                <p class="text-sm sm:text-base text-slate-300">
                    Explore las 6 categorías técnicas de intervención. Seleccione cualquier servicio para incluirlo automáticamente en su solicitud de presupuesto personalizada.
                </p>
            </div>

            {{-- Tabs / Filtros por Categoría --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12" id="catalog-filters" role="tablist">
                <button type="button" 
                        data-filter="all" 
                        class="filter-tab active px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 bg-[#f5782b] text-white shadow-md shadow-[#f5782b]/30 border border-[#f5782b]">
                    <i class="fa-solid fa-layer-group mr-1.5"></i> Todos los Servicios
                </button>
                @foreach($categories as $key => $cat)
                    <button type="button" 
                            data-filter="{{ $cat['id'] }}" 
                            class="filter-tab px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 bg-[#0a254e] text-slate-200 hover:bg-[#0a3166] hover:text-white border border-[#cbcfd4]/15">
                        <i class="{{ $cat['icon'] }} mr-1.5 text-[#ff934f]"></i> {{ $cat['short_name'] }}
                    </button>
                @endforeach
            </div>

            {{-- Contenedor de Servicios --}}
            <div class="space-y-16" id="catalog-container">
                @foreach($categories as $key => $cat)
                    <div class="category-block space-y-6" data-category-group="{{ $cat['id'] }}">
                        {{-- Cabecera de Categoría --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-5 rounded-2xl bg-[#051f42]/80 border border-[#cbcfd4]/15">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-[#0a3166] text-[#f5782b] flex items-center justify-center text-xl shrink-0">
                                    <i class="{{ $cat['icon'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ $cat['name'] }}</h3>
                                    <p class="text-xs text-slate-300">{{ $cat['description'] }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] text-slate-400 font-mono bg-[#020d1c] px-3 py-1.5 rounded-lg border border-[#cbcfd4]/10 self-start md:self-auto">
                                {{ count($cat['services']) }} servicios disponibles
                            </span>
                        </div>

                        {{-- Grid de Tarjetas de Servicio --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($cat['services'] as $service)
                                <div class="service-card card-corporate p-6 flex flex-col justify-between" data-category="{{ $cat['id'] }}">
                                    <div class="space-y-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="text-[11px] font-bold text-[#ff934f] bg-[#f5782b]/15 px-2.5 py-1 rounded-md border border-[#f5782b]/30">
                                                {{ $cat['short_name'] }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                {{ $service['complexity'] }}
                                            </span>
                                        </div>

                                        <h4 class="text-base font-bold text-white leading-snug">
                                            {{ $service['name'] }}
                                        </h4>

                                        <p class="text-xs text-slate-300 leading-relaxed">
                                            {{ $service['description'] }}
                                        </p>

                                        <div class="pt-3 border-t border-[#cbcfd4]/10 space-y-2 text-[11px]">
                                            <div class="text-slate-400">
                                                <strong class="text-slate-200">Aplicación:</strong> {{ $service['applicability'] }}
                                            </div>
                                            <div class="text-slate-400">
                                                <strong class="text-slate-200">Unidad de cotización:</strong> {{ $service['unit'] }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-6">
                                        <button type="button" 
                                                class="btn-select-service w-full py-2.5 px-4 rounded-xl bg-[#0a3166] hover:bg-[#f5782b] text-white text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 border border-[#cbcfd4]/20 hover:border-[#f5782b]"
                                                data-service-name="{{ $service['name'] }}">
                                            <i class="fa-solid fa-file-pen text-sm"></i>
                                            <span>Solicitar Presupuesto</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Aviso de Cotización Personalizada --}}
            <div class="mt-14 p-6 rounded-2xl bg-[#0a254e]/50 border border-[#f5782b]/30 max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-[#f5782b]/20 text-[#f5782b] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">¿Requiere una combinación de servicios o parada de mantenimiento?</h4>
                        <p class="text-xs text-slate-300">Diseñamos pliegos técnicos y planes multiactividad adaptados a los turnos operativos de su empresa.</p>
                    </div>
                </div>
                <a href="#solicitar-presupuesto" class="btn-corporate-primary text-xs shrink-0 whitespace-nowrap">
                    Solicitar Valoración
                </a>
            </div>

        </div>
    </section>

    {{-- Sectores de Aplicación --}}
    <section id="sectores" class="py-20 bg-[#051f42] border-b border-[#cbcfd4]/10 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
                <span class="badge-corporate-orange">Alcance Multidisciplinario</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Sectores y Tipos de Instalaciones
                </h2>
                <p class="text-sm sm:text-base text-slate-300">
                    Adaptamos nuestros métodos y equipamiento técnico a las exigencias sanitarias, de seguridad y continuidad operacional de cada sector.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($sectors as $sec)
                    <div class="p-5 rounded-2xl bg-[#0a254e]/60 border border-[#cbcfd4]/15 hover:border-[#f5782b]/40 hover:bg-[#0a254e] transition-all duration-200 text-center space-y-3 group">
                        <div class="w-12 h-12 rounded-2xl bg-[#051f42] group-hover:bg-[#f5782b] text-[#f5782b] group-hover:text-white mx-auto flex items-center justify-center text-xl transition-colors duration-200">
                            <i class="{{ $sec['icon'] }}"></i>
                        </div>
                        <h3 class="text-sm font-bold text-white">{{ $sec['name'] }}</h3>
                        <p class="text-[11px] text-slate-300 leading-snug">{{ $sec['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Formulario de Solicitud de Presupuesto --}}
    <section id="solicitar-presupuesto" class="py-20 bg-[#020d1c] border-b border-[#cbcfd4]/10 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Encabezado del Formulario --}}
            <div class="text-center space-y-3 mb-10">
                <span class="badge-corporate-orange">Cotización Técnico-Comercial</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Solicitud de Presupuesto para Servicios Tecnológicos
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto">
                    Los servicios técnicos se cotizan de forma personalizada considerando superficie, accesibilidad, nivel de suciedad o complejidad y tiempos de ejecución requeridos.
                </p>
            </div>

            {{-- Alerta de Éxito Flash --}}
            @if(session('tech_success'))
                <div class="mb-8 p-5 rounded-2xl bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 flex items-start gap-3 shadow-lg">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-300">¡Solicitud Registrada con Éxito!</h4>
                        <p class="text-xs mt-1 leading-relaxed">{{ session('tech_success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Alerta de Errores Generales --}}
            @if ($errors->any())
                <div class="mb-8 p-5 rounded-2xl bg-rose-950/80 border border-rose-500/50 text-rose-200 flex items-start gap-3 shadow-lg">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm text-rose-300">Por favor verifique los campos del formulario</h4>
                        <ul class="list-disc list-inside text-xs mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Formulario Principal --}}
            <div class="card-corporate p-6 sm:p-10 border border-[#cbcfd4]/20 shadow-2xl">
                <form action="{{ route('tech_services.quote.store') }}" method="POST" class="space-y-6" id="tech-quote-form">
                    @csrf

                    {{-- Honeypot anti-spam --}}
                    <input type="text" name="empresa_url_check" value="" class="hidden" tabindex="-1" autocomplete="off">

                    {{-- Fila 1: Nombre y Empresa --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="nombre" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Nombre y Apellidos <span class="text-[#f5782b]">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   value="{{ old('nombre') }}" 
                                   required 
                                   placeholder="Ej. Roberto Morales García"
                                   class="input-corporate @error('nombre') border-rose-500 @enderror">
                            @error('nombre')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="empresa" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Entidad / Empresa
                            </label>
                            <input type="text" 
                                   name="empresa" 
                                   id="empresa" 
                                   value="{{ old('empresa') }}" 
                                   placeholder="Ej. Hotel Costa Azul / Ind. Metalúrgica"
                                   class="input-corporate @error('empresa') border-rose-500 @enderror">
                            @error('empresa')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 2: Correo y Teléfono --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Correo Electrónico <span class="text-[#f5782b]">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   placeholder="nombre@empresa.com"
                                   class="input-corporate @error('email') border-rose-500 @enderror">
                            @error('email')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Teléfono / WhatsApp <span class="text-[#f5782b]">*</span>
                            </label>
                            <input type="text" 
                                   name="telefono" 
                                   id="telefono" 
                                   value="{{ old('telefono') }}" 
                                   required 
                                   placeholder="+53 5xxx xxxx"
                                   class="input-corporate @error('telefono') border-rose-500 @enderror">
                            @error('telefono')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 3: Servicio de Interés --}}
                    <div>
                        <label for="servicio_interes" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Servicio Técnico de Interés <span class="text-[#f5782b]">*</span>
                        </label>
                        <select name="servicio_interes" 
                                id="servicio_interes" 
                                required 
                                class="input-corporate bg-[#020d1c] text-white @error('servicio_interes') border-rose-500 @enderror">
                            <option value="">-- Seleccione el servicio que desea cotizar --</option>
                            @foreach($categories as $cat)
                                <optgroup label="{{ $cat['name'] }}">
                                    @foreach($cat['services'] as $srv)
                                        <option value="{{ $srv['name'] }}" {{ old('servicio_interes') == $srv['name'] ? 'selected' : '' }}>
                                            {{ $srv['name'] }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            <option value="Servicio combinado / Pliego multiactividad" {{ old('servicio_interes') == 'Servicio combinado / Pliego multiactividad' ? 'selected' : '' }}>
                                * Servicio combinado / Parada de planta / Múltiples servicios
                            </option>
                        </select>
                        @error('servicio_interes')
                            <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fila 4: Tipo de Instalación y Ubicación --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="tipo_instalacion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Tipo de Instalación
                            </label>
                            <input type="text" 
                                   name="tipo_instalacion" 
                                   id="tipo_instalacion" 
                                   value="{{ old('tipo_instalacion') }}" 
                                   placeholder="Ej. Cocina industrial, Nave 4, Hotel 5 estrellas"
                                   class="input-corporate @error('tipo_instalacion') border-rose-500 @enderror">
                            @error('tipo_instalacion')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ubicacion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Ubicación / Municipio
                            </label>
                            <input type="text" 
                                   name="ubicacion" 
                                   id="ubicacion" 
                                   value="{{ old('ubicacion') }}" 
                                   placeholder="Ej. La Habana, Mariel, Varadero"
                                   class="input-corporate @error('ubicacion') border-rose-500 @enderror">
                            @error('ubicacion')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fila 5: Superficie/Volumen y Complejidad --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="superficie_volumen" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Dimensión Estimada
                            </label>
                            <input type="text" 
                                   name="superficie_volumen" 
                                   id="superficie_volumen" 
                                   value="{{ old('superficie_volumen') }}" 
                                   placeholder="Ej. 450 m², 2 campanas"
                                   class="input-corporate @error('superficie_volumen') border-rose-500 @enderror">
                            @error('superficie_volumen')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nivel_complejidad" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Nivel de Complejidad
                            </label>
                            <select name="nivel_complejidad" 
                                    id="nivel_complejidad" 
                                    class="input-corporate bg-[#020d1c] text-white">
                                <option value="Estándar" {{ old('nivel_complejidad') == 'Estándar' ? 'selected' : '' }}>Estándar</option>
                                <option value="Media" {{ old('nivel_complejidad') == 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Alta / Trabajo en Altura" {{ old('nivel_complejidad') == 'Alta / Trabajo en Altura' ? 'selected' : '' }}>Alta / Altura</option>
                                <option value="Crítica / Parada de Planta" {{ old('nivel_complejidad') == 'Crítica / Parada de Planta' ? 'selected' : '' }}>Crítica / Parada</option>
                            </select>
                        </div>

                        <div>
                            <label for="plazo_deseado" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Plazo Deseado
                            </label>
                            <select name="plazo_deseado" 
                                    id="plazo_deseado" 
                                    class="input-corporate bg-[#020d1c] text-white">
                                <option value="Inmediato (1 a 5 días)" {{ old('plazo_deseado') == 'Inmediato (1 a 5 días)' ? 'selected' : '' }}>Inmediato (1-5 días)</option>
                                <option value="Corto Plazo (1 a 2 semanas)" {{ old('plazo_deseado') == 'Corto Plazo (1 a 2 semanas)' ? 'selected' : '' }}>Corto Plazo (1-2 sem)</option>
                                <option value="Planificado (Próximo mes)" {{ old('plazo_deseado') == 'Planificado (Próximo mes)' ? 'selected' : '' }}>Planificado (Próx mes)</option>
                                <option value="Contrato Periódico Anual" {{ old('plazo_deseado') == 'Contrato Periódico Anual' ? 'selected' : '' }}>Contrato Periódico</option>
                            </select>
                        </div>
                    </div>

                    {{-- Fila 6: Descripción del requerimiento --}}
                    <div>
                        <label for="descripcion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Descripción del Requerimiento / Alcance Técnico <span class="text-[#f5782b]">*</span>
                        </label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="4" 
                                  required 
                                  placeholder="Detalle el estado actual de las áreas o equipos, problemas observados (grasa, obstrucción, sarro, filtraciones) o condicionantes de horario para la ejecución..."
                                  class="input-corporate @error('descripcion') border-rose-500 @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Aceptación de Privacidad --}}
                    <div class="pt-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="aceptado_privacidad" 
                                   value="1" 
                                   required 
                                   {{ old('aceptado_privacidad') ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 rounded text-[#f5782b] focus:ring-[#f5782b] bg-[#020d1c] border-[#cbcfd4]/30">
                            <span class="text-xs text-slate-300 leading-relaxed">
                                Acepto que los datos suministrados sean procesados por el equipo técnico-comercial de BLANKISOL SURL para elaborar la propuesta y coordinar visitas técnicas si fuesen requeridas. <span class="text-[#f5782b]">*</span>
                            </span>
                        </label>
                        @error('aceptado_privacidad')
                            <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón de Envío --}}
                    <div class="pt-4">
                        <button type="submit" class="btn-corporate-primary w-full py-4 text-base">
                            <i class="fa-solid fa-paper-plane mr-2"></i>
                            <span>Enviar Solicitud de Presupuesto</span>
                        </button>
                    </div>

                </form>
            </div>

            {{-- Bloque de Contactos Directos y Verificación --}}
            <div class="mt-12 p-6 rounded-2xl bg-[#0a254e]/40 border border-[#cbcfd4]/15 text-center space-y-4">
                <div class="inline-flex items-center gap-2 text-xs font-bold text-[#f5782b] uppercase tracking-wider">
                    <i class="fa-solid fa-headset"></i>
                    <span>Atención Comercial y Coordinación de Visitas</span>
                </div>
                <p class="text-xs text-slate-300 max-w-xl mx-auto">
                    Para pliegos de licitación, consultas de urgencia o coordinación de inspección técnica en planta:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg mx-auto text-xs">
                    <div class="p-3 rounded-xl bg-[#051f42] border border-[#cbcfd4]/10">
                        <span class="font-bold text-white block">Maikel Yunior León Alvarez</span>
                        <span class="text-slate-400 block text-[11px]">Coordinación Comercial</span>
                        <span class="text-[#ff934f] font-mono font-semibold block mt-1">+53 56142721</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#051f42] border border-[#cbcfd4]/10">
                        <span class="font-bold text-white block">Yanara Lara Pérez</span>
                        <span class="text-slate-400 block text-[11px]">Atención Técnica</span>
                        <span class="text-[#ff934f] font-mono font-semibold block mt-1">+53 63512810</span>
                    </div>
                </div>

                <p class="text-[10px] text-slate-400 italic">
                    * Canales corporativos en validación de disponibilidad operativa. El formulario digital cuenta con recepción y acuse de recibo inmediato.
                </p>
            </div>

        </div>
    </section>

    {{-- Footer Corporativo --}}
    @include('partials.landing.footer')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Filtrado dinámico de servicios por categoría
    const filterTabs = document.querySelectorAll('.filter-tab');
    const categoryBlocks = document.querySelectorAll('.category-block');
    const serviceCards = document.querySelectorAll('.service-card');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');

            // Actualizar estado de pestañas
            filterTabs.forEach(t => {
                t.classList.remove('active', 'bg-[#f5782b]', 'text-white', 'shadow-md', 'border-[#f5782b]');
                t.classList.add('bg-[#0a254e]', 'text-slate-200', 'border-[#cbcfd4]/15');
            });
            this.classList.add('active', 'bg-[#f5782b]', 'text-white', 'shadow-md', 'border-[#f5782b]');
            this.classList.remove('bg-[#0a254e]', 'text-slate-200');

            if (filter === 'all') {
                categoryBlocks.forEach(b => b.style.display = 'block');
                serviceCards.forEach(c => c.style.display = 'flex');
            } else {
                categoryBlocks.forEach(b => {
                    const group = b.getAttribute('data-category-group');
                    if (group === filter) {
                        b.style.display = 'block';
                    } else {
                        b.style.display = 'none';
                    }
                });
            }
        });
    });

    // 2. Pre-llenado de servicio al hacer clic en "Solicitar Presupuesto" en cualquier tarjeta
    const selectServiceButtons = document.querySelectorAll('.btn-select-service');
    const serviceSelect = document.getElementById('servicio_interes');
    const quoteSection = document.getElementById('solicitar-presupuesto');

    selectServiceButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const serviceName = this.getAttribute('data-service-name');
            if (serviceSelect && serviceName) {
                // Buscar si existe en las opciones
                let found = false;
                for (let i = 0; i < serviceSelect.options.length; i++) {
                    if (serviceSelect.options[i].value === serviceName) {
                        serviceSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    serviceSelect.value = serviceName;
                }

                // Desplazamiento suave al formulario
                if (quoteSection) {
                    quoteSection.scrollIntoView({ behavior: 'smooth' });
                    // Resaltar brevemente el select
                    serviceSelect.focus();
                    serviceSelect.classList.add('ring-2', 'ring-[#f5782b]');
                    setTimeout(() => {
                        serviceSelect.classList.remove('ring-2', 'ring-[#f5782b]');
                    }, 1800);
                }
            }
        });
    });

    // 3. Conmutador Interactivo de Casos de Intervención Técnica (Antes / Después)
    const caseButtons = document.querySelectorAll('.case-btn');
    const caseBeforeTitle = document.getElementById('case-before-title');
    const caseBeforeDesc = document.getElementById('case-before-desc');
    const caseAfterTitle = document.getElementById('case-after-title');
    const caseAfterDesc = document.getElementById('case-after-desc');

    const casesData = {
        cocinas: {
            beforeTitle: 'Campana y Ductos con Grasa Petrificada',
            beforeDesc: 'Acumulación excesiva de grasa combustible en ductos, campana y turbina. Riesgo inminente de ignición por llama de cocina, sobrecalentamiento del extractor y rechazo en auditoría sanitaria.',
            afterTitle: 'Acabado Espejo Inoxidable & Desengrase Total',
            afterDesc: 'Aplicación de desengrasante técnico de inmersión y vapor a temperatura. Recuperación del flujo volumétrico de succión, turbina balanceada y certificado de desinfección expedido.'
        },
        pisos: {
            beforeTitle: 'Pavimentos Epóxicos con Residuos Químicos y Aceite',
            beforeDesc: 'Superficie industrial altamente resbaladiza con costras de hidrocarburos, pintura desgastada y sedimentos que incumplen normas de seguridad y ponen en riesgo a operarios.',
            afterTitle: 'Decapado Mecánico y Tratamiento Antideslizante',
            afterDesc: 'Tratamiento profundo con barredoras rotativas y succión industrial. Superficie totalmente descontaminada, textura homogénea antideslizante y apta para tráfico pesado.'
        },
        tanques: {
            beforeTitle: 'Tanques y Cisternas con Biofilm y Sedimentos',
            beforeDesc: 'Paredes con sarro biológico, lodos estancados en fondo y pérdida de potabilidad. Alerta sanitaria para el suministro de agua en comedores, calderas o red contra incendios.',
            afterTitle: 'Vaporizado Sanitario y Desinfección Grado Inocuo',
            afterDesc: 'Evacuación de lodos, desincrustación con vapor técnico y aplicación de desinfectante certificado. Agua limpia, paredes sanitizadas y dictamen de inocuidad emitido.'
        }
    };

    caseButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const caseKey = this.getAttribute('data-case');
            if (casesData[caseKey]) {
                caseButtons.forEach(b => {
                    b.classList.remove('active', 'bg-[#f5782b]', 'text-white', 'shadow-md');
                    b.classList.add('bg-[#0a254e]', 'text-slate-300');
                });
                this.classList.add('active', 'bg-[#f5782b]', 'text-white', 'shadow-md');
                this.classList.remove('bg-[#0a254e]', 'text-slate-300');

                if (caseBeforeTitle) caseBeforeTitle.textContent = casesData[caseKey].beforeTitle;
                if (caseBeforeDesc) caseBeforeDesc.textContent = casesData[caseKey].beforeDesc;
                if (caseAfterTitle) caseAfterTitle.textContent = casesData[caseKey].afterTitle;
                if (caseAfterDesc) caseAfterDesc.textContent = casesData[caseKey].afterDesc;
            }
        });
    });
});
</script>
@endsection
