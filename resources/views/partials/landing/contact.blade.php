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

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div>
                                <label for="contact-nombre" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Nombre o Persona de Contacto <span class="text-[#f5782b]">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-nombre" 
                                    name="nombre" 
                                    value="{{ old('nombre') }}" 
                                    required 
                                    maxlength="150"
                                    placeholder="Ej: Carlos Mendoza"
                                    class="input-corporate"
                                />
                            </div>

                            <!-- Empresa -->
                            <div>
                                <label for="contact-empresa" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Empresa o Razón Social (Opcional)
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-empresa" 
                                    name="empresa" 
                                    value="{{ old('empresa') }}" 
                                    maxlength="150"
                                    placeholder="Ej: Inversiones Caribe S.A."
                                    class="input-corporate"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div>
                                <label for="contact-email" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Correo Electrónico <span class="text-[#f5782b]">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="contact-email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    maxlength="150"
                                    placeholder="nombre@empresa.com"
                                    class="input-corporate"
                                />
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="contact-telefono" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Teléfono / WhatsApp
                                </label>
                                <input 
                                    type="tel" 
                                    id="contact-telefono" 
                                    name="telefono" 
                                    value="{{ old('telefono') }}" 
                                    maxlength="50"
                                    placeholder="+53 / +58 / +1..."
                                    class="input-corporate"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Modalidad -->
                            <div class="sm:col-span-1">
                                <label for="contact-modalidad" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Modalidad <span class="text-[#f5782b]">*</span>
                                </label>
                                <select id="contact-modalidad" name="modalidad" class="input-corporate bg-[#020d1c] text-white">
                                    <option value="maritimo" {{ old('modalidad') == 'maritimo' ? 'selected' : '' }}>Marítimo</option>
                                    <option value="aereo" {{ old('modalidad') == 'aereo' ? 'selected' : '' }}>Aéreo</option>
                                    <option value="terrestre" {{ old('modalidad') == 'terrestre' ? 'selected' : '' }}>Terrestre</option>
                                    <option value="aduanal" {{ old('modalidad') == 'aduanal' ? 'selected' : '' }}>Gestión Aduanal</option>
                                    <option value="otro" {{ old('modalidad') == 'otro' ? 'selected' : '' }}>Otro Requerimiento</option>
                                </select>
                            </div>

                            <!-- Origen -->
                            <div class="sm:col-span-1">
                                <label for="contact-origen" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Ciudad / Puerto Origen
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-origen" 
                                    name="origen" 
                                    value="{{ old('origen') }}" 
                                    maxlength="150"
                                    placeholder="Ej: Miami / Colón"
                                    class="input-corporate"
                                />
                            </div>

                            <!-- Destino -->
                            <div class="sm:col-span-1">
                                <label for="contact-destino" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                    Puerto / Destino Final
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-destino" 
                                    name="destino" 
                                    value="{{ old('destino') }}" 
                                    maxlength="150"
                                    placeholder="Ej: Mariel / La Habana"
                                    class="input-corporate"
                                />
                            </div>
                        </div>

                        <!-- Mercancía -->
                        <div>
                            <label for="contact-mercancia" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                Tipo de Carga / Mercancía
                            </label>
                            <input 
                                type="text" 
                                id="contact-mercancia" 
                                name="mercancia" 
                                value="{{ old('mercancia') }}" 
                                maxlength="200"
                                placeholder="Ej: Vehículos eléctricos, paneles solares, repuestos, paquetería..."
                                class="input-corporate"
                            />
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="contact-descripcion" class="block text-xs font-semibold text-[#cbcfd4] mb-1.5">
                                Detalle de tu Solicitud <span class="text-[#f5782b]">*</span>
                            </label>
                            <textarea 
                                id="contact-descripcion" 
                                name="descripcion" 
                                rows="3" 
                                required 
                                maxlength="3000"
                                placeholder="Indica volumen estimado, peso, fechas tentativas o dudas específicas..."
                                class="input-corporate resize-none"
                            >{{ old('descripcion') }}</textarea>
                        </div>

                        <!-- Aceptación Privacidad -->
                        <div class="pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="aceptado_privacidad" 
                                    value="1" 
                                    required 
                                    checked
                                    class="mt-1 h-4 w-4 rounded border-slate-700 text-[#f5782b] focus:ring-[#f5782b] bg-[#020d1c]"
                                />
                                <span class="text-xs text-slate-300 leading-snug">
                                    Acepto el tratamiento de mis datos de contacto exclusivamente para la gestión y seguimiento de esta cotización comercial conforme a las políticas de confidencialidad de BLANKISOL.
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="btn-corporate-primary w-full text-base !py-4 shadow-xl">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                <span>Enviar Solicitud de Cotización</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</section>