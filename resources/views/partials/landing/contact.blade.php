<section id="contacto" class="py-24 bg-[#020d1c]/90 border-t border-[#cbcfd4]/10 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Contact Info Column -->
            <div class="lg:col-span-5 space-y-8">
                <div>
                    <span class="badge-corporate-orange mb-3">
                        <i class="fa-solid fa-headset"></i> Atención Comercial
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                        Inicia tu Próximo Envío con BLANKISOL
                    </h2>
                    <p class="text-sm text-slate-300 mt-3 leading-relaxed">
                        Cuéntanos los requerimientos de tu carga. Nuestro equipo analizará las mejores rutas, modalidades de transporte y tarifas para tu proyecto.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-[#051f42]/80 border border-[#cbcfd4]/15 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#f5782b]/15 text-[#f5782b] flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <div>
                            <span class="text-xs text-[#cbcfd4] uppercase font-bold block">Cotizaciones & Ventas</span>
                            <span class="text-sm font-semibold text-white">comercial@blankisol.com</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Respuesta en menos de 24 horas laborables</span>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#051f42]/80 border border-[#cbcfd4]/15 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#f5782b]/15 text-[#f5782b] flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <span class="text-xs text-[#cbcfd4] uppercase font-bold block">Mesa de Ayuda & WhatsApp</span>
                            <span class="text-sm font-semibold text-white">Atención vía Plataforma SCGI</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Notificaciones automáticas de arribo</span>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#051f42]/80 border border-[#cbcfd4]/15 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#f5782b]/15 text-[#f5782b] flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-xs text-[#cbcfd4] uppercase font-bold block">Horario de Operaciones</span>
                            <span class="text-sm font-semibold text-white">Lunes a Viernes: 08:30 - 17:30</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Torre de control digital: 24/7</span>
                        </div>
                    </div>
                </div>

                <!-- Callout Box -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-[#0a254e] to-[#051f42] border border-[#f5782b]/30">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#f5782b] mb-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Privacidad & Seguridad Garantizada</span>
                    </div>
                    <p class="text-xs text-slate-300">
                        La información de tu carga y datos comerciales se procesan con estricta confidencialidad para fines exclusivos de cotización y servicio.
                    </p>
                </div>
            </div>

            <!-- Form Column -->
            <div class="lg:col-span-7">
                <div class="card-corporate p-8 sm:p-10 border border-[#cbcfd4]/20 shadow-2xl relative">
                    
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                        Solicitud de Cotización Logística
                    </h3>
                    <p class="text-xs text-slate-300 mb-6">
                        Completa el siguiente formulario para recibir una propuesta adaptada a tu carga.
                    </p>

                    <!-- Success Message -->
                    @if(session('contact_success'))
                        <div class="mb-6 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 text-xs sm:text-sm font-medium flex items-center justify-between shadow-lg" role="alert">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                                <span>{{ session('contact_success') }}</span>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white p-1" aria-label="Cerrar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-rose-200 text-xs font-medium space-y-1" role="alert">
                            <div class="flex items-center gap-2 font-bold text-rose-300">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>Por favor corrige los siguientes campos:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-300">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Stepper Progress Bar --}}
                    <div class="mb-8" id="quote-wizard-steps">
                        <div class="flex items-center justify-between relative">
                            {{-- Línea conectora de fondo --}}
                            <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-0.5 bg-[#cbcfd4]/15 -z-0"></div>
                            <div id="wizard-progress-bar" class="absolute left-6 top-1/2 -translate-y-1/2 h-0.5 bg-[#f5782b] transition-all duration-300 -z-0" style="width: 0%;"></div>

                            {{-- Paso 1 --}}
                            <button type="button" class="wizard-step-indicator active flex flex-col items-center gap-1.5 focus:outline-none relative z-10" data-target-step="1">
                                <span class="w-9 h-9 rounded-full bg-[#f5782b] text-white flex items-center justify-center text-xs font-bold shadow-md shadow-[#f5782b]/40 ring-4 ring-[#051f42]">1</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-white">Modalidad</span>
                            </button>

                            {{-- Paso 2 --}}
                            <button type="button" class="wizard-step-indicator flex flex-col items-center gap-1.5 focus:outline-none relative z-10" data-target-step="2">
                                <span class="w-9 h-9 rounded-full bg-[#0a254e] text-slate-400 border border-[#cbcfd4]/20 flex items-center justify-center text-xs font-bold ring-4 ring-[#051f42]">2</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Carga</span>
                            </button>

                            {{-- Paso 3 --}}
                            <button type="button" class="wizard-step-indicator flex flex-col items-center gap-1.5 focus:outline-none relative z-10" data-target-step="3">
                                <span class="w-9 h-9 rounded-full bg-[#0a254e] text-slate-400 border border-[#cbcfd4]/20 flex items-center justify-center text-xs font-bold ring-4 ring-[#051f42]">3</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Contacto</span>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('contact.store') }}" id="logistics-quote-wizard-form" class="space-y-6">
                        @csrf

                        {{-- PASO 1: Modalidad y Ruta --}}
                        <div class="wizard-pane space-y-5" id="wizard-pane-1">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#cbcfd4] mb-3">
                                    1. Seleccione la Modalidad de Transporte <span class="text-[#f5782b]">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="modalidad-cards-selector">
                                    <button type="button" 
                                            data-modalidad="maritimo" 
                                            class="modalidad-card p-3 rounded-2xl bg-[#0a254e]/80 border-2 border-[#f5782b] text-left transition-all group flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-xl bg-[#051f42] text-[#f5782b] flex items-center justify-center text-sm mb-2 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-ship"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Marítimo</span>
                                            <span class="text-[10px] text-slate-300 block">FCL / LCL Contenedor</span>
                                        </div>
                                    </button>

                                    <button type="button" 
                                            data-modalidad="aereo" 
                                            class="modalidad-card p-3 rounded-2xl bg-[#0a254e]/40 border-2 border-[#cbcfd4]/15 hover:border-[#f5782b]/50 text-left transition-all group flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-xl bg-[#051f42] text-slate-300 group-hover:text-[#f5782b] flex items-center justify-center text-sm mb-2 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-plane-departure"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Aéreo</span>
                                            <span class="text-[10px] text-slate-400 block">Prioritario / Paquetería</span>
                                        </div>
                                    </button>

                                    <button type="button" 
                                            data-modalidad="terrestre" 
                                            class="modalidad-card p-3 rounded-2xl bg-[#0a254e]/40 border-2 border-[#cbcfd4]/15 hover:border-[#f5782b]/50 text-left transition-all group flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-xl bg-[#051f42] text-slate-300 group-hover:text-[#f5782b] flex items-center justify-center text-sm mb-2 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-truck-moving"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Terrestre</span>
                                            <span class="text-[10px] text-slate-400 block">Distribución Provincial</span>
                                        </div>
                                    </button>

                                    <button type="button" 
                                            data-modalidad="aduanal" 
                                            class="modalidad-card p-3 rounded-2xl bg-[#0a254e]/40 border-2 border-[#cbcfd4]/15 hover:border-[#f5782b]/50 text-left transition-all group flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-xl bg-[#051f42] text-slate-300 group-hover:text-[#f5782b] flex items-center justify-center text-sm mb-2 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-stamp"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Aduanal</span>
                                            <span class="text-[10px] text-slate-400 block">Despacho & Aranceles</span>
                                        </div>
                                    </button>

                                    <button type="button" 
                                            data-modalidad="otro" 
                                            class="modalidad-card p-3 rounded-2xl bg-[#0a254e]/40 border-2 border-[#cbcfd4]/15 hover:border-[#f5782b]/50 text-left transition-all group flex flex-col justify-between col-span-2 sm:col-span-1">
                                        <div class="w-8 h-8 rounded-xl bg-[#051f42] text-slate-300 group-hover:text-[#f5782b] flex items-center justify-center text-sm mb-2 group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-boxes-packing"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-white block">Combinado</span>
                                            <span class="text-[10px] text-slate-400 block">Otro Requerimiento</span>
                                        </div>
                                    </button>
                                </div>

                                {{-- Input select real oculto pero accesible para validación del backend --}}
                                <select id="contact-modalidad" name="modalidad" class="hidden">
                                    <option value="maritimo" {{ old('modalidad', 'maritimo') == 'maritimo' ? 'selected' : '' }}>Marítimo</option>
                                    <option value="aereo" {{ old('modalidad') == 'aereo' ? 'selected' : '' }}>Aéreo</option>
                                    <option value="terrestre" {{ old('modalidad') == 'terrestre' ? 'selected' : '' }}>Terrestre</option>
                                    <option value="aduanal" {{ old('modalidad') == 'aduanal' ? 'selected' : '' }}>Gestión Aduanal</option>
                                    <option value="otro" {{ old('modalidad') == 'otro' ? 'selected' : '' }}>Otro Requerimiento</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                <div>
                                    <label for="contact-origen" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Ciudad o Puerto de Origen
                                    </label>
                                    <input type="text" 
                                           id="contact-origen" 
                                           name="origen" 
                                           value="{{ old('origen') }}" 
                                           maxlength="150"
                                           placeholder="Ej: Colón, Panamá / Miami, EE.UU."
                                           class="input-corporate" />
                                </div>

                                <div>
                                    <label for="contact-destino" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Puerto de Entrada o Destino
                                    </label>
                                    <input type="text" 
                                           id="contact-destino" 
                                           name="destino" 
                                           value="{{ old('destino', 'Puerto del Mariel, Cuba') }}" 
                                           maxlength="150"
                                           placeholder="Ej: Mariel / La Habana"
                                           class="input-corporate" />
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="button" id="btn-wizard-next-1" class="btn-corporate-primary text-xs !py-3 !px-6">
                                    <span>Continuar: Detalle de Carga</span>
                                    <i class="fa-solid fa-arrow-right ml-1.5"></i>
                                </button>
                            </div>
                        </div>

                        {{-- PASO 2: Mercancía y Descripción --}}
                        <div class="wizard-pane hidden space-y-5" id="wizard-pane-2">
                            <div>
                                <label for="contact-mercancia" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Tipo de Carga o Mercancía
                                </label>
                                <input type="text" 
                                       id="contact-mercancia" 
                                       name="mercancia" 
                                       value="{{ old('mercancia') }}" 
                                       maxlength="200"
                                       placeholder="Ej: Repuestos automotrices, motos eléctricas, equipos solares..."
                                       class="input-corporate" />
                            </div>

                            <div>
                                <label for="contact-descripcion" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Detalle del Requerimiento / Volumen Estimado <span class="text-[#f5782b]">*</span>
                                </label>
                                <textarea id="contact-descripcion" 
                                          name="descripcion" 
                                          rows="4" 
                                          required 
                                          maxlength="3000"
                                          placeholder="Indique peso aproximado, cantidad de bultos o contenedor (20'/40'), fechas tentativas de envío o si requiere trámite aduanal en destino..."
                                          class="input-corporate resize-none">{{ old('descripcion') }}</textarea>
                            </div>

                            <div class="pt-4 flex items-center justify-between">
                                <button type="button" id="btn-wizard-prev-2" class="btn-corporate-secondary text-xs !py-3 !px-5">
                                    <i class="fa-solid fa-arrow-left mr-1.5"></i>
                                    <span>Anterior</span>
                                </button>
                                <button type="button" id="btn-wizard-next-2" class="btn-corporate-primary text-xs !py-3 !px-6">
                                    <span>Continuar: Datos de Contacto</span>
                                    <i class="fa-solid fa-arrow-right ml-1.5"></i>
                                </button>
                            </div>
                        </div>

                        {{-- PASO 3: Datos de Contacto y Envío --}}
                        <div class="wizard-pane hidden space-y-5" id="wizard-pane-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="contact-nombre" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Nombre Completo <span class="text-[#f5782b]">*</span>
                                    </label>
                                    <input type="text" 
                                           id="contact-nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           required 
                                           maxlength="150"
                                           placeholder="Ej: Carlos Mendoza"
                                           class="input-corporate" />
                                </div>

                                <div>
                                    <label for="contact-empresa" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Empresa o Razón Social (Opcional)
                                    </label>
                                    <input type="text" 
                                           id="contact-empresa" 
                                           name="empresa" 
                                           value="{{ old('empresa') }}" 
                                           maxlength="150"
                                           placeholder="Ej: Inversiones Caribe S.A."
                                           class="input-corporate" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="contact-email" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Correo Electrónico <span class="text-[#f5782b]">*</span>
                                    </label>
                                    <input type="email" 
                                           id="contact-email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           maxlength="150"
                                           placeholder="nombre@empresa.com"
                                           class="input-corporate" />
                                </div>

                                <div>
                                    <label for="contact-telefono" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                        Teléfono / WhatsApp
                                    </label>
                                    <input type="tel" 
                                           id="contact-telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono') }}" 
                                           maxlength="50"
                                           placeholder="+53 5xxx xxxx / +58..."
                                           class="input-corporate" />
                                </div>
                            </div>

                            <div class="pt-2">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           name="aceptado_privacidad" 
                                           value="1" 
                                           required 
                                           checked
                                           class="mt-1 h-4 w-4 rounded border-slate-700 text-[#f5782b] focus:ring-[#f5782b] bg-[#020d1c]" />
                                    <span class="text-xs text-slate-300 leading-snug">
                                        Acepto el tratamiento de mis datos exclusivamente para la gestión y seguimiento de esta cotización de carga conforme a las políticas de BLANKISOL SURL.
                                    </span>
                                </label>
                            </div>

                            <div class="pt-4 flex items-center justify-between">
                                <button type="button" id="btn-wizard-prev-3" class="btn-corporate-secondary text-xs !py-3 !px-5">
                                    <i class="fa-solid fa-arrow-left mr-1.5"></i>
                                    <span>Anterior</span>
                                </button>
                                <button type="submit" class="btn-corporate-primary text-sm !py-3.5 !px-8 shadow-xl">
                                    <i class="fa-solid fa-paper-plane mr-2"></i>
                                    <span>Enviar Solicitud de Cotización</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

{{-- Script del Wizard de Cotización --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pane1 = document.getElementById('wizard-pane-1');
    const pane2 = document.getElementById('wizard-pane-2');
    const pane3 = document.getElementById('wizard-pane-3');
    const progressBar = document.getElementById('wizard-progress-bar');
    const stepIndicators = document.querySelectorAll('.wizard-step-indicator');

    const next1 = document.getElementById('btn-wizard-next-1');
    const next2 = document.getElementById('btn-wizard-next-2');
    const prev2 = document.getElementById('btn-wizard-prev-2');
    const prev3 = document.getElementById('btn-wizard-prev-3');

    const modalidadCards = document.querySelectorAll('.modalidad-card');
    const modalidadSelect = document.getElementById('contact-modalidad');

    function goToStep(step) {
        // Ocultar todos los paneles
        pane1.classList.add('hidden');
        pane2.classList.add('hidden');
        pane3.classList.add('hidden');

        // Mostrar panel activo
        if (step === 1) pane1.classList.remove('hidden');
        if (step === 2) pane2.classList.remove('hidden');
        if (step === 3) pane3.classList.remove('hidden');

        // Actualizar barra de progreso
        const progressWidth = step === 1 ? '0%' : (step === 2 ? '50%' : '100%');
        if (progressBar) progressBar.style.width = progressWidth;

        // Actualizar indicadores
        stepIndicators.forEach((ind, idx) => {
            const stepNum = idx + 1;
            const badgeSpan = ind.querySelector('span:first-child');
            const labelSpan = ind.querySelector('span:last-child');

            if (stepNum <= step) {
                badgeSpan.classList.add('bg-[#f5782b]', 'text-white');
                badgeSpan.classList.remove('bg-[#0a254e]', 'text-slate-400');
                labelSpan.classList.add('text-white');
                labelSpan.classList.remove('text-slate-400');
            } else {
                badgeSpan.classList.remove('bg-[#f5782b]', 'text-white');
                badgeSpan.classList.add('bg-[#0a254e]', 'text-slate-400');
                labelSpan.classList.remove('text-white');
                labelSpan.classList.add('text-slate-400');
            }
        });
    }

    if (next1) next1.addEventListener('click', () => goToStep(2));
    if (next2) next2.addEventListener('click', () => {
        const descField = document.getElementById('contact-descripcion');
        if (descField && !descField.value.trim()) {
            descField.focus();
            descField.classList.add('ring-2', 'ring-rose-500');
            setTimeout(() => descField.classList.remove('ring-2', 'ring-rose-500'), 1500);
            return;
        }
        goToStep(3);
    });
    if (prev2) prev2.addEventListener('click', () => goToStep(1));
    if (prev3) prev3.addEventListener('click', () => goToStep(2));

    // Clic en pasos superiores
    stepIndicators.forEach(ind => {
        ind.addEventListener('click', function () {
            const target = parseInt(this.getAttribute('data-target-step'), 10);
            if (target) goToStep(target);
        });
    });

    // Selección de modalidad interactiva
    modalidadCards.forEach(card => {
        card.addEventListener('click', function () {
            const mod = this.getAttribute('data-modalidad');
            if (modalidadSelect && mod) {
                modalidadSelect.value = mod;

                modalidadCards.forEach(c => {
                    c.classList.remove('border-[#f5782b]', 'bg-[#0a254e]/80');
                    c.classList.add('border-[#cbcfd4]/15', 'bg-[#0a254e]/40');
                    const icon = c.querySelector('i');
                    if (icon) {
                        icon.classList.remove('text-[#f5782b]');
                        icon.classList.add('text-slate-300');
                    }
                });

                this.classList.remove('border-[#cbcfd4]/15', 'bg-[#0a254e]/40');
                this.classList.add('border-[#f5782b]', 'bg-[#0a254e]/80');
                const activeIcon = this.querySelector('i');
                if (activeIcon) {
                    activeIcon.classList.remove('text-slate-300');
                    activeIcon.classList.add('text-[#f5782b]');
                }
            }
        });
    });
});
</script>

        </div>

    </div>
</section>