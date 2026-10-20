{{-- Centro Interactivo de Preguntas Frecuentes (Glassmorphic FAQ Accordion) --}}
<section id="faq" class="py-24 bg-[#020d1c]/95 border-b border-[#cbcfd4]/10 relative overflow-hidden">
    <div class="absolute inset-0 bg-grid-mesh pointer-events-none opacity-40 -z-10"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Encabezado --}}
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
            <span class="badge-corporate-orange">
                <i class="fa-solid fa-circle-question"></i> Centro de Conocimiento
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                Preguntas Frecuentes
            </h2>
            <p class="text-base text-slate-300 leading-relaxed">
                Claridad, rigor y transparencia. Resolvemos las dudas más habituales sobre nuestras dos divisiones empresariales.
            </p>
        </div>

        {{-- Conmutador de Categorías FAQ --}}
        <div class="flex items-center justify-center gap-3 mb-12" id="faq-category-switcher">
            <button type="button" 
                    data-faq-group="logistica" 
                    class="faq-tab active px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 bg-[#f5782b] text-white shadow-lg shadow-[#f5782b]/30 border border-[#f5782b] flex items-center gap-2">
                <i class="fa-solid fa-ship"></i>
                <span>Logística & Trazabilidad</span>
            </button>
            <button type="button" 
                    data-faq-group="tecnologicos" 
                    class="faq-tab px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition-all duration-200 bg-[#0a254e] text-slate-300 hover:text-white border border-[#cbcfd4]/15 flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-[#ff934f]"></i>
                <span>Servicios Tecnológicos (STI)</span>
            </button>
        </div>

        {{-- Preguntas: Grupo Logística --}}
        <div class="faq-group space-y-4" id="faq-group-logistica">
            
            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-qrcode text-[#f5782b]"></i>
                        ¿Cómo funciona el código PIN de rastreo y con qué frecuencia se actualiza?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        Al recibir su carga en el almacén de consolidación, emitimos un identificador único (ej. <span class="font-mono text-[#ff934f] font-bold">BK-78492</span>). La torre de control SCGI registra los escaneos ópticos en cada hito: recepción en hub, embarque marítimo/aéreo, arribo a patio fiscal Mariel y entrega final, actualizando el estado de inmediato.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-boxes-stacked text-[#f5782b]"></i>
                        ¿Cuál es la diferencia entre FCL y LCL y qué modalidad me conviene?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        <strong class="text-white">FCL (Full Container Load):</strong> El contenedor de 20' o 40' es exclusivo para su empresa, ideal para grandes volúmenes o maquinaria. <br>
                        <strong class="text-white">LCL (Less than Container Load):</strong> Consolidación compartida para paquetería o importaciones medianas, pagando únicamente por los metros cúbicos o peso utilizados.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-stamp text-[#f5782b]"></i>
                        ¿BLANKISOL asiste en los trámites y aranceles ante la Aduana de Cuba?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        Sí. Gestionamos el cotejo documental de manifiestos, clasificación arancelaria y custodia en el patio de verificación del Puerto del Mariel o aeropuerto hasta la liberación y emisión del dictamen aduanal.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-file-contract text-[#f5782b]"></i>
                        ¿Qué datos se necesitan para recibir una cotización de carga rápida?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        Basta con indicar el puerto/ciudad de origen (ej. Panamá o Miami), destino, tipo aproximado de mercancía, volumen/peso estimado y si requiere contenedor completo o paquetería consolidada a través de nuestro cotizador.
                    </p>
                </div>
            </div>

        </div>

        {{-- Preguntas: Grupo Servicios Tecnológicos (STI) --}}
        <div class="faq-group hidden space-y-4" id="faq-group-tecnologicos">
            
            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-calculator text-[#ff934f]"></i>
                        ¿Cómo se calculan los presupuestos en Servicios Tecnológicos Integrales?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        Cada intervención técnica se cotiza a medida. Nuestro equipo realiza un levantamiento técnico preliminar evaluando superficie (m²), metros lineales de ductería, grado de acumulación de grasa o sarro, accesibilidad y si se requiere horario nocturno o parada de planta.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-fire-burner text-[#ff934f]"></i>
                        ¿Con qué frecuencia debe desengrasarse una campana y ducto de cocina industrial?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        En hoteles y cocinas comerciales de uso continuo, los protocolos de seguridad contra incendios recomiendan una limpieza profunda y desengrase mecánico de campanas, filtros y ductos cada 3 a 6 meses para prevenir ignición y sobrecarga en turbinas.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-clock text-[#ff934f]"></i>
                        ¿Es necesario detener la operación de mi empresa durante el servicio?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        No. Diseñamos planes de intervención coordinados: cuadrillas en turnos nocturnos, fines de semana o paradas programadas de turno para asegurar cero paro operacional no contemplado en su producción o servicio a clientes.
                    </p>
                </div>
            </div>

            <div class="glass-panel-3d overflow-hidden rounded-2xl transition-all duration-200">
                <button type="button" class="faq-accordion-header w-full p-6 text-left flex items-center justify-between gap-4 focus:outline-none">
                    <span class="text-sm sm:text-base font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-file-circle-check text-[#ff934f]"></i>
                        ¿Qué certificación o comprobante técnico se entrega al concluir los trabajos?
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-[#cbcfd4] transition-transform duration-300 shrink-0"></i>
                </button>
                <div class="faq-accordion-body max-h-0 overflow-hidden transition-all duration-300 px-6 bg-[#051f42]/40">
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pb-6 pt-2 border-t border-[#cbcfd4]/10">
                        Al finalizar la inspección técnica conjunta con el responsable de su instalación, se firma el Acta de Recepción Conforme y se emite la Constancia de Higienización y Tratamiento Técnico, válida para auditorías internas e inspecciones sanitarias.
                    </p>
                </div>
            </div>

        </div>

        {{-- Callout Box de Consulta Rápida --}}
        <div class="mt-14 p-6 rounded-3xl bg-[#0a254e]/50 border border-[#cbcfd4]/15 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <h4 class="text-sm font-bold text-white">¿Tiene una consulta específica no incluida en este listado?</h4>
                <p class="text-xs text-slate-300">Nuestro equipo comercial y técnico le responderá en menos de 24 horas.</p>
            </div>
            <a href="#contacto" class="btn-corporate-primary text-xs shrink-0 whitespace-nowrap">
                Contactar a un Asesor
            </a>
        </div>

    </div>
</section>

{{-- Script de Acordeón FAQ y Conmutador de Grupos --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Conmutador de grupos
    const faqTabs = document.querySelectorAll('.faq-tab');
    const groupLogistica = document.getElementById('faq-group-logistica');
    const groupTecnologicos = document.getElementById('faq-group-tecnologicos');

    faqTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const group = this.getAttribute('data-faq-group');

            faqTabs.forEach(t => {
                t.classList.remove('active', 'bg-[#f5782b]', 'text-white', 'shadow-lg', 'border-[#f5782b]');
                t.classList.add('bg-[#0a254e]', 'text-slate-300', 'border-[#cbcfd4]/15');
            });
            this.classList.add('active', 'bg-[#f5782b]', 'text-white', 'shadow-lg', 'border-[#f5782b]');
            this.classList.remove('bg-[#0a254e]', 'text-slate-300');

            if (group === 'logistica') {
                groupLogistica.classList.remove('hidden');
                groupTecnologicos.classList.add('hidden');
            } else {
                groupLogistica.classList.add('hidden');
                groupTecnologicos.classList.remove('hidden');
            }
        });
    });

    // 2. Acordeón interactivo
    const accordionHeaders = document.querySelectorAll('.faq-accordion-header');

    accordionHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const body = this.nextElementSibling;
            const chevron = this.querySelector('.fa-chevron-down');
            const isOpen = body.style.maxHeight && body.style.maxHeight !== '0px';

            // Cerrar todos los acordeones
            document.querySelectorAll('.faq-accordion-body').forEach(b => b.style.maxHeight = '0px');
            document.querySelectorAll('.faq-accordion-header .fa-chevron-down').forEach(c => c.classList.remove('rotate-180'));

            if (!isOpen) {
                body.style.maxHeight = body.scrollHeight + 'px';
                chevron.classList.add('rotate-180');
            }
        });
    });
});
</script>
