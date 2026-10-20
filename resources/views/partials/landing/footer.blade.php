<footer class="bg-[#020d1c] border-t border-[#cbcfd4]/15 text-slate-300 text-xs py-16 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-[#cbcfd4]/10">
            
            <!-- Brand Column (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-[#f5782b]/40 p-1 flex items-center justify-center shadow-md overflow-hidden">
                        <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL SURL" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-white block">BLANKISOL SURL</span>
                        <span class="text-[10px] font-bold tracking-widest text-[#cbcfd4] uppercase">Soluciones Corporativas Integrales</span>
                    </div>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                    Empresa dedicada a la integración de soluciones logísticas multimodales y servicios tecnológicos de limpieza industrial especializada, higienización y mantenimiento técnico.
                </p>

                <div class="pt-2 space-y-1">
                    <p class="text-xs font-semibold text-white">Logística: «Tu carga. Nuestro compromiso.»</p>
                    <p class="text-xs font-semibold text-[#f5782b]">Servicios Tecnológicos: «Credibilidad y garantía es nuestra meta.»</p>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#051f42] border border-[#cbcfd4]/15 text-[11px] text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistemas y Servicios Corporativos en Operación</span>
                </div>
            </div>

            <!-- Links Column 1: Navegación Corporativa -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Navegación</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('home') }}" class="hover:text-[#f5782b] transition-colors">Inicio</a></li>
                    <li><a href="{{ route('home') }}#nosotros" class="hover:text-[#f5782b] transition-colors">Nosotros</a></li>
                    <li><a href="{{ route('home') }}#areas-negocio" class="hover:text-[#f5782b] transition-colors">Áreas de Negocio</a></li>
                    <li><a href="{{ route('home') }}#tracking" class="text-[#f5782b] hover:text-[#ff934f] transition-colors font-medium">Rastreo de Carga</a></li>
                    <li><a href="{{ route('home') }}#faq" class="hover:text-[#f5782b] transition-colors">Preguntas Frecuentes</a></li>
                    <li><a href="{{ route('home') }}#contacto" class="hover:text-[#f5782b] transition-colors">Contacto Corporativo</a></li>
                </ul>
            </div>

            <!-- Links Column 2: Logística y Trazabilidad -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Logística</h4>
                <ul class="space-y-2.5 text-slate-400">
                    <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Transporte Marítimo FCL / LCL</a></li>
                    <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Transporte Aéreo Prioritario</a></li>
                    <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Distribución Terrestre Regional</a></li>
                    <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Trámites y Control Aduanal</a></li>
                    <li><a href="{{ route('home') }}#contacto" class="hover:text-white transition-colors">Cotización de Carga</a></li>
                </ul>
            </div>

            <!-- Links Column 3: Servicios Tecnológicos Integrales -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Servicios Tecnológicos</h4>
                <ul class="space-y-2.5 text-slate-400">
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Limpieza e Higienización</a></li>
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Sistemas de Extracción y Cocinas</a></li>
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Redes Hidráulicas y Saneamiento</a></li>
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Limpieza Industrial Especializada</a></li>
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Energía y Paneles Solares</a></li>
                    <li><a href="{{ route('tech_services.index') }}#catalogo" class="hover:text-white transition-colors">Construcción y Mantenimiento</a></li>
                    <li><a href="{{ route('tech_services.index') }}#solicitar-presupuesto" class="text-[#f5782b] hover:text-[#ff934f] transition-colors font-medium">Solicitar Presupuesto</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
            <div>
                &copy; {{ date('Y') }} <strong class="text-slate-300">BLANKISOL SURL</strong>. Todos los derechos reservados.
            </div>
            <div class="flex items-center gap-6">
                <span>Plataforma Corporativa SCGI</span>
                <a href="#hero" class="text-slate-400 hover:text-[#f5782b] transition-colors" title="Volver al inicio">
                    <i class="fa-solid fa-chevron-up"></i>
                </a>
            </div>
        </div>
    </div>
</footer>