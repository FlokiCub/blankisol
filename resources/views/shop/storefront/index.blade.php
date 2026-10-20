@extends('shop.layouts.store')

@section('title', 'BLANKISOL SHOP — Tienda Oficial en Cuba (USD)')

@section('content')
<div class="space-y-12 py-6">

  <!-- 1. Hero Grid de Banners Comerciales (Estilo Tuambia / Jámazon) -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
      
      <!-- Banner Principal (8 Columnas) -->
      <div class="lg:col-span-8 rounded-3xl overflow-hidden bg-gradient-to-br from-emerald-700 via-teal-800 to-slate-900 p-8 sm:p-12 text-white relative flex flex-col justify-between shadow-md">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="space-y-4 max-w-xl relative z-10">
          <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/20 backdrop-blur-md text-emerald-100 text-xs font-bold border border-white/20">
            <i class="fa-solid fa-truck-fast"></i>
            <span>ENTREGAS EN TODA CUBA · LIQUIDACIÓN USD</span>
          </div>

          <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-[1.1]">
            Motos Eléctricas, Paneles & Baterías de Litio
          </h1>

          <p class="text-emerald-100 text-xs sm:text-sm leading-relaxed">
            Compra directamente con entrega en La Habana y despacho a todas las provincias. Stock físico garantizado, nacionalización en Puerto Mariel y atención B2C y B2B.
          </p>
        </div>

        <div class="pt-8 relative z-10 flex flex-wrap items-center gap-3">
          <a href="{{ route('shop.products.index') }}" class="px-6 py-3 rounded-full bg-white text-emerald-900 font-extrabold text-xs sm:text-sm hover:bg-emerald-50 transition shadow-sm flex items-center gap-2">
            <span>Ver Catálogo Completo</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
          <a href="{{ route('shop.products.index', ['inventory_type' => 'on_demand']) }}" class="px-6 py-3 rounded-full bg-emerald-900/60 hover:bg-emerald-900 text-white font-bold text-xs sm:text-sm border border-emerald-400/30 transition flex items-center gap-2">
            <i class="fa-solid fa-boxes-packing text-emerald-300"></i>
            <span>Equipos Bajo Pedido</span>
          </a>
        </div>
      </div>

      <!-- Banners Laterales Promocionales (4 Columnas) -->
      <div class="lg:col-span-4 flex flex-col gap-4">
        
        <!-- Side Banner 1: Energía Solar -->
        <div class="flex-1 rounded-3xl p-6 bg-gradient-to-br from-amber-500 to-amber-600 text-white relative overflow-hidden flex flex-col justify-between shadow-sm">
          <div class="space-y-2 relative z-10">
            <span class="text-[10px] font-black uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-full inline-block">
              STOCK FÍSICO 24-48H
            </span>
            <h3 class="text-xl font-black leading-tight">Sistemas Solares & Baterías 48V</h3>
            <p class="text-xs text-amber-100">Equipos de alta eficiencia listos para entrega inmediata.</p>
          </div>
          <div class="pt-4 relative z-10">
            <a href="{{ route('shop.products.index', ['category' => 'solar']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-amber-900 font-bold text-xs hover:bg-amber-50 transition">
              <span>Explorar</span>
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
          </div>
        </div>

        <!-- Side Banner 2: B2B Corporativo -->
        <div class="flex-1 rounded-3xl p-6 bg-gradient-to-br from-sky-700 to-slate-800 text-white relative overflow-hidden flex flex-col justify-between shadow-sm">
          <div class="space-y-2 relative z-10">
            <span class="text-[10px] font-black uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-full inline-block">
              VENTAS B2B A EMPRESAS
            </span>
            <h3 class="text-xl font-black leading-tight">Contratos Comerciales & Factura Fiscal</h3>
            <p class="text-xs text-sky-100">Generadores, maquinaria y repuestos con entrega portuaria.</p>
          </div>
          <div class="pt-4 relative z-10">
            <a href="{{ route('shop.products.index') }}#b2b" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-sky-900 font-bold text-xs hover:bg-sky-50 transition">
              <span>Cotizar B2B</span>
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- 2. Barra de Confianza & Valores de Servicio (Estilo Tuambia) -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      
      <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3.5 shadow-2xs">
        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-truck-fast"></i>
        </div>
        <div class="leading-tight">
          <strong class="text-slate-900 text-xs font-bold block">Entrega en Cuba</strong>
          <span class="text-slate-500 text-[11px]">24-48h Habana y Provincias</span>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3.5 shadow-2xs">
        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="leading-tight">
          <strong class="text-slate-900 text-xs font-bold block">Garantía Oficial</strong>
          <span class="text-slate-500 text-[11px]">Soporte y Taller STI en Habana</span>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3.5 shadow-2xs">
        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-lock"></i>
        </div>
        <div class="leading-tight">
          <strong class="text-slate-900 text-xs font-bold block">Pagos en USD</strong>
          <span class="text-slate-500 text-[11px]">Pasarela 100% Protegida</span>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center gap-3.5 shadow-2xs">
        <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-building"></i>
        </div>
        <div class="leading-tight">
          <strong class="text-slate-900 text-xs font-bold block">Atención B2C & B2B</strong>
          <span class="text-slate-500 text-[11px]">Particular y Razón Social</span>
        </div>
      </div>

    </div>
  </section>

  <!-- 3. Vitrina de Categorías en Círculos (Firma Visual de Tuambia) -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
          <i class="fa-solid fa-layer-group text-emerald-600"></i>
          <span>Categorías Populares</span>
        </h2>
        <p class="text-xs text-slate-500">Explora por líneas de productos disponibles para envío</p>
      </div>
      <a href="{{ route('shop.products.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1">
        <span>Ver todas</span>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
      </a>
    </div>

    <div class="flex items-center gap-5 sm:gap-8 overflow-x-auto no-scrollbar py-2">
      @forelse($categories as $category)
        <a href="{{ route('shop.categories.show', $category->slug) }}" class="tuambia-category-circle shrink-0 group">
          <div class="tuambia-category-icon-box">
            <i class="{{ $category->icon ?: 'fa-solid fa-boxes-stacked' }}"></i>
          </div>
          <div>
            <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition block max-w-[90px] truncate">
              {{ $category->name }}
            </span>
            <span class="text-[10px] text-slate-400 font-mono">{{ $category->products_count }} ítems</span>
          </div>
        </a>
      @empty
        <div class="py-4 text-xs text-slate-400">No hay categorías registradas.</div>
      @endforelse
    </div>
  </section>

  <!-- 4. Vitrina de Productos Destacados (Tarjetas de Producto Estilo Tuambia) -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 uppercase">
            ⚡ MÁS SOLICITADOS
          </span>
        </div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
          Productos Destacados con Entrega Inmediata
        </h2>
      </div>
      <a href="{{ route('shop.products.index', ['sort' => 'featured']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1">
        <span>Ver catálogo completo</span>
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
      @forelse($featuredProducts as $product)
        <div class="tuambia-product-card group">
          
          <!-- Image Box with Badges -->
          <div class="tuambia-card-img-wrap">
            <a href="{{ route('shop.products.show', $product->slug) }}" class="w-full h-full flex items-center justify-center p-4">
              <div class="w-20 h-20 rounded-2xl bg-white border border-slate-200 text-emerald-600 flex items-center justify-center text-4xl group-hover:scale-110 transition-transform shadow-2xs">
                <i class="fa-solid fa-box-open"></i>
              </div>
            </a>

            <!-- Availability Badges -->
            <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 items-start">
              @if($product->isOnDemand())
                <span class="tuambia-badge-ondemand">
                  <i class="fa-solid fa-clock text-[9px]"></i>
                  <span>Bajo Pedido</span>
                </span>
              @else
                <span class="tuambia-badge-stock">
                  <i class="fa-solid fa-circle-check text-[9px]"></i>
                  <span>Stock Cuba</span>
                </span>
              @endif

              @if($product->sale_price && $product->sale_price < $product->price)
                <span class="tuambia-badge-discount">
                  -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                </span>
              @endif
            </div>

            <button type="button" class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-white/90 text-slate-400 hover:text-rose-500 hover:bg-white flex items-center justify-center transition shadow-2xs" title="Guardar en favoritos">
              <i class="fa-regular fa-heart text-xs"></i>
            </button>
          </div>

          <!-- Card Content Body -->
          <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
            <div>
              <span class="text-[10px] font-bold text-emerald-700 block uppercase tracking-wider">
                {{ $product->category?->name ?? 'Equipamiento' }}
              </span>
              <a href="{{ route('shop.products.show', $product->slug) }}" class="font-bold text-slate-900 text-xs sm:text-sm hover:text-emerald-700 transition line-clamp-2 mt-0.5">
                {{ $product->name }}
              </a>
              @if($product->short_description)
                <p class="text-[11px] text-slate-500 line-clamp-2 mt-1">
                  {{ $product->short_description }}
                </p>
              @endif
            </div>

            <!-- Price and Direct Cart Button -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
              <div class="flex items-baseline justify-between">
                <div>
                  <span class="text-[9px] text-slate-400 uppercase font-bold block">Precio Oficial</span>
                  @if($product->sale_price && $product->sale_price < $product->price)
                    <div class="flex items-baseline gap-1.5 font-mono">
                      <span class="text-base sm:text-lg font-black text-slate-900">${{ number_format((float)$product->sale_price, 2) }}</span>
                      <span class="text-[11px] text-slate-400 line-through">${{ number_format((float)$product->price, 2) }}</span>
                    </div>
                  @else
                    <span class="text-base sm:text-lg font-black text-slate-900 font-mono">${{ number_format((float)$product->price, 2) }}</span>
                  @endif
                </div>
                <span class="text-[10px] font-bold font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                  USD ($)
                </span>
              </div>

              <!-- Botón Añadir al Carrito (Tuambia Style) -->
              <button 
                type="button" 
                onclick="quickAddToCart({{ $product->id }}, '{{ addslashes($product->name) }}')"
                class="tuambia-btn-add"
              >
                <i class="fa-solid fa-cart-plus text-xs"></i>
                <span>Añadir al Carrito</span>
              </button>
            </div>

          </div>

        </div>
      @empty
        <div class="col-span-full py-12 text-center text-slate-500 text-xs">
          No hay productos destacados activos en este momento.
        </div>
      @endforelse
    </div>
  </section>

  <!-- 5. Vitrina: Equipos Bajo Pedido (Maquinaria Pesada & Generadores) -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase">
            <i class="fa-solid fa-boxes-packing"></i>
            <span>MODALIDAD ON DEMAND</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
            Maquinaria Pesada & Generadores Industriales Bajo Pedido
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Equipamiento de gran escala con despacho marítimo programado a Puerto Mariel (15-20 días).</p>
        </div>
        <a href="{{ route('shop.products.index', ['inventory_type' => 'on_demand']) }}" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition shrink-0 self-start sm:self-auto">
          Ver todos los equipos on-demand
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($onDemandProducts as $item)
          <div class="p-4 rounded-2xl border border-slate-200 hover:border-blue-400 bg-slate-50/50 hover:bg-white transition flex flex-col justify-between space-y-4">
            <div class="space-y-2">
              <span class="text-[10px] font-mono text-blue-600 font-bold block uppercase">{{ $item->sku }}</span>
              <a href="{{ route('shop.products.show', $item->slug) }}" class="font-bold text-slate-900 text-xs sm:text-sm hover:text-blue-600 transition line-clamp-2">
                {{ $item->name }}
              </a>
              <div class="inline-flex items-center gap-1 text-[11px] text-slate-500 font-medium bg-white px-2 py-1 rounded-full border border-slate-200">
                <i class="fa-regular fa-clock text-blue-500 text-xs"></i>
                <span>Entrega: {{ $item->estimated_delivery_days ?: '15-20' }} días</span>
              </div>
            </div>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
              <span class="text-sm font-black text-slate-900 font-mono">${{ number_format((float)$item->price, 2) }} USD</span>
              <a href="{{ route('shop.products.show', $item->slug) }}" class="text-xs font-bold text-blue-600 hover:underline">
                Detalles →
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full py-6 text-center text-slate-400 text-xs">
            No hay equipos bajo pedido disponibles actualmente.
          </div>
        @endforelse
      </div>
    </div>
  </section>

