<section id="proceso" class="py-24 bg-[#020d1c]/90 border-y border-[#cbcfd4]/10 relative overflow-hidden">
    {{-- Textura y reflejos lumínicos --}}
    <div class="absolute inset-0 bg-grid-mesh pointer-events-none opacity-40 -z-10"></div>
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-[#0a3166]/25 blur-[150px] pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="badge-corporate-orange mb-3">
                <i class="fa-solid fa-route"></i> Trazabilidad de Punta a Punta
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                El Viaje de tu Carga con BLANKISOL
            </h2>
            <p class="text-base text-slate-300 mt-4 leading-relaxed">
                Un ciclo logístico transparente y estructurado. Selecciona cualquier hito del flujo interactivo para examinar las garantías documentales y operativas de cada fase.
            </p>

            {{-- Resumen de Corredores Activos --}}
            <div class="mt-6 inline-flex flex-wrap items-center justify-center gap-2 px-4 py-2 rounded-full bg-[#0a254e]/70 border border-[#cbcfd4]/20 text-xs text-slate-300">
                <span class="font-bold text-[#ff934f]">Corredores Activos:</span>
                <span>Panamá (ZLC) &bull; EE.UU. (Miami) &bull; Europa</span>
                <i class="fa-solid fa-arrow-right text-[10px] text-[#f5782b] mx-1"></i>
                <span class="text-emerald-300 font-semibold">Puerto del Mariel (Cuba)</span>
            </div>
        </div>

        <!-- Interactive 4-Step Pipeline Connector -->
        <div class="relative">
            
            {{-- Línea conectora visual con gradiente (en desktop) --}}
            <div class="hidden lg:block absolute top-1/2 left-10 right-10 h-1 bg-gradient-to-r from-[#0a3166] via-[#f5782b] to-[#0a3166] -translate-y-6 -z-0 opacity-40"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10" id="freight-pipeline-cards">
                
                <!-- Hito 01 -->
                <div class="pipeline-step-card glass-panel-3d p-6 flex flex-col justify-between cursor-pointer border-[#f5782b]/50 shadow-lg glow-accent-sm transition-all duration-300"
                     data-step="1"
                     data-title="Recepción en Hub & Emisión de PIN"
                     data-badge="Fase Inicial de Custodia"
                     data-duration="24 - 48 Horas"
                     data-docs="Packing List Cifrado, Fotos de Verificación, Registro VIN / Serial"
                     data-desc="La carga ingresa al almacén internacional de consolidación. Se realiza pesaje certificado en báscula electrónica, verificación física de empaque y emisión del identificador PIN con código QR para seguimiento en la torre de control.">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">01</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-box-archive text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Recepción & PIN</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Cotejo de empaque, inspección de seriales e ingreso formal a la plataforma con PIN exclusivo.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-[#ff934f] flex items-center justify-between font-medium">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-qrcode"></i> PIN digital</span>
                        <span class="text-[10px] text-slate-400 font-mono">Hub Origen</span>
                    </div>
                </div>

                <!-- Hito 02 -->
                <div class="pipeline-step-card glass-panel-3d p-6 flex flex-col justify-between cursor-pointer border-[#cbcfd4]/15 hover:border-[#f5782b]/40 transition-all duration-300"
                     data-step="2"
                     data-title="Tránsito Multimodal Internacional"
                     data-badge="Monitoreo Satelital Activo"
                     data-duration="3 a 12 Días (según marítimo/aéreo)"
                     data-docs="Bill of Lading (B/L) o Air Waybill (AWB), Manifiesto de Carga"
                     data-desc="Embarque en buques de línea regular o vuelos prioritarios de carga. El sistema actualiza en tiempo real los hitos de travesía marítima/aérea, posición satelital estimada y fecha tentativa de arribo (ETA) al puerto de destino.">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">02</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-compass text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Tránsito Multimodal</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Telemetría activa durante el flete marítimo o aéreo con manifiesto oficial y fecha estimada de arribo.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-slate-300 flex items-center justify-between font-medium">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-satellite text-[#f5782b]"></i> Telemetría</span>
                        <span class="text-[10px] text-slate-400 font-mono">En altamar / vuelo</span>
                    </div>
                </div>

                <!-- Hito 03 -->
                <div class="pipeline-step-card glass-panel-3d p-6 flex flex-col justify-between cursor-pointer border-[#cbcfd4]/15 hover:border-[#f5782b]/40 transition-all duration-300"
                     data-step="3"
                     data-title="Despacho Aduanal y Patio Técnico"
                     data-badge="Validación Arancelaria y Fiscal"
                     data-duration="48 a 72 Horas"
                     data-docs="Declaración de Aduana, Dictamen Técnico de Inspección, Acta de Desaduanaje"
                     data-desc="Arribo a puerto fiscal (Terminal de Contenedores del Mariel o Aeropuerto). Inspección física y documental por las autoridades aduanales, liberación de aforos y custodia segura en patio técnico de verificación para su despacho.">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">03</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-stamp text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Aduana & Patio PVP</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Inspección oficial, liberación arancelaria y puesta a punto en patio técnico de verificación aduanal.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-slate-300 flex items-center justify-between font-medium">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-clipboard-check text-[#f5782b]"></i> Liberación fiscal</span>
                        <span class="text-[10px] text-slate-400 font-mono">Puerto Mariel</span>
                    </div>
                </div>

                <!-- Hito 04 -->
                <div class="pipeline-step-card glass-panel-3d p-6 flex flex-col justify-between cursor-pointer border-[#cbcfd4]/15 hover:border-[#f5782b]/40 transition-all duration-300"
                     data-step="4"
                     data-title="Distribución Regional & Entrega Certificada"
                     data-badge="Cierre de Trazabilidad"
                     data-duration="24 a 48 Horas según provincia"
                     data-docs="Guía de Conducción Terrestre, Acta de Recepción Conforme, Firma Digital"
                     data-desc="Traslado terrestre en convoyes coordinados hacia cajas receptoras o entrega puerta a puerta al consignatario. Firma de conformidad con cotejo físico de bultos y cierre formal del ciclo en la torre de control.">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-black text-[#f5782b] font-mono">04</span>
                            <div class="w-10 h-10 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 flex items-center justify-center text-slate-200">
                                <i class="fa-solid fa-truck-ramp-box text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Distribución & Entrega</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Convoyes regionales hacia cajas receptoras o consignatario con acta de recepción y cierre de trazabilidad.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#cbcfd4]/10 text-[11px] text-slate-300 flex items-center justify-between font-medium">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Entrega final</span>
                        <span class="text-[10px] text-slate-400 font-mono">Nacional</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Panel Interactivo Detallado del Hito Seleccionado -->
        <div id="pipeline-detail-panel" class="mt-10 p-6 sm:p-8 rounded-3xl bg-[#051f42]/90 border border-[#cbcfd4]/20 shadow-2xl backdrop-blur-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#cbcfd4]/15">
                <div class="flex items-center gap-3">
                    <span id="detail-step-badge" class="w-8 h-8 rounded-lg bg-[#f5782b] text-white flex items-center justify-center font-mono font-bold text-sm">
                        01
                    </span>
                    <div>
                        <h4 id="detail-step-title" class="text-lg font-extrabold text-white">
                            Recepción en Hub & Emisión de PIN
                        </h4>
                        <span id="detail-step-sub" class="text-xs text-[#ff934f] font-semibold">
                            Fase Inicial de Custodia
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs font-mono text-slate-300">
                    <div class="flex items-center gap-1.5 bg-[#020d1c] px-3 py-1.5 rounded-xl border border-[#cbcfd4]/10">
                        <i class="fa-regular fa-clock text-[#f5782b]"></i>
                        <span id="detail-step-duration">24 - 48 Horas</span>
                    </div>
                </div>
            </div>

            <div class="pt-5 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-8">
                    <p id="detail-step-desc" class="text-sm text-slate-300 leading-relaxed">
                        La carga ingresa al almacén internacional de consolidación. Se realiza pesaje certificado en báscula electrónica, verificación física de empaque y emisión del identificador PIN con código QR para seguimiento en la torre de control.
                    </p>
                    <div class="mt-4 pt-3 border-t border-[#cbcfd4]/10 flex items-start gap-2 text-xs text-slate-400">
                        <i class="fa-solid fa-file-shield text-[#f5782b] mt-0.5"></i>
                        <span><strong>Documentación asociada:</strong> <span id="detail-step-docs" class="text-slate-200">Packing List Cifrado, Fotos de Verificación, Registro VIN / Serial</span></span>
                    </div>
                </div>

                <div class="lg:col-span-4 flex justify-start lg:justify-end">
                    <a href="#tracking" class="btn-corporate-primary text-xs !py-3 !px-5 whitespace-nowrap">
                        <i class="fa-solid fa-magnifying-glass mr-1.5"></i>
                        <span>Consultar mi PIN en este hito</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Script del Pipeline Interactivo --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.pipeline-step-card');
    const badge = document.getElementById('detail-step-badge');
    const title = document.getElementById('detail-step-title');
    const sub = document.getElementById('detail-step-sub');
    const duration = document.getElementById('detail-step-duration');
    const desc = document.getElementById('detail-step-desc');
    const docs = document.getElementById('detail-step-docs');

    cards.forEach(card => {
        card.addEventListener('click', function () {
            // Deseleccionar todas
            cards.forEach(c => {
                c.classList.remove('border-[#f5782b]/50', 'glow-accent-sm');
                c.classList.add('border-[#cbcfd4]/15');
            });

            // Seleccionar activa
            this.classList.remove('border-[#cbcfd4]/15');
            this.classList.add('border-[#f5782b]/50', 'glow-accent-sm');

            // Actualizar datos del panel
            const stepNum = this.getAttribute('data-step').padStart(2, '0');
            badge.textContent = stepNum;
            title.textContent = this.getAttribute('data-title');
            sub.textContent = this.getAttribute('data-badge');
            duration.textContent = this.getAttribute('data-duration');
            desc.textContent = this.getAttribute('data-desc');
            docs.textContent = this.getAttribute('data-docs');
        });
    });
});
</script>