<!DOCTYPE html>
@php
  $globalCartSessionId = request()->header('X-Cart-Session')
      ?: (request()->input('cart_session') ?: session()->getId());
  
  $globalCart = \App\Models\Shop\ShopCart::with('items')
      ->where(function ($q) use ($globalCartSessionId) {
          $q->where('session_id', $globalCartSessionId);
          if (auth()->check()) {
              $q->orWhere('user_id', auth()->id());
          }
      })
      ->latest('updated_at')
      ->first();

  $globalCartCount = $globalCart ? $globalCart->total_items : 0;
  $globalCartSubtotal = $globalCart ? $globalCart->subtotal : 0.0;
@endphp
<html lang="es" class="h-full bg-[#F8F9FA] text-slate-800 antialiased shop-tuambia">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Tienda Virtual Oficial' }} — BLANKISOL SHOP (USD)</title>
  
  <meta name="description" content="{{ $metaDescription ?? 'Tienda virtual oficial de BLANKISOL en Cuba. Motos eléctricas, paneles solares, baterías de litio y equipamiento industrial con entregas garantizadas en USD.' }}">

  <!-- Favicon Corporativo Oficial para Fondos Claros -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-light/favicon.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-light/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-light/favicon-16x16.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-light/apple-touch-icon.png') }}">

  <!-- Tailwind CSS CDN -->
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
            },
            tuambia: {
              bg: '#F8F9FA',
              card: '#FFFFFF',
              border: '#E5E7EB',
              green: '#059669',
              greenLight: '#ecfdf5',
              amber: '#f59e0b',
              amberLight: '#fffbeb',
              subtle: '#6B7280',
            }
          }
        }
      }
    }
  </script>

  <!-- Iconografía Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Google Fonts: Plus Jakarta Sans + Inter + JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Estilos Estilo Tuambia Oficial -->
  <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
  
  <style>
    body {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
      background-color: #F8F9FA;
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
  </style>
  
  @stack('styles')
</head>
<body class="min-h-full flex flex-col selection:bg-emerald-500 selection:text-white">

  <!-- 1. Barra de Zona de Entrega Cuba (Estilo Tuambia) -->
  <div class="tuambia-zone-bar flex items-center justify-between px-4 sm:px-6 lg:px-8 border-b border-amber-200 bg-amber-50 text-amber-900 text-xs py-2 shadow-xs">
    <div class="max-w-7xl mx-auto w-full flex flex-wrap items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-200 text-amber-800 text-[11px]">
          <i class="fa-solid fa-location-dot"></i>
        </span>
        <span>
          Lugar de entrega: <strong id="top-zone-name" class="font-bold underline decoration-amber-400">Playa, La Habana</strong>
          <span id="top-zone-badge" class="ml-1.5 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] inline-flex items-center gap-1 border border-emerald-200">
            <i class="fa-solid fa-circle-check text-[9px]"></i> <span id="top-zone-badge-text">Entrega Disponible</span>
          </span>
        </span>
        <button type="button" onclick="confirmZone()" class="ml-2 px-3 py-1 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition shadow-xs">
          Confirmar
        </button>
        <button type="button" onclick="openZoneModal()" class="text-[11px] font-semibold underline underline-offset-2 text-amber-800 hover:text-amber-950 transition ml-1">
          Cambiar Municipio / Provincia
        </button>
      </div>

      <div class="flex items-center gap-4 text-amber-800 text-[11px] font-medium hidden md:flex">
        <span class="flex items-center gap-1.5">
          <i class="fa-solid fa-bolt text-emerald-600"></i>
          <span>Liquidación en <strong>USD ($)</strong></span>
        </span>
        <span>·</span>
        <a href="{{ route('tracking.index') }}" class="hover:underline flex items-center gap-1">
          <i class="fa-solid fa-satellite-dish text-sky-600"></i>
          <span>Rastrear Envío</span>
        </a>
        <span>·</span>
        <a href="{{ route('home') }}" class="hover:underline flex items-center gap-1">
          <i class="fa-solid fa-building text-slate-700"></i>
          <span>Portal Corporativo</span>
        </a>
        @auth
          @if(auth()->user()->isShopAdmin() || auth()->user()->isDirector())
            <span>·</span>
            <a href="{{ route('shop.admin.dashboard') }}" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
              <i class="fa-solid fa-gauge-high"></i>
              <span>Panel Admin SHOP</span>
            </a>
          @endif
        @endauth
      </div>
    </div>
  </div>

  <!-- 2. Header Comercial Principal (Blanco Elevado con Barra de Búsqueda Tuambia) -->
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-20 flex items-center justify-between gap-4">
        
        <!-- Brand Logo BLANKISOL SHOP (Variante Oficial para Fondos Claros) -->
        <a href="{{ route('shop.index') }}" class="flex items-center gap-3 shrink-0 group">
          <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200/90 p-1 flex items-center justify-center shadow-xs group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-300 overflow-hidden">
            <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
          </div>
          <div>
            <div class="flex items-center gap-1.5">
              <span class="font-extrabold text-xl tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">BLANKISOL</span>
              <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-black uppercase font-mono">SHOP</span>
            </div>
            <p class="text-[10px] text-slate-500 font-semibold tracking-wide">ENTREGAS A CUBA · USD</p>
          </div>
        </a>

        <!-- Location Chip (Selector de Provincia Rápido) -->
        <button type="button" onclick="openZoneModal()" class="hidden xl:inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200/80 transition shadow-2xs">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <i class="fa-solid fa-location-dot text-emerald-600 text-sm"></i>
          <span id="header-zone-pill">La Habana</span>
          <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
        </button>

        <!-- Barra de Búsqueda en Píldora (Estilo Tuambia) -->
        <div class="flex-1 max-w-xl hidden md:block">
          <form method="GET" action="{{ route('shop.products.index') }}" class="tuambia-search-bar flex items-center px-2 py-1.5">
            <span class="pl-2 pr-1 text-slate-400">
              <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input 
              type="text" 
              name="search" 
              id="header_search_input"
              value="{{ request('search') }}"
              placeholder="Buscar motos, paneles solares, baterías, generadores..." 
              class="w-full bg-transparent border-none text-slate-800 text-xs placeholder-slate-400 focus:outline-none px-2"
            >
            <button type="submit" class="px-4 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 shrink-0">
              <span>Buscar</span>
            </button>
          </form>
        </div>

        <!-- Acciones: Favoritos, Ayuda y Carrito -->
        <div class="flex items-center gap-3 sm:gap-4 shrink-0">
          
          <!-- Botón de WhatsApp / Atención -->
          <a href="https://wa.me/5352941122" target="_blank" rel="noopener noreferrer" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold transition" title="Atención comercial WhatsApp">
            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
            <span>Ayuda</span>
          </a>

          <!-- Botón Carrito en Píldora (Estilo Tuambia) -->
          <a href="{{ route('shop.cart.index') }}" class="relative inline-flex items-center gap-2.5 px-3.5 py-2 rounded-full bg-slate-900 hover:bg-emerald-700 text-white transition-all group shadow-sm">
            <div class="relative">
              <i class="fa-solid fa-cart-shopping text-white text-sm group-hover:scale-110 transition-transform"></i>
              <span id="cart-counter-badge" class="absolute -top-2 -right-2.5 w-4 h-4 rounded-full bg-emerald-400 text-slate-950 font-black text-[9px] flex items-center justify-center font-mono shadow">
                {{ $globalCartCount }}
              </span>
            </div>
            <div class="hidden sm:block text-left text-[11px] leading-tight pr-1">
              <span class="text-slate-300 block text-[9px] uppercase font-bold tracking-wider">Carrito</span>
              <span id="cart-total-badge" class="font-mono font-bold text-white group-hover:text-emerald-200 transition-colors">${{ number_format($globalCartSubtotal, 2) }} USD</span>
            </div>
          </a>

          <!-- Acceso de Usuario -->
          @auth
            <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
              @if(auth()->user()->isShopAdmin() || auth()->user()->isDirector())
                <a href="{{ route('shop.admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-xs font-bold text-emerald-800 transition" title="Ir al Panel de Administración Comercial">
                  <i class="fa-solid fa-gauge-high text-emerald-600"></i>
                  <span class="hidden md:inline">Panel Admin</span>
                </a>
              @endif
              <span class="hidden xl:inline text-xs font-semibold text-slate-700 truncate max-w-[120px]" title="{{ auth()->user()->name }}">
                {{ auth()->user()->name }}
              </span>
              <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                @csrf
                <button type="submit" title="Cerrar sesión de la tienda" class="p-2 rounded-full bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 transition-colors text-xs">
                  <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
              </form>
            </div>
          @else
            <a href="{{ route('shop.login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 text-xs font-bold text-slate-800 hover:text-emerald-700 transition-all shadow-2xs">
              <i class="fa-regular fa-user text-emerald-600"></i>
              <span>Iniciar Sesión</span>
            </a>
          @endauth

          <!-- Botón de Búsqueda Móvil -->
          <button onclick="toggleMobileSearch()" class="md:hidden p-2 rounded-full bg-slate-100 text-slate-700">
            <i class="fa-solid fa-magnifying-glass text-sm"></i>
          </button>

        </div>

      </div>

      <!-- Barra de Búsqueda Móvil (Colapsable) -->
      <div id="mobile-search-bar" class="hidden md:hidden pb-4 pt-1">
        <form method="GET" action="{{ route('shop.products.index') }}" class="tuambia-search-bar flex items-center px-3 py-2 bg-slate-50">
          <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}"
            placeholder="Buscar productos en Cuba..." 
            class="w-full bg-transparent border-none text-slate-800 text-xs placeholder-slate-500 focus:outline-none"
          >
          <button type="submit" class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-semibold">
            Buscar
          </button>
        </form>
      </div>

    </div>

    <!-- 3. Subnavegación Secundaria en Píldoras (Chips estilo Tuambia) -->
    <nav aria-label="Navegación de categorías y accesos rápidos" class="border-t border-slate-200/80 bg-slate-50/90 py-2">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 text-xs">
          
          <a href="{{ route('shop.products.index') }}" class="tuambia-subnav-pill {{ request()->routeIs('shop.products.index') && !request()->has('inventory_type') ? 'active' : '' }}">
            <i class="fa-solid fa-grip text-emerald-600"></i>
            <span>Todo el Catálogo</span>
          </a>

          <a href="{{ route('shop.products.index', ['sort' => 'featured']) }}" class="tuambia-subnav-pill">
            <i class="fa-solid fa-bolt text-amber-500"></i>
            <span>Ofertas y Destacados</span>
          </a>

          @if(isset($shopNavCategories) && $shopNavCategories->isNotEmpty())
            @foreach($shopNavCategories as $navCat)
              <a href="{{ route('shop.categories.show', $navCat->slug) }}" class="tuambia-subnav-pill {{ request()->is('shop/categories/'.$navCat->slug) ? 'active' : '' }}">
                <i class="{{ $navCat->icon ?: 'fa-solid fa-tag' }} text-slate-500"></i>
                <span>{{ $navCat->name }}</span>
              </a>
            @endforeach
          @endif

          <a href="{{ route('shop.products.index', ['inventory_type' => 'on_demand']) }}" class="tuambia-subnav-pill {{ request('inventory_type') === 'on_demand' ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-packing text-blue-500"></i>
            <span>Bajo Pedido (15-20 días)</span>
          </a>

          <a href="{{ route('shop.cart.index') }}" class="tuambia-subnav-pill">
            <i class="fa-solid fa-truck-fast text-emerald-600"></i>
            <span>Tarifas de Entrega</span>
          </a>

          <a href="{{ route('shop.products.index') }}#b2b" class="tuambia-subnav-pill">
            <i class="fa-solid fa-building text-slate-600"></i>
            <span>Empresas B2B</span>
          </a>

        </div>
      </div>
    </nav>
  </header>

  <!-- Notificaciones Flash -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-4">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
          <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 font-bold">✕</button>
      </div>
    @endif

    @if(session('error'))
      <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-4">
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
          <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 p-1 font-bold">✕</button>
      </div>
    @endif
  </div>

  <!-- Contenido Principal -->
  <main class="flex-1">
    @yield('content')
  </main>

  <!-- 4. Footer Comercial Estilo Tuambia (Limpio, con Garantías y Cobertura en Cuba) -->
  <footer class="mt-20 border-t border-slate-200 bg-white pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Grid de 4 Columnas -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-200">
        
        <!-- Col 1: Marca y Garantía de Entrega -->
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200/90 p-1 flex items-center justify-center shadow-xs overflow-hidden">
              <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
            </div>
            <div>
              <span class="font-black text-slate-900 text-base tracking-tight block">BLANKISOL SHOP</span>
              <span class="text-[10px] text-emerald-700 font-bold uppercase font-mono">B2C & B2B Oficial</span>
            </div>
          </div>
          <p class="text-xs text-slate-600 leading-relaxed">
            Plataforma comercial para compras directas con entrega y nacionalización en Cuba. Motos eléctricas, paneles solares, baterías de litio y equipamiento industrial con liquidación transparente en USD.
          </p>
          <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full w-fit border border-emerald-200">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Garantía Oficial de Importación</span>
          </div>
        </div>

        <!-- Col 2: Categorías Rápidas -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Catálogo Principal</h4>
          <ul class="space-y-2 text-xs text-slate-600 font-medium">
            <li><a href="{{ route('shop.products.index') }}" class="hover:text-emerald-600 transition-colors">Movilidad Eléctrica (Motos y Trimóviles)</a></li>
            <li><a href="{{ route('shop.products.index') }}" class="hover:text-emerald-600 transition-colors">Baterías de Litio & Paneles Solares</a></li>
            <li><a href="{{ route('shop.products.index') }}" class="hover:text-emerald-600 transition-colors">Generadores & Maquinaria Pesada</a></li>
            <li><a href="{{ route('shop.products.index', ['inventory_type' => 'on_demand']) }}" class="hover:text-emerald-600 transition-colors">Equipos Bajo Pedido (15-20 días)</a></li>
            <li><a href="{{ route('shop.products.index') }}" class="hover:text-emerald-600 transition-colors">Repuestos & Mantenimiento STI</a></li>
          </ul>
        </div>

        <!-- Col 3: Envíos & Cobertura Cuba -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Cobertura & Logística</h4>
          <ul class="space-y-2 text-xs text-slate-600 font-medium">
            <li><a href="{{ route('shop.cart.index') }}" class="hover:text-emerald-600 transition-colors">Entregas en La Habana (24-48h)</a></li>
            <li><a href="{{ route('shop.cart.index') }}" class="hover:text-emerald-600 transition-colors">Envíos a Provincias Occidente / Oriente</a></li>
            <li><a href="{{ route('tracking.index') }}" class="hover:text-emerald-600 transition-colors">Rastreo de Cargas por PIN / Contenedor</a></li>
            <li><a href="{{ route('home') }}#servicios" class="hover:text-emerald-600 transition-colors">Despacho Aduanal en Puerto Mariel</a></li>
          </ul>
        </div>

        <!-- Col 4: Métodos de Pago y Seguridad en USD -->
        <div class="space-y-4">
          <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pagos 100% Seguros en USD</h4>
          <p class="text-xs text-slate-600 leading-relaxed">
            Transacciones directas en Dólares Estadounidenses (USD) mediante pasarela protegida con cifrado SSL y verificación auditable de orden.
          </p>
          <div class="flex flex-wrap gap-2 text-slate-700 text-xs font-bold font-mono">
            <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200">USD ($)</span>
            <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200"><i class="fa-brands fa-cc-visa text-blue-600"></i> Visa</span>
            <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200"><i class="fa-brands fa-cc-mastercard text-amber-500"></i> Mastercard</span>
            <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200"><i class="fa-solid fa-lock text-emerald-600"></i> SSL 256-bit</span>
          </div>
        </div>

      </div>

      <!-- Barra Inferior de Copyright -->
      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-medium">
        <p>© {{ date('Y') }} BLANKISOL S.A. Módulo Comercial Oficial BLANKISOL SHOP.</p>
        <div class="flex items-center gap-6">
          <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Portal Corporativo</a>
          <a href="{{ route('tracking.index') }}" class="hover:text-emerald-600 transition-colors">Tracking</a>
          <a href="{{ route('shop.products.index') }}" class="hover:text-emerald-600 transition-colors">Términos y Condiciones</a>
        </div>
      </div>

    </div>
  </footer>

  <!-- 5. Modal Selector de Provincia y Municipio de Cuba (Estilo Tuambia) -->
  <div id="zone-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 animate-scale-in max-h-[92vh] flex flex-col">
      
      <!-- Encabezado del Modal -->
      <div class="flex items-start justify-between pb-3 border-b border-slate-100">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">
            <i class="fa-solid fa-location-dot"></i>
            <span>ENTREGA Y COBERTURA EN CUBA</span>
          </div>
          <h3 class="text-lg font-bold text-slate-900">Define tu Lugar de Entrega</h3>
          <p class="text-xs text-slate-500">Selecciona tu <strong>Provincia</strong> y <strong>Municipio</strong> para confirmar la factibilidad y tarifas de entrega.</p>
        </div>
        <button onclick="closeZoneModal()" class="text-slate-400 hover:text-slate-600 p-1 text-base">✕</button>
      </div>

      <!-- Barra de Pasos / Navegación -->
      <div class="flex items-center gap-2 text-xs font-bold">
        <button type="button" id="step-prov-btn" onclick="showProvinceStep()" class="flex-1 py-2 px-3 rounded-xl border border-emerald-500 bg-emerald-50 text-emerald-800 flex items-center justify-center gap-2 transition">
          <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center">1</span>
          <span>Provincia: <strong id="selected-prov-label">La Habana</strong></span>
        </button>

        <i class="fa-solid fa-chevron-right text-slate-300 text-[10px]"></i>

        <button type="button" id="step-mun-btn" onclick="showMunicipalityStep()" class="flex-1 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300 flex items-center justify-center gap-2 transition">
          <span class="w-5 h-5 rounded-full bg-slate-400 text-white text-[10px] flex items-center justify-center" id="step-mun-badge">2</span>
          <span>Municipio: <strong id="selected-mun-label">Playa</strong></span>
        </button>
      </div>

      <!-- Buscador Rápido -->
      <div>
        <input 
          type="text" 
          id="location-search-input" 
          onkeyup="filterLocations()" 
          placeholder="Buscar provincia o municipio (ej: Playa, Cárdenas, Santa Clara, Moa)..." 
          class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
        >
      </div>

      <!-- Contenedor Scrollable de Selección -->
      <div class="flex-1 overflow-y-auto pr-1 min-h-[180px] max-h-[260px]">
        
        <!-- Vista 1: Lista de Provincias -->
        <div id="provinces-view" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <!-- Renderizado dinámicamente por JavaScript -->
        </div>

        <!-- Vista 2: Lista de Municipios -->
        <div id="municipalities-view" class="hidden space-y-2">
          <div class="flex items-center justify-between pb-1">
            <span class="text-xs font-bold text-slate-700">Municipios de <span id="mun-parent-prov" class="text-emerald-700"></span>:</span>
            <button type="button" onclick="showProvinceStep()" class="text-[11px] font-semibold text-emerald-700 hover:underline">
              ← Cambiar Provincia
            </button>
          </div>
          <div id="municipalities-list" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <!-- Renderizado dinámicamente -->
          </div>
        </div>

      </div>

      <!-- Tarjeta de Factibilidad de Entrega -->
      <div id="delivery-status-card" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
        <div class="flex items-center justify-between">
          <span class="font-bold text-slate-700">Factibilidad de Entrega:</span>
          <span id="delivery-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
            ✓ Entrega Disponible
          </span>
        </div>
        <p id="delivery-status-detail" class="text-[11px] text-slate-600">
          Entrega puerta a puerta disponible en <strong id="status-target-location">Playa, La Habana</strong>. Despacho directo desde Mariel.
        </p>
      </div>

      <!-- Acciones Inferiores -->
      <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-400 text-[11px] hidden sm:inline">No limita la visualización del catálogo.</span>
        <div class="flex items-center gap-2 ml-auto">
          <button type="button" onclick="closeZoneModal()" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
            Cancelar
          </button>
          <button type="button" onclick="saveLocationSelection()" class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-xs">
            Confirmar Destino
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- Scripts Globales de la Tienda (Ubicación Provincia + Municipio) -->
  <script>
    // Diccionario Oficial de Provincias y Municipios de Cuba
    window.CUBA_LOCATIONS = {
      "La Habana": ["Playa", "Plaza de la Revolución", "Centro Habana", "Habana Vieja", "Regla", "Habana del Este", "Guanabacoa", "San Miguel del Padrón", "Diez de Octubre", "Cerro", "Marianao", "La Lisa", "Boyeros", "Arroyo Naranjo", "Cotorro"],
      "Artemisa": ["Artemisa", "Mariel", "Guanajay", "Caimito", "Bauta", "San Antonio de los Baños", "Güira de Melena", "Alquízar", "Candelaria", "San Cristóbal", "Bahía Honda"],
      "Mayabeque": ["San José de las Lajas", "Bejucal", "San Nicolás", "Güines", "Melena del Sur", "Batabanó", "Quivicán", "Madruga", "Nueva Paz", "Jaruco", "Santa Cruz del Norte"],
      "Matanzas": ["Matanzas", "Cárdenas", "Colón", "Jovellanos", "Jagüey Grande", "Calimete", "Martí", "Pedro Betancourt", "Perico", "Unión de Reyes", "Los Arabos", "Limonar", "Ciénaga de Zapata"],
      "Pinar del Río": ["Pinar del Río", "Consolación del Sur", "Viñales", "Sandino", "Guane", "Mantua", "Minas de Matahambre", "San Juan y Martínez", "San Luis", "La Palma", "Los Palacios"],
      "Villa Clara": ["Santa Clara", "Sagua la Grande", "Placetas", "Camajuaní", "Remedios", "Caibarién", "Santo Domingo", "Ranchuelo", "Encrucijada", "Manicaragua", "Cifuentes", "Corralillo", "Quemado de Güines"],
      "Cienfuegos": ["Cienfuegos", "Palmira", "Cruces", "Cumanayagua", "Abreus", "Rodas", "Aguada de Pasajeros", "Lajas"],
      "Sancti Spíritus": ["Sancti Spíritus", "Trinidad", "Cabaiguán", "Yaguajay", "Jatibonico", "Fomento", "Taguasco", "La Sierpe"],
      "Ciego de Ávila": ["Ciego de Ávila", "Morón", "Chambas", "Ciro Redondo", "Majagua", "Florencia", "Venezuela", "Baraguá", "Primero de Enero", "Bolivia"],
      "Camagüey": ["Camagüey", "Florida", "Nuevitas", "Guáimaro", "Minas", "Vertientes", "Esmeralda", "Céspedes", "Sibanicú", "Santa Cruz del Sur", "Jimaguayú", "Najasa", "Sierra de Cubitas"],
      "Las Tunas": ["Las Tunas", "Puerto Padre", "Amancio", "Colombia", "Jobabo", "Majibacoa", "Manatí", "Jesús Menéndez"],
      "Holguín": ["Holguín", "Banes", "Gibara", "Mayarí", "Moa", "Rafael Freyre", "Calixto García", "Cueto", "Frank País", "Báguanos", "Antilla", "Urbano Noris", "Sagua de Tánamo", "Cacocum"],
      "Granma": ["Bayamo", "Manzanillo", "Jiguaní", "Campechuela", "Niquero", "Media Luna", "Bartolomé Masó", "Yara", "Guisa", "Buey Arriba", "Pilón", "Cauto Cristo", "Río Cauto"],
      "Santiago de Cuba": ["Santiago de Cuba", "Palma Soriano", "Contramaestre", "San Luis", "Songo - La Maya", "Segundo Frente", "Tercer Frente", "Guamá", "Mella"],
      "Guantánamo": ["Guantánamo", "Baracoa", "El Salvador", "Yateras", "Maisí", "Imías", "San Antonio del Sur", "Manuel Tames", "Caimanera", "Niceto Pérez"],
      "Isla de la Juventud": ["Nueva Gerona", "La Fe", "Isla de la Juventud"]
    };

    // Variables de Estado del Selector
    let currentSelectedProvince = 'La Habana';
    let currentSelectedMunicipality = 'Playa';

    function checkDeliveryAvailability(province, municipality) {
      // Municipios con limitaciones de acceso vial para entrega directa puerta a puerta
      const restrictedAreas = ['Maisí', 'Yateras', 'Ciénaga de Zapata', 'Guamá', 'Imías', 'Manuel Tames'];
      
      if (restrictedAreas.includes(municipality)) {
        return {
          available: false,
          type: 'hub_only',
          badgeText: 'Retiro en Almacén Regional',
          badgeClass: 'bg-amber-100 text-amber-900 border-amber-300',
          detailText: `Entrega a domicilio no disponible para ${municipality}. Puedes retirar tu pedido en el almacén provincial central de ${province} o en el HUB Mariel.`
        };
      }

      return {
        available: true,
        type: 'door_to_door',
        badgeText: '✓ Entrega Disponible',
        badgeClass: 'bg-emerald-100 text-emerald-800 border-emerald-200',
        detailText: `Entrega puerta a puerta disponible para ${municipality}, ${province}. Despacho directo desde Mariel/Habana.`
      };
    }

    function renderProvincesList() {
      const container = document.getElementById('provinces-view');
      if (!container) return;
      container.innerHTML = '';

      Object.keys(window.CUBA_LOCATIONS).forEach(prov => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `location-prov-btn text-left p-3 rounded-2xl border text-xs font-semibold transition flex items-center justify-between group ${
          prov === currentSelectedProvince 
            ? 'border-emerald-500 bg-emerald-50 text-emerald-900' 
            : 'border-slate-200 hover:border-emerald-400 hover:bg-slate-50 text-slate-700'
        }`;
        btn.setAttribute('data-name', prov.toLowerCase());
        btn.innerHTML = `
          <span>${prov}</span>
          <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-emerald-600"></i>
        `;
        btn.onclick = () => selectProvince(prov);
        container.appendChild(btn);
      });
    }

    function renderMunicipalitiesList(prov) {
      const container = document.getElementById('municipalities-list');
      if (!container) return;
      container.innerHTML = '';

      const municipalities = window.CUBA_LOCATIONS[prov] || [];
      municipalities.forEach(mun => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `location-mun-btn text-left p-2.5 rounded-2xl border text-xs font-semibold transition flex items-center justify-between group ${
          mun === currentSelectedMunicipality 
            ? 'border-emerald-500 bg-emerald-50 text-emerald-900' 
            : 'border-slate-200 hover:border-emerald-400 hover:bg-slate-50 text-slate-700'
        }`;
        btn.setAttribute('data-name', mun.toLowerCase());
        btn.innerHTML = `
          <span>${mun}</span>
          <i class="fa-solid fa-check text-[10px] ${mun === currentSelectedMunicipality ? 'text-emerald-600' : 'text-transparent group-hover:text-slate-300'}"></i>
        `;
        btn.onclick = () => selectMunicipality(mun);
        container.appendChild(btn);
      });

      const parentProv = document.getElementById('mun-parent-prov');
      if (parentProv) parentProv.textContent = prov;
    }

    function showProvinceStep() {
      document.getElementById('provinces-view').classList.remove('hidden');
      document.getElementById('municipalities-view').classList.add('hidden');
      document.getElementById('step-prov-btn').className = 'flex-1 py-2 px-3 rounded-xl border border-emerald-500 bg-emerald-50 text-emerald-800 flex items-center justify-center gap-2 transition';
      document.getElementById('step-mun-btn').className = 'flex-1 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300 flex items-center justify-center gap-2 transition';
      document.getElementById('location-search-input').value = '';
      filterLocations();
    }

    function showMunicipalityStep() {
      renderMunicipalitiesList(currentSelectedProvince);
      document.getElementById('provinces-view').classList.add('hidden');
      document.getElementById('municipalities-view').classList.remove('hidden');
      document.getElementById('step-prov-btn').className = 'flex-1 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300 flex items-center justify-center gap-2 transition';
      document.getElementById('step-mun-btn').className = 'flex-1 py-2 px-3 rounded-xl border border-emerald-500 bg-emerald-50 text-emerald-800 flex items-center justify-center gap-2 transition';
      document.getElementById('location-search-input').value = '';
      filterLocations();
    }

    function selectProvince(prov) {
      currentSelectedProvince = prov;
      const munList = window.CUBA_LOCATIONS[prov] || ['Cabecera'];
      if (!munList.includes(currentSelectedMunicipality)) {
        currentSelectedMunicipality = munList[0];
      }
      document.getElementById('selected-prov-label').textContent = prov;
      document.getElementById('selected-mun-label').textContent = currentSelectedMunicipality;
      updateDeliveryPreview();
      showMunicipalityStep();
    }

    function selectMunicipality(mun) {
      currentSelectedMunicipality = mun;
      document.getElementById('selected-mun-label').textContent = mun;
      updateDeliveryPreview();
      renderMunicipalitiesList(currentSelectedProvince);
    }

    function updateDeliveryPreview() {
      const info = checkDeliveryAvailability(currentSelectedProvince, currentSelectedMunicipality);
      const badge = document.getElementById('delivery-status-badge');
      const detail = document.getElementById('delivery-status-detail');
      const target = document.getElementById('status-target-location');

      if (badge) {
        badge.textContent = info.badgeText;
        badge.className = `px-2 py-0.5 rounded-full text-[10px] font-bold border ${info.badgeClass}`;
      }
      if (detail) {
        detail.textContent = info.detailText;
      }
      if (target) {
        target.textContent = `${currentSelectedMunicipality}, ${currentSelectedProvince}`;
      }
    }

    function filterLocations() {
      const term = document.getElementById('location-search-input').value.toLowerCase().trim();
      
      const isProvView = !document.getElementById('provinces-view').classList.contains('hidden');
      if (isProvView) {
        const buttons = document.querySelectorAll('.location-prov-btn');
        buttons.forEach(btn => {
          const name = btn.getAttribute('data-name');
          btn.style.display = name.includes(term) ? 'flex' : 'none';
        });
      } else {
        const buttons = document.querySelectorAll('.location-mun-btn');
        buttons.forEach(btn => {
          const name = btn.getAttribute('data-name');
          btn.style.display = name.includes(term) ? 'flex' : 'none';
        });
      }
    }

    function toggleMobileSearch() {
      const el = document.getElementById('mobile-search-bar');
      el.classList.toggle('hidden');
    }

    function openZoneModal() {
      currentSelectedProvince = localStorage.getItem('blankisol_delivery_province') || 'La Habana';
      currentSelectedMunicipality = localStorage.getItem('blankisol_delivery_municipality') || 'Playa';
      
      document.getElementById('selected-prov-label').textContent = currentSelectedProvince;
      document.getElementById('selected-mun-label').textContent = currentSelectedMunicipality;
      
      renderProvincesList();
      updateDeliveryPreview();
      showProvinceStep();
      document.getElementById('zone-modal-backdrop').classList.remove('hidden');
    }

    function closeZoneModal() {
      document.getElementById('zone-modal-backdrop').classList.add('hidden');
    }

    function saveLocationSelection() {
      localStorage.setItem('blankisol_delivery_province', currentSelectedProvince);
      localStorage.setItem('blankisol_delivery_municipality', currentSelectedMunicipality);

      applyLocationToUI(currentSelectedProvince, currentSelectedMunicipality);
      closeZoneModal();

      // Disparar evento personalizado para componentes dinámicos
      window.dispatchEvent(new CustomEvent('blankisol:location-changed', {
        detail: {
          province: currentSelectedProvince,
          municipality: currentSelectedMunicipality
        }
      }));

      // Si está en Checkout, actualizar provincia en URL
      if (window.location.pathname.includes('/shop/checkout') || window.location.pathname.includes('/shop/cart')) {
        const url = new URL(window.location.href);
        url.searchParams.set('province', currentSelectedProvince);
        window.location.href = url.toString();
      }
    }

    function applyLocationToUI(prov, mun) {
      const topLabel = document.getElementById('top-zone-name');
      const topBadge = document.getElementById('top-zone-badge');
      const topBadgeText = document.getElementById('top-zone-badge-text');

      if (topLabel) {
        topLabel.textContent = `${mun}, ${prov}`;
      }

      const info = checkDeliveryAvailability(prov, mun);
      if (topBadge && topBadgeText) {
        topBadgeText.textContent = info.badgeText;
        topBadge.className = `ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1 border ${info.badgeClass}`;
      }
    }

    function confirmZone() {
      const prov = localStorage.getItem('blankisol_delivery_province') || 'La Habana';
      const mun = localStorage.getItem('blankisol_delivery_municipality') || 'Playa';
      const btn = event.target;
      btn.textContent = '✓ Confirmado';
      btn.classList.remove('bg-amber-500');
      btn.classList.add('bg-emerald-600');
      setTimeout(() => {
        btn.textContent = 'Confirmar';
        btn.classList.remove('bg-emerald-600');
        btn.classList.add('bg-amber-500');
      }, 2000);
    }

    // Actualizador Global del Carrito (Cantidad y Monto en USD)
    window.updateGlobalCartBadge = function(count, subtotal) {
      const badgeCount = document.getElementById('cart-counter-badge');
      const badgeTotal = document.getElementById('cart-total-badge');
      if (badgeCount && count !== undefined && count !== null) {
        badgeCount.textContent = count;
      }
      if (badgeTotal && subtotal !== undefined && subtotal !== null) {
        const formatted = Number(subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        badgeTotal.textContent = '$' + formatted + ' USD';
      }
    };

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
      const savedProv = localStorage.getItem('blankisol_delivery_province') || 'La Habana';
      const savedMun = localStorage.getItem('blankisol_delivery_municipality') || 'Playa';
      
      currentSelectedProvince = savedProv;
      currentSelectedMunicipality = savedMun;

      applyLocationToUI(savedProv, savedMun);
    });
  </script>

  @stack('scripts')
</body>
</html>