</div>

<!-- Toast flotante estilo Tuambia para confirmación de carrito -->
<div id="tuambia-toast" class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none">
  <div class="w-8 h-8 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-sm">
    <i class="fa-solid fa-check"></i>
  </div>
  <div>
    <span class="text-xs font-bold block" id="toast-title">¡Producto añadido!</span>
    <span class="text-[11px] text-slate-300 block truncate max-w-xs" id="toast-desc">Tu carrito fue actualizado</span>
  </div>
  <a href="{{ route('shop.cart.index') }}" class="ml-2 text-xs font-bold text-emerald-400 underline underline-offset-2">Ver Carrito</a>
</div>

@push('scripts')
<script>
  function quickAddToCart(productId, productName) {
    fetch('{{ route('shop.cart.add') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: 1
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Actualizar contador y monto del header
        if (typeof window.updateGlobalCartBadge === 'function') {
          window.updateGlobalCartBadge(data.cart_count, data.subtotal);
        } else {
          const badge = document.getElementById('cart-counter-badge');
          if (badge) badge.textContent = data.cart_count;
        }
        
        // Mostrar Toast
        showToast('¡Añadido al Carrito!', productName);
      } else {
        alert(data.message || 'No se pudo añadir al carrito.');
      }
    })
    .catch(err => {
      console.error(err);
      window.location.href = '{{ route('shop.cart.index') }}';
    });
  }

  function showToast(title, desc) {
    const toast = document.getElementById('tuambia-toast');
    document.getElementById('toast-title').textContent = title;
    document.getElementById('toast-desc').textContent = desc;

    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
      toast.classList.remove('translate-y-0', 'opacity-100');
      toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3500);
  }
</script>
@endpush
@endsection
