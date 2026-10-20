{{-- Widget Flotante de Contacto Inmediato (Floating Contact Hub) --}}
<div id="floating-contact-hub" class="fixed bottom-6 right-6 z-50 font-sans" x-data="{ open: false }">
    
    {{-- Panel Desplegable Glassmorphic --}}
    <div id="contact-hub-panel" 
         class="hidden mb-4 w-80 sm:w-96 rounded-3xl bg-[#051f42]/95 border border-[#cbcfd4]/20 shadow-2xl backdrop-blur-2xl p-5 space-y-4 text-white transform transition-all duration-300 origin-bottom-right scale-95 opacity-0">
        
        {{-- Cabecera del Panel --}}
        <div class="flex items-center justify-between pb-3 border-b border-[#cbcfd4]/15">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-[#f5782b]/40 p-1 flex items-center justify-center shadow-md overflow-hidden">
                    <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white leading-tight">Mesa de Ayuda Directa</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-semibold text-emerald-300">Asesores en línea</span>
                    </div>
                </div>
            </div>
            <button type="button" 
                    id="btn-close-hub" 
                    class="w-7 h-7 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors"
                    aria-label="Cerrar ventana de contacto">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Selector Rápido WhatsApp por División --}}
        <div class="space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                Seleccione el canal de WhatsApp:
            </span>

            {{-- Canal Logística --}}
            <a href="https://wa.me/584120000000?text=Hola%20BLANKISOL,%20solicito%20informaci%C3%B3n%20sobre%20env%C3%ADos%20mar%C3%ADtimos/a%C3%A9reos%20y%20trazabilidad" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="flex items-center justify-between p-3 rounded-2xl bg-[#0a254e]/80 hover:bg-[#0a254e] border border-[#cbcfd4]/15 hover:border-[#25D366]/50 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#25D366]/20 text-[#25D366] flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-white block group-hover:text-[#25D366] transition-colors">
                            Logística & Cargas
                        </span>
                        <span class="text-[11px] text-slate-300 block">
                            Envíos marítimos, aéreos y aduanas
                        </span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition-all"></i>
            </a>

            {{-- Canal Servicios Tecnológicos --}}
            <a href="https://wa.me/5356142721?text=Hola%20BLANKISOL,%20deseo%20cotizar%20Servicios%20Tecnol%C3%B3gicos%20Integrales%20(limpieza/mantenimiento)" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="flex items-center justify-between p-3 rounded-2xl bg-[#0a254e]/80 hover:bg-[#0a254e] border border-[#cbcfd4]/15 hover:border-[#f5782b]/50 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#f5782b]/20 text-[#f5782b] flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-white block group-hover:text-[#ff934f] transition-colors">
                            Servicios Tecnológicos
                        </span>
                        <span class="text-[11px] text-slate-300 block">
                            Limpieza industrial, cocinas y obras
                        </span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition-all"></i>
            </a>
        </div>

        {{-- Rastreo PIN Rápido Modal Express --}}
        <div class="p-3.5 rounded-2xl bg-[#020d1c]/80 border border-[#cbcfd4]/15 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-[#f5782b]"></i>
                    Rastreo Rápido con PIN
                </span>
                <span class="text-[9px] font-mono text-slate-400">PIN único</span>
            </div>

            <form action="{{ route('tracking.search') }}" method="GET" class="flex items-center gap-2">
                <div class="relative flex-grow">
                    <input type="text" 
                           name="pin" 
                           placeholder="Ej. BK-78492" 
                           required 
                           maxlength="20"
                           class="w-full bg-[#051f42] border border-[#cbcfd4]/30 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 uppercase font-mono tracking-wider focus:outline-none focus:border-[#f5782b] focus:ring-1 focus:ring-[#f5782b]">
                </div>
                <button type="submit" 
                        class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-[#f5782b] to-[#e0651a] hover:from-[#ff934f] hover:to-[#f5782b] text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center gap-1.5"
                        title="Consultar PIN">
                    <span>Rastrear</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </form>
        </div>

        {{-- Enlaces Rápidos de Cotización --}}
        <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400">
            <a href="{{ request()->routeIs('tech_services.*') ? '#solicitar-presupuesto' : '#contacto' }}" 
               id="hub-link-cotizar"
               class="hover:text-[#f5782b] transition-colors flex items-center gap-1 font-medium">
                <i class="fa-solid fa-file-invoice-dollar text-[10px]"></i>
                <span>Ir al formulario de cotización</span>
            </a>
            <span class="text-slate-600">•</span>
            <span class="text-[10px] text-slate-400">Respuesta &lt; 24h</span>
        </div>

    </div>

    {{-- Botón Disparador Flotante con Tooltip --}}
    <div class="flex items-center justify-end gap-3">
        {{-- Tooltip sutil en desktop --}}
        <div id="contact-hub-tooltip" 
             class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#020d1c]/90 border border-[#cbcfd4]/20 text-white text-xs font-semibold shadow-xl backdrop-blur-md transition-opacity duration-300 pointer-events-none">
            <span class="w-2 h-2 rounded-full bg-[#25D366] animate-pulse"></span>
            <span>¿Podemos ayudarte?</span>
        </div>

        {{-- Botón Principal --}}
        <button type="button" 
                id="btn-trigger-hub" 
                class="relative w-14 h-14 rounded-full bg-gradient-to-br from-[#f5782b] via-[#ea6c1f] to-[#d85714] text-white flex items-center justify-center text-xl shadow-xl shadow-[#f5782b]/40 border-2 border-white/20 hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#f5782b]/40"
                aria-label="Abrir centro de ayuda y WhatsApp"
                aria-expanded="false">
            
            {{-- Pulso perimetral lumínico --}}
            <span class="absolute -inset-1 rounded-full bg-[#f5782b]/30 animate-ping pointer-events-none"></span>

            {{-- Ícono cerrado (Headset + WhatsApp) --}}
            <i class="fa-solid fa-comments transition-transform duration-300" id="icon-hub-trigger"></i>
        </button>
    </div>

</div>

{{-- Script de Comportamiento del Hub Flotante --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const triggerBtn = document.getElementById('btn-trigger-hub');
    const closeBtn = document.getElementById('btn-close-hub');
    const hubPanel = document.getElementById('contact-hub-panel');
    const triggerIcon = document.getElementById('icon-hub-trigger');
    const tooltip = document.getElementById('contact-hub-tooltip');
    const hubLinkCotizar = document.getElementById('hub-link-cotizar');

    let isOpen = false;

    function openHub() {
        isOpen = true;
        hubPanel.classList.remove('hidden');
        setTimeout(() => {
            hubPanel.classList.remove('scale-95', 'opacity-0');
            hubPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
        triggerIcon.classList.remove('fa-comments');
        triggerIcon.classList.add('fa-xmark');
        triggerBtn.setAttribute('aria-expanded', 'true');
        if (tooltip) tooltip.classList.add('opacity-0');
    }

    function closeHub() {
        isOpen = false;
        hubPanel.classList.remove('scale-100', 'opacity-100');
        hubPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            hubPanel.classList.add('hidden');
        }, 300);
        triggerIcon.classList.remove('fa-xmark');
        triggerIcon.classList.add('fa-comments');
        triggerBtn.setAttribute('aria-expanded', 'false');
        if (tooltip) tooltip.classList.remove('opacity-0');
    }

    if (triggerBtn && hubPanel) {
        triggerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isOpen) {
                closeHub();
            } else {
                openHub();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeHub();
        });
    }

    // Cerrar al hacer clic en el enlace interno de cotizar
    if (hubLinkCotizar) {
        hubLinkCotizar.addEventListener('click', function () {
            closeHub();
        });
    }

    // Cerrar al hacer clic fuera del panel
    document.addEventListener('click', function (e) {
        if (isOpen && !hubPanel.contains(e.target) && !triggerBtn.contains(e.target)) {
            closeHub();
        }
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            closeHub();
        }
    });
});
</script>
