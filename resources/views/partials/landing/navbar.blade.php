<header class="sticky top-0 z-50 bg-[#051f42]/95 backdrop-blur-xl border-b border-[#cbcfd4]/15 shadow-lg shadow-[#020d1c]/40 transition-all duration-300" id="site-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-[#f5782b] rounded-xl p-1">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-[#f5782b]/30 flex items-center justify-center text-[#f5782b] shadow-md shadow-[#051f42]/50 group-hover:scale-105 group-hover:border-[#f5782b] transition-all duration-300">
                    <i class="fa-solid fa-ship text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white block leading-tight font-sans">
                        BLANKISOL
                    </span>
                    <span class="text-[10px] font-bold tracking-widest text-[#cbcfd4] uppercase block">
                        Logística & Trazabilidad
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-8" aria-label="Navegación principal">
                <a href="#hero" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">Inicio</a>
                <a href="#nosotros" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">Nosotros</a>
                <a href="#servicios" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">Servicios</a>
                <a href="#proceso" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">Cómo Funciona</a>
                <a href="#tracking" class="text-sm font-semibold text-[#f5782b] hover:text-[#ff934f] transition-colors py-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass-location text-xs"></i>
                    <span>Rastrear Carga</span>
                </a>
                <a href="#contacto" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">Contacto</a>
            </nav>

            <!-- Actions / CTA -->
            <div class="hidden sm:flex items-center gap-4">
                <a href="#contacto" class="btn-corporate-primary text-xs !py-2.5 !px-5">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Cotizar Envío</span>
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-[#0a254e] border border-[#cbcfd4]/20 focus:outline-none focus:ring-2 focus:ring-[#f5782b]" aria-expanded="false" aria-label="Abrir menú">
                <i class="fa-solid fa-bars text-xl" id="menu-icon-open"></i>
                <i class="fa-solid fa-xmark text-xl hidden" id="menu-icon-close"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div class="md:hidden hidden bg-[#051f42]/98 border-t border-[#cbcfd4]/15 px-4 pt-3 pb-6 shadow-2xl space-y-3" id="mobile-menu">
        <a href="#hero" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">Inicio</a>
        <a href="#nosotros" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">Nosotros</a>
        <a href="#servicios" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">Servicios</a>
        <a href="#proceso" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">Cómo Funciona</a>
        <a href="#tracking" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-[#f5782b] bg-[#f5782b]/10 border border-[#f5782b]/20">
            <i class="fa-solid fa-magnifying-glass-location mr-2"></i> Rastrear Carga
        </a>
        <a href="#contacto" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">Contacto</a>

        <div class="pt-4 border-t border-[#cbcfd4]/15 flex flex-col gap-3">
            <a href="#contacto" class="btn-corporate-primary w-full text-center !py-3">
                Solicitar Cotización Inmediata
            </a>
        </div>
    </div>
</header>