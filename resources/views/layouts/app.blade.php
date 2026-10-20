<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Torre de Control' }} — SCGI BLANKISOL</title>
  
  <!-- Favicon Oficial Corporativo Adaptativo (Claro / Oscuro) -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-light/favicon.ico') }}" media="(prefers-color-scheme: light)">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-light/favicon-32x32.png') }}" media="(prefers-color-scheme: light)">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-light/favicon-16x16.png') }}" media="(prefers-color-scheme: light)">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-dark/favicon.ico') }}" media="(prefers-color-scheme: dark)">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-dark/favicon-32x32.png') }}" media="(prefers-color-scheme: dark)">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-dark/favicon-16x16.png') }}" media="(prefers-color-scheme: dark)">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-dark/apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">

  <!-- Tailwind CSS CDN con configuración extendida -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
          },
          colors: {
            brand: {
              50: '#f0f7ff',
              100: '#e0effe',
              200: '#bae0fd',
              300: '#7cc7fb',
              400: '#38a9f7',
              500: '#0e8ce9',
              600: '#026fc7',
              700: '#0358a1',
              800: '#074b85',
              900: '#0c3f6e',
              950: '#082849',
            },
            corporate: {
              primary: '#051f42',
              surface: '#0a254e',
              dark: '#020d1c',
              accent: '#f5782b',
              'accent-hover': '#e0651a',
              'accent-light': '#ff934f',
              neutral: '#cbcfd4',
            }
          }
        }
      }
    }
  </script>

  <!-- Iconografía Font Awesome 6 Pro/Free -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Google Fonts: Plus Jakarta Sans + Inter + JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
      background-color: #060911;
      background-image: 
        radial-gradient(at 0% 0%, rgba(14, 140, 233, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0b0f19; }
    ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #334155; }

    /* Glassmorphic utilities */
    .glass-card {
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glass-header {
      background: rgba(10, 15, 29, 0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }
    .glass-sidebar {
      background: rgba(8, 12, 23, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-right: 1px solid rgba(255, 255, 255, 0.07);
    }

    /* Print Clean Rules */
    @media print {
      body { background: #fff !important; color: #000 !important; }
      aside, header, #sidebar-backdrop, .no-print { display: none !important; }
      main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
      .glass-card { background: none !important; border: 1px solid #ccc !important; box-shadow: none !important; }
    }
  </style>
  <!-- Estilos Corporativos BLANKISOL -->
  <link rel="stylesheet" href="{{ asset('css/blankisol.css') }}">
</head>
<body class="h-full flex overflow-hidden selection:bg-brand-500 selection:text-white">

  <!-- Mobile Sidebar Backdrop -->
  <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden hidden transition-opacity"></div>

  <!-- Sidebar Navegación Principal -->
  <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar flex flex-col shrink-0 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
    
    <!-- Brand / Logotipo Oficial -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-[#f5782b]/40 p-1 flex items-center justify-center shadow-lg shadow-brand-500/25 group-hover:scale-105 transition-transform overflow-hidden">
          <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-base tracking-tight text-white group-hover:text-brand-400 transition-colors">BLANKISOL</span>
            <span class="text-[9px] px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-300 border border-brand-500/30 font-bold uppercase font-mono">SCGI</span>
          </div>
          <p class="text-[11px] text-slate-400 font-medium">Torre de Control Logística</p>
        </div>
      </a>
      <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-2">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <!-- Quick Search trigger (Ctrl+K) -->
    <div class="px-4 pt-4">
      <button onclick="openGlobalSearch()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs text-slate-400 hover:text-slate-200 transition-all shadow-inner">
        <div class="flex items-center gap-2.5">
          <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs"></i>
          <span>Buscar PIN / VIN / Orden...</span>
        </div>
        <kbd class="font-mono text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">Ctrl K</kbd>
      </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
      
      {{-- MODULOS OPERATIVOS CORE --}}
      <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Operaciones Portuarias & HUB
      </div>

      {{-- 1. Aduanas --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::ADUANA, \App\Enums\UserRole::DIRECTOR, \App\Enums\UserRole::AUDITOR]))
        <a href="{{ route('customs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('customs.*') ? 'bg-amber-600/90 text-white shadow-lg shadow-amber-900/40 border border-amber-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-anchor w-5 text-center text-sm {{ request()->routeIs('customs.*') ? 'text-white' : 'text-amber-400' }}"></i>
          <span class="flex-1">Inspección Aduanal</span>
          <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300">48/72h</span>
        </a>
      @endif

      {{-- 2. Escáner de Despacho (Puerto Mariel) --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::DESPACHO_PUERTO, \App\Enums\UserRole::DIRECTOR, \App\Enums\UserRole::AUDITOR]))
        <a href="{{ route('dispatch.scanner') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dispatch.scanner') ? 'bg-brand-600 text-white shadow-lg shadow-brand-900/40 border border-brand-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-barcode w-5 text-center text-sm {{ request()->routeIs('dispatch.scanner') ? 'text-white' : 'text-brand-400' }}"></i>
          <span class="flex-1">Escáner Despacho Mariel</span>
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        </a>
      @endif

      {{-- 3. Hojas de Ruta y Logística --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::LOGISTICA_CHOFER, \App\Enums\UserRole::DESPACHO_PUERTO, \App\Enums\UserRole::DIRECTOR]))
        <a href="{{ route('logistics.routes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('logistics.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40 border border-blue-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-truck-fast w-5 text-center text-sm {{ request()->routeIs('logistics.*') ? 'text-white' : 'text-blue-400' }}"></i>
          <span class="flex-1">Hojas de Ruta Regional</span>
        </a>
      @endif

      {{-- 4. Carga Masiva de Manifiestos (22 columnas Excel) --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::DESPACHO_PUERTO, \App\Enums\UserRole::ADUANA, \App\Enums\UserRole::DIRECTOR]))
        <a href="{{ route('manifests.import.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('manifests.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-file-excel w-5 text-center text-sm {{ request()->routeIs('manifests.*') ? 'text-white' : 'text-emerald-400' }}"></i>
          <span class="flex-1">Manifiestos Excel / CSV</span>
        </a>
      @endif

      {{-- SECCION FINANCIERA & AUDITORIA --}}
      <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Gestión Económica & Control
      </div>

      {{-- 5. Cajas Regionales (Cajero & Director) --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::CAJERO_REGIONAL, \App\Enums\UserRole::DIRECTOR, \App\Enums\UserRole::AUDITOR]))
        <a href="{{ route('cashier.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('cashier.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-cash-register w-5 text-center text-sm {{ request()->routeIs('cashier.*') ? 'text-white' : 'text-emerald-400' }}"></i>
          <span class="flex-1">Caja & Cobro Aranceles</span>
          <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 font-bold">$ USD/CUP</span>
        </a>
      @endif

      {{-- 6. Auditoría & Reportes --}}
      @if(auth()->user()->hasRole([\App\Enums\UserRole::AUDITOR, \App\Enums\UserRole::DIRECTOR]))
        <a href="{{ route('audit.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('audit.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/40 border border-purple-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sm {{ request()->routeIs('audit.*') ? 'text-white' : 'text-purple-400' }}"></i>
          <span class="flex-1">Auditoría & Finanzas</span>
        </a>

        {{-- 7. Rentabilidad & Factibilidad Económica (Sprint 5) --}}
        <a href="{{ route('profitability.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('profitability.*') ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-900/40 border border-cyan-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-chart-line w-5 text-center text-sm {{ request()->routeIs('profitability.*') ? 'text-white' : 'text-cyan-400' }}"></i>
          <span class="flex-1">Rentabilidad Contenedores</span>
          <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 font-bold">MC & ROI</span>
        </a>
      @endif


      {{-- SECCION ADMINISTRACION & SISTEMA --}}
      @if(auth()->user()->isAdmin())
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
          Supervisión & Sistema
        </div>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/40 border border-indigo-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-indigo-400' }}"></i>
          <span class="flex-1">Dashboard Ejecutivo</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/40 border border-indigo-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
          <i class="fa-solid fa-users-gear w-5 text-center text-sm {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-indigo-400' }}"></i>
          <span class="flex-1">Usuarios & RBAC (7 Roles)</span>
        </a>
      @endif

      {{-- MODULO COMERCIAL: TIENDA VIRTUAL (BLANKISOL SHOP) --}}
      @if(auth()->user()->isShopAdmin() || auth()->user()->isDirector())
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400">
          Tienda Virtual (SHOP)
        </div>

        <a href="{{ route('shop.admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-emerald-950/40 hover:text-emerald-300' }}">
          <i class="fa-solid fa-store w-5 text-center text-sm text-emerald-400"></i>
          <span class="flex-1">Administración Tienda</span>
        </a>

        <a href="{{ route('shop.admin.products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all text-slate-300 hover:bg-emerald-950/40 hover:text-emerald-300">
          <i class="fa-solid fa-box-open w-5 text-center text-sm text-emerald-400"></i>
          <span class="flex-1">Productos & Stock</span>
        </a>

        <a href="{{ route('shop.admin.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all text-slate-300 hover:bg-emerald-950/40 hover:text-emerald-300">
          <i class="fa-solid fa-receipt w-5 text-center text-sm text-emerald-400"></i>
          <span class="flex-1">Pedidos Comerciales</span>
        </a>

        <a href="{{ route('shop.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-emerald-300 transition-all border border-dashed border-slate-800 hover:border-emerald-500/40">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-cart-shopping w-5 text-center text-sm text-emerald-400"></i>
            <span>Ver Tienda Online</span>
          </div>
          <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-500"></i>
        </a>
      @endif

      <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Consultas & Trazabilidad
      </div>

      <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white border border-slate-700' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-gauge-high w-5 text-center text-sm {{ request()->routeIs('dashboard') ? 'text-white' : 'text-cyan-400' }}"></i>
        <span class="flex-1">Mi Puesto Operativo</span>
      </a>

      <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-[#0a254e] hover:text-[#ff934f] transition-all border border-dashed border-slate-800 hover:border-[#f5782b]/40">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-globe w-5 text-center text-sm text-[#f5782b]"></i>
          <span>Portal Web Corporativo</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-500"></i>
      </a>

      <a href="{{ route('tracking.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-cyan-300 transition-all border border-dashed border-slate-800 hover:border-cyan-500/40">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-satellite-dish w-5 text-center text-sm text-cyan-400"></i>
          <span>Portal Tracking Público</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-500"></i>
      </a>

    </nav>

    <!-- User Profile Box en Sidebar -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-indigo-700 border border-slate-700 flex items-center justify-center font-bold text-white text-sm shadow-inner font-mono">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
          <div class="flex items-center gap-1.5 mt-0.5">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-brand-500/15 text-brand-300 border border-brand-500/25">
              {{ auth()->user()->role?->label() ?? 'Operador' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Contenedor Principal -->
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    
    <!-- Header Superior Inteligente -->
    <header class="h-20 glass-header flex items-center justify-between px-6 sm:px-8 z-20 shrink-0">
      
      <!-- Left side: Mobile Toggle & Breadcrumb / Page Title -->
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
          <i class="fa-solid fa-bars text-base"></i>
        </button>

        <div>
          <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-0.5 font-medium">
            <span>SCGI BLANKISOL</span>
            <span>/</span>
            <span class="text-brand-400 font-semibold">{{ auth()->user()->role?->label() ?? 'Operaciones' }}</span>
          </div>
          <h2 class="text-lg sm:text-xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
            @yield('page_title', 'Torre de Control')
          </h2>
        </div>
      </div>

      <!-- Right side: Server/DB Status, Regional Warehouse & User Logout -->
      <div class="flex items-center gap-3">
        
        <!-- Live System Node Status -->
        <div class="hidden xl:flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span class="text-slate-400">PostgreSQL:</span>
          <strong class="text-slate-200 font-mono">scgi_db</strong>
        </div>

        <!-- Regional Warehouse Indicator -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-800 text-xs text-slate-300">
          <i class="fa-solid fa-warehouse text-brand-400"></i>
          <span>HUB: <strong class="text-white">{{ auth()->user()->regionalWarehouse?->name ?? 'Puerto Mariel' }}</strong></span>
        </div>

        <!-- WhatsApp Gateway Status indicator -->
        <a href="http://localhost:3333/qr" target="_blank" title="Panel WhatsApp QR Local" class="hidden sm:flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/25 text-emerald-400 text-xs font-semibold transition-all">
          <i class="fa-brands fa-whatsapp text-sm"></i>
          <span class="hidden lg:inline">WA Gateway</span>
        </a>

        <!-- Logout Action -->
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" title="Cerrar sesión" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-800 hover:border-rose-500/40 text-xs font-semibold transition-all">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="hidden sm:inline">Salir</span>
          </button>
        </form>

      </div>
    </header>

    <!-- Flash Alert Notifications -->
    <div class="px-6 sm:px-8 pt-6 max-w-7xl mx-auto w-full no-print">
      @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xl shadow-emerald-950/20 animate-fade-in mb-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
              <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <span>{{ session('success') }}</span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200 p-1">✕</button>
        </div>
      @endif

      @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xl shadow-rose-950/20 animate-fade-in mb-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
              <i class="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
            <span>{{ session('error') }}</span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200 p-1">✕</button>
        </div>
      @endif

      @if(session('warning'))
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xl shadow-amber-950/20 animate-fade-in mb-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
              <i class="fa-solid fa-circle-exclamation text-base"></i>
            </div>
            <span>{{ session('warning') }}</span>
          </div>
          <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-200 p-1">✕</button>
        </div>
      @endif
    </div>

    <!-- Main Content Stage -->
    <main class="flex-1 overflow-y-auto px-6 sm:px-8 pb-12 pt-2">
      <div class="max-w-7xl mx-auto space-y-8">
        @yield('content')
      </div>
    </main>

  </div>

  <!-- Global Quick Search Modal (Ctrl+K) -->
  <div id="global-search-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-start justify-center pt-20 px-4">
    <div class="w-full max-w-xl glass-card rounded-3xl p-6 shadow-2xl border border-slate-700/80 space-y-4 animate-fade-in">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <div class="flex items-center gap-2.5 text-brand-400">
          <i class="fa-solid fa-magnifying-glass text-sm"></i>
          <span class="text-xs font-bold uppercase tracking-wider text-white">Búsqueda Rápida en Torre de Control</span>
        </div>
        <kbd class="font-mono text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded">ESC para cerrar</kbd>
      </div>

      <div>
        <input 
          type="text" 
          id="global-search-input" 
          placeholder="Escribe PIN (ej. BK-78492), VIN o Nombre..." 
          class="w-full px-4 py-3.5 rounded-2xl bg-slate-950 border border-brand-500/40 text-white font-mono text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all uppercase"
          onkeydown="if(event.key === 'Enter') executeQuickSearch()"
        />
      </div>

      <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
        <span>Presiona <strong class="text-white">Enter</strong> para consultar expediente</span>
        <button onclick="closeGlobalSearch()" class="text-slate-400 hover:text-white font-semibold">Cerrar</button>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('main-sidebar');
      const backdrop = document.getElementById('sidebar-backdrop');
      sidebar.classList.toggle('-translate-x-full');
      backdrop.classList.toggle('hidden');
    }

    function openGlobalSearch() {
      const modal = document.getElementById('global-search-modal');
      const input = document.getElementById('global-search-input');
      modal.classList.remove('hidden');
      setTimeout(() => input.focus(), 100);
    }

    function closeGlobalSearch() {
      document.getElementById('global-search-modal').classList.add('hidden');
    }

    function executeQuickSearch() {
      const query = document.getElementById('global-search-input').value.trim();
      if (!query) return;
      window.location.href = "{{ url('/tracking') }}/" + encodeURIComponent(query);
    }

    // Atajos de teclado globales
    window.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        openGlobalSearch();
      }
      if (e.key === 'Escape') {
        closeGlobalSearch();
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
