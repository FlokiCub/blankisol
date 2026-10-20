<header class="sticky top-0 z-50 bg-[#051f42]/95 backdrop-blur-xl border-b border-[#cbcfd4]/15 shadow-lg shadow-[#020d1c]/40 transition-all duration-300" id="site-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-[#f5782b] rounded-xl p-1" title="BLANKISOL SURL - Inicio">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-[#f5782b]/30 p-1 flex items-center justify-center shadow-md shadow-[#051f42]/50 group-hover:scale-105 group-hover:border-[#f5782b] transition-all duration-300 overflow-hidden">
                    <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL SURL" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white block leading-tight font-sans">
                        BLANKISOL <span class="text-xs text-[#f5782b] font-bold">SURL</span>
                    </span>
                    <span class="text-[10px] font-bold tracking-widest text-[#cbcfd4] uppercase block">
                        Soluciones Corporativas Integrales
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-7" aria-label="Navegación principal">
                <a href="{{ route('home') }}" class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-[#f5782b]' : 'text-slate-200' }} hover:text-[#f5782b] transition-colors py-1">
                    Inicio
                </a>

                <a href="{{ route('home') }}#nosotros" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">
                    Nosotros
                </a>

                <!-- Dropdown: Áreas de Negocio -->
                <div class="relative group">
                    <button type="button" class="text-sm font-semibold {{ request()->routeIs('tech_services.*') ? 'text-[#f5782b]' : 'text-slate-200' }} hover:text-[#f5782b] transition-colors py-2 flex items-center gap-1.5 focus:outline-none focus:text-[#f5782b]" id="business-areas-menu-button" aria-expanded="false" aria-haspopup="true">
                        <span>Áreas de Negocio</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-[#cbcfd4] group-hover:rotate-180 transition-transform duration-200"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="absolute left-0 top-full pt-2 w-72 hidden group-hover:block group-focus-within:block transition-all duration-200 z-50">
                        <div class="bg-[#051f42] border border-[#cbcfd4]/20 rounded-2xl shadow-2xl p-2.5 space-y-1 backdrop-blur-xl">
                            <!-- Opción 1: Logística y Trazabilidad -->
                            <a href="{{ route('home') }}#logistica" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-[#0a254e] transition-colors group/item">
                                <div class="w-8 h-8 rounded-lg bg-[#0a3166] text-[#f5782b] flex items-center justify-center text-sm shrink-0 mt-0.5 group-hover/item:bg-[#f5782b] group-hover/item:text-white transition-colors">
                                    <i class="fa-solid fa-ship"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block group-hover/item:text-[#f5782b] transition-colors">
                                        Logística & Trazabilidad
                                    </span>
                                    <span class="text-[11px] text-[#cbcfd4] block leading-tight mt-0.5">
                                        Transporte marítimo, aéreo, terrestre y seguimiento
                                    </span>
                                </div>
                            </a>

                            <!-- Opción 2: Servicios Tecnológicos Integrales -->
                            <a href="{{ route('tech_services.index') }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-[#0a254e] transition-colors group/item {{ request()->routeIs('tech_services.*') ? 'bg-[#0a254e]/80 border border-[#f5782b]/30' : '' }}">
                                <div class="w-8 h-8 rounded-lg bg-[#0a3166] text-[#f5782b] flex items-center justify-center text-sm shrink-0 mt-0.5 group-hover/item:bg-[#f5782b] group-hover/item:text-white transition-colors">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block group-hover/item:text-[#f5782b] transition-colors">
                                        Servicios Tecnológicos Integrales
                                    </span>
                                    <span class="text-[11px] text-[#cbcfd4] block leading-tight mt-0.5">
                                        Limpieza industrial, higienización y mantenimiento
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Enlace rápido a Seguimiento de Carga -->
                <a href="{{ route('home') }}#tracking" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass-location text-xs text-[#f5782b]"></i>
                    <span>Rastrear Carga</span>
                </a>

                <!-- Contacto -->
                <a href="{{ request()->routeIs('tech_services.*') ? '#solicitar-presupuesto' : route('home') . '#contacto' }}" class="text-sm font-semibold text-slate-200 hover:text-[#f5782b] transition-colors py-1">
                    Contacto
                </a>
            </nav>

            <!-- Actions / CTA Header -->
            <div class="hidden sm:flex items-center gap-3">
                @if(request()->routeIs('tech_services.*'))
                    <a href="#solicitar-presupuesto" class="btn-corporate-primary text-xs !py-2.5 !px-5">
                        <i class="fa-solid fa-calculator text-xs"></i>
                        <span>Solicitar Presupuesto</span>
                    </a>
                @else
                    <a href="{{ route('home') }}#contacto" class="btn-corporate-secondary text-xs !py-2.5 !px-4">
                        <i class="fa-solid fa-file-pen text-xs"></i>
                        <span>Cotizar Logística</span>
                    </a>
                    <a href="{{ route('tech_services.index') }}" class="btn-corporate-primary text-xs !py-2.5 !px-4">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        <span>Servicios Tecnológicos</span>
                    </a>
                @endif
            </div>

            <!-- Mobile Hamburger Button -->
            <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-[#0a254e] border border-[#cbcfd4]/20 focus:outline-none focus:ring-2 focus:ring-[#f5782b]" aria-expanded="false" aria-label="Abrir menú de navegación">
                <i class="fa-solid fa-bars text-xl" id="menu-icon-open"></i>
                <i class="fa-solid fa-xmark text-xl hidden" id="menu-icon-close"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div class="md:hidden hidden bg-[#051f42]/98 border-t border-[#cbcfd4]/15 px-4 pt-3 pb-6 shadow-2xl space-y-2.5" id="mobile-menu">
        <a href="{{ route('home') }}" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">
            <i class="fa-solid fa-house mr-2 text-xs text-[#f5782b]"></i> Inicio
        </a>
        <a href="{{ route('home') }}#nosotros" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">
            <i class="fa-solid fa-building mr-2 text-xs text-[#f5782b]"></i> Nosotros
        </a>

        <!-- Áreas de Negocio Subgroup en Móvil -->
        <div class="pt-2 pb-1 border-y border-[#cbcfd4]/10 space-y-1">
            <span class="text-[11px] font-bold text-[#cbcfd4] uppercase tracking-wider block px-3 mb-1">
                Áreas de Negocio
            </span>
            <a href="{{ route('home') }}#logistica" class="mobile-nav-link flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-[#0a254e] hover:text-white">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-ship text-[#f5782b] text-xs"></i>
                    Logística & Trazabilidad
                </span>
                <i class="fa-solid fa-angle-right text-xs text-[#cbcfd4]"></i>
            </a>
            <a href="{{ route('tech_services.index') }}" class="mobile-nav-link flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-[#0a254e] hover:text-white {{ request()->routeIs('tech_services.*') ? 'bg-[#0a254e] text-[#f5782b]' : '' }}">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-[#f5782b] text-xs"></i>
                    Servicios Tecnológicos Integrales
                </span>
                <i class="fa-solid fa-angle-right text-xs text-[#cbcfd4]"></i>
            </a>
        </div>

        <a href="{{ route('home') }}#tracking" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-base font-medium text-[#f5782b] bg-[#f5782b]/10 border border-[#f5782b]/20">
            <i class="fa-solid fa-magnifying-glass-location mr-2"></i> Rastrear Carga en Vivo
        </a>

        <a href="{{ request()->routeIs('tech_services.*') ? '#solicitar-presupuesto' : route('home') . '#contacto' }}" class="mobile-nav-link block px-3 py-2 rounded-lg text-base font-medium text-slate-100 hover:bg-[#0a254e] hover:text-[#f5782b]">
            <i class="fa-solid fa-headset mr-2 text-xs text-[#f5782b]"></i> Contacto Comercial
        </a>

        <div class="pt-3 border-t border-[#cbcfd4]/15 flex flex-col gap-2.5">
            @if(request()->routeIs('tech_services.*'))
                <a href="#solicitar-presupuesto" class="btn-corporate-primary w-full text-center !py-3">
                    Solicitar Presupuesto Tecnológico
                </a>
            @else
                <a href="{{ route('tech_services.index') }}" class="btn-corporate-primary w-full text-center !py-3">
                    Explorar Servicios Tecnológicos
                </a>
                <a href="{{ route('home') }}#contacto" class="btn-corporate-secondary w-full text-center !py-2.5 text-xs">
                    Cotizar Envío Logístico
                </a>
            @endif
        </div>
    </div>
</header>