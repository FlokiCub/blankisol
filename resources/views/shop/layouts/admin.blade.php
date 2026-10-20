<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Panel Comercial' }} — BLANKISOL SHOP</title>
  
  <!-- Favicon Oficial Corporativo -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-dark/favicon.ico') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-dark/apple-touch-icon.png') }}">

  <!-- Tailwind CSS CDN con configuración comercial extendida -->
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
            shop: {
              50: '#ecfdf5',
              100: '#d1fae5',
              200: '#a7f3d0',
              300: '#6ee7b7',
              400: '#34d399',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
              800: '#065f46',
              900: '#064e3b',
              950: '#022c22',
            },
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
            }
          }
        }
      }
    }
  </script>

  <!-- Iconografía Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
      background-color: #070b14;
      background-image: 
        radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(14, 140, 233, 0.06) 0px, transparent 50%);
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #090e1a; }
    ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #334155; }

    /* Glassmorphism */
    .glass-card {
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glass-header {
      background: rgba(9, 14, 26, 0.88);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }
    .glass-sidebar {
      background: rgba(7, 11, 22, 0.96);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-right: 1px solid rgba(255, 255, 255, 0.07);
    }
  </style>
</head>
<body class="h-full flex overflow-hidden selection:bg-shop-500 selection:text-white">

  <!-- Mobile Sidebar Backdrop -->
  <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden hidden transition-opacity"></div>

  <!-- Sidebar Navegación Principal del Panel Comercial -->
  <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar flex flex-col shrink-0 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
    
    <!-- Brand / Logotipo Oficial SHOP -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80">
      <a href="{{ route('shop.admin.dashboard') }}" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-emerald-400/40 p-1 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform overflow-hidden">
          <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-base tracking-tight text-white group-hover:text-emerald-400 transition-colors">BLANKISOL</span>
            <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold uppercase font-mono">SHOP</span>
          </div>
          <p class="text-[11px] text-slate-400 font-medium">Panel Comercial B2C / B2B</p>
        </div>
      </a>
      <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-2">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <!-- Currency / Store Status Pill -->
    <div class="px-5 pt-4">
      <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 text-xs text-slate-400">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span class="font-semibold text-slate-200">Tienda Online: USD</span>
        </div>
        <span class="font-mono text-[10px] bg-emerald-500/15 text-emerald-300 px-2 py-0.5 rounded font-bold border border-emerald-500/30">ACTIVA</span>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
      
      <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Comercio & Gestión
      </div>

      {{-- 1. Dashboard Ejecutivo Comercial --}}
      <a href="{{ route('shop.admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ request()->routeIs('shop.admin.dashboard') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Dashboard Comercial</span>
      </a>

      {{-- 2. Gestión de Productos --}}
      <a href="{{ route('shop.admin.products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.products.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-box-open w-5 text-center text-sm {{ request()->routeIs('shop.admin.products.*') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Productos & Catálogo</span>
      </a>

      {{-- 3. Categorías Comerciales --}}
      <a href="{{ route('shop.admin.categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.categories.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-tags w-5 text-center text-sm {{ request()->routeIs('shop.admin.categories.*') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Categorías</span>
      </a>

      {{-- 4. Pedidos & Facturación --}}
      <a href="{{ route('shop.admin.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.orders.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-receipt w-5 text-center text-sm {{ request()->routeIs('shop.admin.orders.*') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Pedidos & Ventas</span>
      </a>

      <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Marketing & Logística
      </div>

      {{-- 5. Promociones y Cupones --}}
      <a href="{{ route('shop.admin.coupons.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.coupons.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-ticket w-5 text-center text-sm {{ request()->routeIs('shop.admin.coupons.*') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Cupones & Ofertas</span>
      </a>

      {{-- 6. Tarifas y Zonas de Entrega --}}
      <a href="{{ route('shop.admin.shipping.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('shop.admin.shipping.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/40 border border-emerald-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
        <i class="fa-solid fa-truck-ramp-box w-5 text-center text-sm {{ request()->routeIs('shop.admin.shipping.*') ? 'text-white' : 'text-emerald-400' }}"></i>
        <span class="flex-1">Zonas & Tarifas Entrega</span>
      </a>

      <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
        Vistas Externas
      </div>

      {{-- Enlace a Storefront Público --}}
      <a href="{{ route('shop.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-emerald-300 transition-all border border-dashed border-slate-800 hover:border-emerald-500/40">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-globe w-5 text-center text-sm text-emerald-400"></i>
          <span>Ver Tienda Pública</span>
        </div>
        <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-500"></i>
      </a>

      {{-- Enlace de retorno a SCGI si es Director --}}
      @if(auth()->user()->isDirector() || auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-brand-300 transition-all border border-dashed border-slate-800 hover:border-brand-500/40">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-satellite-dish w-5 text-center text-sm text-brand-400"></i>
            <span>Volver a SCGI Logístico</span>
          </div>
          <i class="fa-solid fa-arrow-right text-[11px] text-slate-500"></i>
        </a>
      @endif

    </nav>

    <!-- User Profile Box -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-800 border border-slate-700 flex items-center justify-center font-bold text-white text-sm shadow-inner font-mono">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
          <div class="flex items-center gap-1.5 mt-0.5">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/25">
              {{ auth()->user()->role?->label() ?? 'Administrador Tienda' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Contenedor Principal -->
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    
    <!-- Header Superior Comercial -->
    <header class="h-20 glass-header flex items-center justify-between px-6 sm:px-8 z-20 shrink-0">
      
      <!-- Left side: Mobile Toggle & Page Title -->
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
          <i class="fa-solid fa-bars text-base"></i>
        </button>

        <div>
          <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-0.5 font-medium">
            <span>BLANKISOL SHOP</span>
            <span>/</span>
            <span class="text-emerald-400 font-semibold">Panel de Control Comercial</span>
          </div>
          <h2 class="text-lg sm:text-xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
            @yield('page_title', 'Dashboard Comercial')
          </h2>
        </div>
      </div>

      <!-- Right side: Actions, Currency and Logout -->
      <div class="flex items-center gap-3">
        
        <!-- Moneda Oficial USD -->
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
          <i class="fa-solid fa-dollar-sign text-emerald-400"></i>
          <span class="text-slate-400">Moneda Base:</span>
          <strong class="text-emerald-300 font-mono font-bold">USD</strong>
        </div>

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
    <div class="px-6 sm:px-8 pt-6 max-w-7xl mx-auto w-full">
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

      @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm font-medium shadow-xl shadow-rose-950/20 mb-4">
          <div class="flex items-center gap-2 mb-2 font-bold text-rose-400">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>Por favor corrige los siguientes errores:</span>
          </div>
          <ul class="list-disc list-inside space-y-1 text-slate-300 text-xs">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
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

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('main-sidebar');
      const backdrop = document.getElementById('sidebar-backdrop');
      sidebar.classList.toggle('-translate-x-full');
      backdrop.classList.toggle('hidden');
    }
  </script>

  @stack('scripts')
</body>
</html>
