<footer class="bg-[#020d1c] border-t border-[#cbcfd4]/15 text-slate-300 text-xs py-16 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-[#cbcfd4]/10">
            
            <!-- Brand Column (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-[#f5782b]/40 flex items-center justify-center text-[#f5782b] text-lg shadow-md">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-white block">BLANKISOL</span>
                        <span class="text-[10px] font-bold tracking-widest text-[#cbcfd4] uppercase">Soluciones Logísticas Integrales</span>
                    </div>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                    Operador logístico de vanguardia especializado en el transporte multimodal, control aduanal y trazabilidad en tiempo real.
                </p>

                <div class="pt-2">
                    <p class="text-xs font-semibold text-white">«Tu carga. Nuestro compromiso.»</p>
                    <p class="text-[11px] text-[#cbcfd4] mt-0.5">El mundo, nuestro destino.</p>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#051f42] border border-[#cbcfd4]/15 text-[11px] text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistemas y Servidores SCGI Activos</span>
                </div>
            </div>

            <!-- Links Column 1: Navegación -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Navegación</h4>
                <ul class="space-y-2.5">
                    <li><a href="#hero" class="hover:text-[#f5782b] transition-colors">Inicio</a></li>
                    <li><a href="#nosotros" class="hover:text-[#f5782b] transition-colors">Quiénes Somos</a></li>
                    <li><a href="#servicios" class="hover:text-[#f5782b] transition-colors">Servicios Multimodales</a></li>
                    <li><a href="#proceso" class="hover:text-[#f5782b] transition-colors">Metodología</a></li>
                    <li><a href="#tracking" class="text-[#f5782b] hover:text-[#ff934f] transition-colors font-medium">Rastrear Carga</a></li>
                    <li><a href="#contacto" class="hover:text-[#f5782b] transition-colors">Cotizar Envío</a></li>
                </ul>
            </div>

            <!-- Links Column 2: Servicios -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Servicios</h4>
                <ul class="space-y-2.5 text-slate-400">
                    <li><a href="#servicios" onclick="selectModalidad('maritimo')" class="hover:text-white transition-colors">Transporte Marítimo FCL / LCL</a></li>
                    <li><a href="#servicios" onclick="selectModalidad('aereo')" class="hover:text-white transition-colors">Transporte Aéreo Prioritario</a></li>
                    <li><a href="#servicios" onclick="selectModalidad('terrestre')" class="hover:text-white transition-colors">Distribución Terrestre Regional</a></li>
                    <li><a href="#servicios" onclick="selectModalidad('aduanal')" class="hover:text-white transition-colors">Cotejo y Trámite Aduanal</a></li>
                    <li><a href="#contacto" class="hover:text-white transition-colors">Soluciones para MIPYMES</a></li>
                </ul>
            </div>

            <!-- Links Column 3: Soporte & Privacidad -->
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white mb-4">Soporte & Legal</h4>
                <ul class="space-y-2.5 text-slate-400">
                    <li><a href="#contacto" class="hover:text-white transition-colors">Soporte Técnico</a></li>
                    <li><span class="text-slate-500">Aviso de Privacidad (En revisión comercial)</span></li>
                    <li><span class="text-slate-500">Términos de Servicio</span></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
            <div>
                &copy; {{ date('Y') }} <strong class="text-slate-300">BLANKISOL</strong>. Todos los derechos reservados.
            </div>
            <div class="flex items-center gap-6">
                <span>Torre de Control SCGI &bull; Versión 2.0</span>
                <a href="#hero" class="text-slate-400 hover:text-[#f5782b] transition-colors" title="Volver arriba">
                    <i class="fa-solid fa-chevron-up"></i>
                </a>
            </div>
        </div>
    </div>
</footer>