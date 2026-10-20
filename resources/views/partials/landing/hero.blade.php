<section id="hero" class="relative pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden">
    {{-- Rejilla sutil y efectos lumínicos locales --}}
    <div class="absolute inset-0 bg-grid-mesh pointer-events-none opacity-50 -z-10"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[850px] h-[450px] bg-[#0a3166]/30 blur-[130px] pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            
            <!-- Badge de Identidad Corporativa con Beacon Activo -->
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-[#0a254e]/90 border border-[#f5782b]/35 shadow-inner mb-6 backdrop-blur-md">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#f5782b] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#f5782b]"></span>
                </span>
                <span class="text-xs font-bold tracking-wider uppercase text-[#cbcfd4]">
                    BLANKISOL SURL &bull; Dos divisiones empresariales, un mismo estándar de excelencia
                </span>
            </div>

            <!-- Título Principal H1 Oficial Corporativo -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.1] mb-6 font-sans">
                Soluciones integrales para <br class="hidden sm:inline" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-[#cbcfd4] to-[#f5782b]">
                    empresas e instituciones
                </span>
            </h1>

            <!-- Descripción Editorial Oficial -->
            <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto font-normal leading-relaxed mb-8">
                En <strong>BLANKISOL SURL</strong> conectamos soluciones logísticas multimodales y servicios tecnológicos industriales para responder a las necesidades operativas de su empresa.
            </p>

            <!-- Dual-Hub Experience Switcher (Pill Switcher) -->
            <div class="mb-10 max-w-xl mx-auto">
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 block mb-2.5">
                    ¿Qué solución requiere su organización hoy?
                </span>
                <div class="p-1.5 rounded-2xl bg-[#020d1c]/90 border border-[#cbcfd4]/20 flex items-center shadow-xl backdrop-blur-xl" id="hero-hub-switcher" role="tablist">
                    <button type="button" 
                            id="tab-logistica" 
                            class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-300 flex items-center justify-center gap-2 bg-gradient-to-r from-[#f5782b] to-[#e0651a] text-white shadow-lg shadow-[#f5782b]/30">
                        <i class="fa-solid fa-ship text-sm"></i>
                        <span>1. Logística & Carga</span>
                    </button>
                    <button type="button" 
                            id="tab-tecnologicos" 
                            class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-sm text-[#ff934f]"></i>
                        <span>2. Servicios Tecnológicos</span>
                    </button>
                </div>
            </div>

            <!-- Contenedor Reactivo: Foco División 1 (Logística) -->
            <div id="hub-content-logistica" class="transition-all duration-300 space-y-6">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-5">
                    <a href="#tracking" class="btn-corporate-primary w-full sm:w-auto text-base !py-4 !px-8 shadow-xl hover-shine">
                        <i class="fa-solid fa-magnifying-glass mr-1.5 text-white"></i>
                        <span>Rastrear Carga con PIN</span>
                    </a>
                    <a href="#contacto" class="btn-corporate-secondary w-full sm:w-auto text-base !py-4 !px-8">
                        <i class="fa-solid fa-file-contract mr-1.5 text-[#f5782b]"></i>
                        <span>Cotizar Envíos Multimodales</span>
                    </a>
                </div>
                <div class="inline-flex flex-wrap items-center justify-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Torre de Control 24/7</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> FCL / LCL Marítimo & Aéreo</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Despacho Aduanal Mariel</span>
                </div>
            </div>

            <!-- Contenedor Reactivo: Foco División 2 (Servicios Tecnológicos) -->
            <div id="hub-content-tecnologicos" class="hidden transition-all duration-300 space-y-6">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-5">
                    <a href="{{ route('tech_services.index') }}#catalogo" class="btn-corporate-primary w-full sm:w-auto text-base !py-4 !px-8 shadow-xl hover-shine">
                        <i class="fa-solid fa-layer-group mr-1.5 text-white"></i>
                        <span>Explorar Catálogo Técnico (6 Categorías)</span>
                    </a>
                    <a href="{{ route('tech_services.index') }}#solicitar-presupuesto" class="btn-corporate-secondary w-full sm:w-auto text-base !py-4 !px-8">
                        <i class="fa-solid fa-calculator mr-1.5 text-[#f5782b]"></i>
                        <span>Solicitar Presupuesto Industrial</span>
                    </a>
                </div>
                <div class="inline-flex flex-wrap items-center justify-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-[#f5782b]"></i> Desengrase de Cocinas y Extracción</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-[#f5782b]"></i> Higienización de Naves y Silos</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-[#f5782b]"></i> Climatización y Obras Técnicas</span>
                </div>
            </div>

            <!-- Resumen Visual de las 2 Divisiones con Estilo Glassmorphism 3D -->
            <div class="pt-10 mt-10 border-t border-[#cbcfd4]/10 grid grid-cols-1 sm:grid-cols-2 gap-5 text-left max-w-4xl mx-auto">
                <a href="#logistica" 
                   id="card-division-logistica"
                   class="glass-panel-3d p-5 flex items-start gap-4 group cursor-pointer border-[#f5782b]/40">
                    <div class="w-12 h-12 rounded-2xl bg-[#051f42] text-[#f5782b] flex items-center justify-center text-xl shrink-0 border border-[#f5782b]/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold text-[#cbcfd4] uppercase tracking-wider">División 01</span>
                            <span class="text-[10px] font-mono text-[#f5782b] font-bold">Logística</span>
                        </div>
                        <h2 class="text-base font-extrabold text-white group-hover:text-[#f5782b] transition-colors">
                            Logística & Trazabilidad
                        </h2>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Carga marítima FCL/LCL, aérea prioritaria y torre de control con seguimiento por PIN en tiempo real.
                        </p>
                    </div>
                </a>

                <a href="{{ route('tech_services.index') }}" 
                   id="card-division-tecnologicos"
                   class="glass-panel-3d p-5 flex items-start gap-4 group cursor-pointer">
                    <div class="w-12 h-12 rounded-2xl bg-[#051f42] text-[#ff934f] flex items-center justify-center text-xl shrink-0 border border-[#ff934f]/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold text-[#cbcfd4] uppercase tracking-wider">División 02</span>
                            <span class="text-[10px] font-mono text-[#ff934f] font-bold">Servicios Técnicos</span>
                        </div>
                        <h2 class="text-base font-extrabold text-white group-hover:text-[#f5782b] transition-colors">
                            Servicios Tecnológicos Integrales
                        </h2>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Limpieza especializada, desengrase industrial de extracción, saneamiento por vapor y mantenimiento.
                        </p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- Script del Conmutador Reactivo del Hero --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabLogistica = document.getElementById('tab-logistica');
    const tabTecnologicos = document.getElementById('tab-tecnologicos');
    const contentLogistica = document.getElementById('hub-content-logistica');
    const contentTecnologicos = document.getElementById('hub-content-tecnologicos');
    const cardLogistica = document.getElementById('card-division-logistica');
    const cardTecnologicos = document.getElementById('card-division-tecnologicos');

    function activateLogistica() {
        tabLogistica.classList.add('bg-gradient-to-r', 'from-[#f5782b]', 'to-[#e0651a]', 'text-white', 'shadow-lg');
        tabLogistica.classList.remove('text-slate-300');
        tabTecnologicos.classList.remove('bg-gradient-to-r', 'from-[#f5782b]', 'to-[#e0651a]', 'text-white', 'shadow-lg');
        tabTecnologicos.classList.add('text-slate-300');

        contentLogistica.classList.remove('hidden');
        contentTecnologicos.classList.add('hidden');

        if (cardLogistica && cardTecnologicos) {
            cardLogistica.classList.add('border-[#f5782b]/60', 'glow-accent-sm');
            cardTecnologicos.classList.remove('border-[#f5782b]/60', 'glow-accent-sm');
        }
    }

    function activateTecnologicos() {
        tabTecnologicos.classList.add('bg-gradient-to-r', 'from-[#f5782b]', 'to-[#e0651a]', 'text-white', 'shadow-lg');
        tabTecnologicos.classList.remove('text-slate-300');
        tabLogistica.classList.remove('bg-gradient-to-r', 'from-[#f5782b]', 'to-[#e0651a]', 'text-white', 'shadow-lg');
        tabLogistica.classList.add('text-slate-300');

        contentTecnologicos.classList.remove('hidden');
        contentLogistica.classList.add('hidden');

        if (cardLogistica && cardTecnologicos) {
            cardTecnologicos.classList.add('border-[#f5782b]/60', 'glow-accent-sm');
            cardLogistica.classList.remove('border-[#f5782b]/60', 'glow-accent-sm');
        }
    }

    if (tabLogistica && tabTecnologicos) {
        tabLogistica.addEventListener('click', activateLogistica);
        tabTecnologicos.addEventListener('click', activateTecnologicos);
    }
});
</script>