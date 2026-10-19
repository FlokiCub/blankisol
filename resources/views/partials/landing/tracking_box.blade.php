<section id="tracking" class="relative -mt-10 mb-24 z-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-corporate p-6 sm:p-10 border-2 border-[#f5782b]/30 shadow-2xl relative overflow-hidden">
            <!-- Decorative Accent line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#051f42] via-[#f5782b] to-[#051f42]"></div>

            <div class="text-center mb-6">
                <span class="badge-corporate-orange mb-2">
                    <i class="fa-solid fa-radar"></i> Módulo de Consulta Pública
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Consulta el Estado de tu Carga
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-lg mx-auto">
                    Introduce tu PIN de seguimiento, número de VIN/Serial o identificador de carga para conocer la ubicación y estado actual.
                </p>
            </div>

            <!-- Error Feedback -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-950/70 border border-rose-500/40 text-rose-200 text-xs sm:text-sm font-medium flex items-center justify-between shadow-lg" role="alert">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white p-1" aria-label="Cerrar alerta">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <!-- Search Form -->
            <form method="POST" action="{{ route('tracking.search') }}" id="tracking-form" class="space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#cbcfd4]">
                            <i class="fa-solid fa-barcode text-lg text-[#f5782b]"></i>
                        </div>
                        <input 
                            type="text" 
                            name="pin" 
                            id="tracking-pin-input"
                            value="{{ old('pin') }}" 
                            required 
                            minlength="3"
                            maxlength="50"
                            autocomplete="off"
                            placeholder="Ej: BK-78492, VIN o ID de Carga"
                            class="input-corporate pl-12 pr-4 py-4 text-base sm:text-lg font-mono uppercase tracking-wider placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400"
                            aria-label="Código PIN de rastreo"
                        />
                    </div>

                    <button 
                        type="submit" 
                        id="tracking-submit-btn"
                        class="btn-corporate-primary text-base !py-4 !px-8 flex items-center justify-center gap-2 whitespace-nowrap"
                    >
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        <span>Consultar Envío</span>
                    </button>
                </div>
            </form>

            <!-- Quick Pins for Demo/Test -->
            <div class="mt-5 pt-4 border-t border-[#cbcfd4]/15 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-[#cbcfd4]">
                    <i class="fa-solid fa-circle-info text-[#f5782b]"></i>
                    <span class="font-medium">Consultas de prueba disponibles:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setTrackingPin('BK-78492')" class="px-3 py-1.5 rounded-lg bg-[#051f42] hover:bg-[#0a254e] border border-[#cbcfd4]/30 hover:border-[#f5782b] text-slate-200 font-mono text-xs transition-colors">
                        BK-78492 <span class="text-[10px] text-[#cbcfd4]">(Vehículo)</span>
                    </button>
                    <button type="button" onclick="setTrackingPin('BK-SOLAR01')" class="px-3 py-1.5 rounded-lg bg-[#051f42] hover:bg-[#0a254e] border border-[#cbcfd4]/30 hover:border-[#f5782b] text-slate-200 font-mono text-xs transition-colors">
                        BK-SOLAR01 <span class="text-[10px] text-[#cbcfd4]">(Kit Solar)</span>
                    </button>
                </div>
            </div>

            <!-- Privacidad y Transparencia -->
            <div class="mt-4 text-center">
                <span class="text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-lock text-[10px] text-[#f5782b]"></i>
                    Búsqueda protegida. No se muestran datos personales ni fiscales de personas naturales en consultas públicas.
                </span>
            </div>
        </div>
    </div>
</section>