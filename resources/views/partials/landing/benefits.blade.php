<section id="beneficios" class="py-24 relative overflow-hidden bg-[#051f42]/95 border-b border-[#cbcfd4]/10">
    {{-- Rejilla sutil y reflejos lumínicos --}}
    <div class="absolute inset-0 bg-grid-mesh pointer-events-none opacity-40 -z-10"></div>
    <div class="absolute top-1/2 right-0 w-[500px] h-[500px] bg-[#f5782b]/5 blur-[160px] pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="badge-corporate-orange">
                <i class="fa-solid fa-award"></i> Capacidades & Diferenciales
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                ¿Por Qué Elegir a BLANKISOL SURL?
            </h2>
            <p class="text-base text-slate-300 leading-relaxed">
                Combinamos infraestructura tecnológica de vanguardia, rigor documental aduanal y un equipo multidisciplinario orientado a la excelencia.
            </p>
        </div>

        <!-- Bento Grid Layout Moderno (Asimétrico con Profundidad 3D) -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
            
            <!-- Bento Card 1: Torre de Control 24/7 (Grande: 2 cols, 2 rows en desktop) -->
            <div class="md:col-span-2 lg:col-span-2 glass-panel-3d p-8 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-[#f5782b]/10 rounded-full blur-3xl group-hover:bg-[#f5782b]/20 transition-all duration-500 pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Torre de Control Activa 24/7</span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">Plataforma SCGI</span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-white mb-3">
                        Telemetría Satelital y Trazabilidad en Tiempo Real
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-md">
                        Cada expedición cuenta con registro digital instantáneo, lectura óptica de códigos QR, cotejo biométrico de precintos y alertas automatizadas de arribo a puerto y patio técnico.
                    </p>
                </div>

                {{-- Visualizador Mini Telemetría simulada --}}
                <div class="mt-8 p-4 rounded-2xl bg-[#020d1c]/90 border border-[#cbcfd4]/15 space-y-2.5">
                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-300">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-satellite text-[#f5782b]"></i> Estado Operativo:</span>
                        <span class="text-emerald-400 font-bold">100% Monitoreado</span>
                    </div>
                    <div class="w-full bg-[#051f42] h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-[#f5782b] to-emerald-400 h-full rounded-full animate-pulse" style="width: 85%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                        <span>ZLC Panamá ➔ Altamar</span>
                        <span>ETA Mariel: En horario</span>
                    </div>
                </div>
            </div>

            <!-- Bento Card 2: Conexión Internacional Multimodal -->
            <div class="md:col-span-1 lg:col-span-2 glass-panel-3d p-7 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#051f42] border border-[#f5782b]/30 text-[#f5782b] flex items-center justify-center text-xl mb-4 shadow-md">
                        <i class="fa-solid fa-earth-americas"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">
                        Corredores Internacionales Estratégicos
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Conexiones fluidas con hubs logísticos en Panamá (Zona Libre de Colón), Miami y Europa, optimizando tiempos de flete y reduciendo escalas intermedias.
                    </p>
                </div>

                <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 flex items-center gap-2 text-xs text-slate-400">
                    <i class="fa-solid fa-ship text-[#ff934f]"></i>
                    <span>Marítimo FCL/LCL</span>
                    <span class="text-slate-600">&bull;</span>
                    <i class="fa-solid fa-plane text-[#ff934f]"></i>
                    <span>Aéreo Prioritario</span>
                </div>
            </div>

            <!-- Bento Card 3: KPIs de Rendimiento (Métricas) -->
            <div class="glass-panel-3d p-7 flex flex-col justify-between text-center bg-gradient-to-b from-[#0a254e]/80 to-[#020d1c]">
                <div>
                    <span class="block text-4xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-[#f5782b] font-mono">
                        100%
                    </span>
                    <span class="text-xs font-bold text-[#ff934f] uppercase tracking-wider block mt-1">
                        Trazabilidad PIN
                    </span>
                    <p class="text-[11px] text-slate-300 mt-2">
                        Control punto a punto desde consolidación hasta entrega en destino.
                    </p>
                </div>
                <div class="pt-4 border-t border-[#cbcfd4]/10 text-[10px] text-slate-400 font-mono">
                    Cero extravíos documentados
                </div>
            </div>

            <!-- Bento Card 4: Seguridad y Custodia Certificada -->
            <div class="glass-panel-3d p-7 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#051f42] border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl mb-4 shadow-md">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">
                        Custodia & Desaduanaje Seguro
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Inspección física y documental en patio técnico PVP Mariel bajo estrictas normas de seguridad y resguardo fiscal.
                    </p>
                </div>
                <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-emerald-400 font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-lock"></i>
                    <span>Protocolos de Seguridad Activos</span>
                </div>
            </div>

            <!-- Bento Card 5: Especialistas en MIPYMES y Empresas -->
            <div class="md:col-span-2 lg:col-span-2 glass-panel-3d p-7 flex flex-col justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#051f42] border border-[#f5782b]/30 text-[#f5782b] flex items-center justify-center text-xl shrink-0 shadow-md">
                        <i class="fa-solid fa-building-circle-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white mb-1.5">
                            Asesoría Integral para Empresas & MIPYMES
                        </h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Acompañamos a nuevos importadores, cooperativas y entidades corporativas con asesoramiento tributario, clasificación arancelaria y optimización de fletes para proteger su rentabilidad.
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="text-slate-300">
                        <strong class="text-white">Modalidades:</strong> Contratos anuales o despachos por lote.
                    </span>
                    <a href="#contacto" class="text-[#f5782b] hover:text-[#ff934f] font-bold flex items-center gap-1">
                        <span>Hablar con un asesor</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>