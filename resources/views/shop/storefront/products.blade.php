@extends('shop.layouts.store')

@section('title', 'Catálogo Oficial de Productos — BLANKISOL SHOP (USD)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Header Breadcrumb & Title -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
    <div>
      <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('shop.index') }}" class="hover:text-emerald-700 transition">Tienda</a>
        <span>/</span>
        <span class="text-emerald-700 font-bold">Catálogo General</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
        <i class="fa-solid fa-box-open text-emerald-600"></i>
        <span>Catálogo de Equipamiento en USD</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
        Explora equipamiento disponible para despacho inmediato en Cuba y bajo pedido programado.
      </p>
    </div>

    <!-- Resultados count -->
    <div class="text-xs font-mono text-slate-700 bg-white px-3.5 py-2 rounded-full border border-slate-200 shadow-2xs">
      Mostrando <strong class="text-emerald-700 font-bold">{{ $products->total() }}</strong> productos
    </div>
  </div>

  <!-- Layout: Sidebar Filters Left, Products Grid Right -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
    <!-- Sidebar Filters (Span 1) -->
    <aside class="space-y-6">
      <form method="GET" action="{{ route('shop.products.index') }}" class="bg-white rounded-3xl p-6 space-y-6 border border-slate-200 shadow-2xs">
        
        <!-- Mantener término de búsqueda si existe -->
        @if(request('search'))
          <input type="hidden" name="search" value="{{ request('search') }}">
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
            <div class="min-w-0">
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Filtro Búsqueda:</span>
              <span class="text-slate-800 font-bold truncate block">"{{ request('search') }}"</span>
            </div>
            <a href="{{ route('shop.products.index', request()->except('search')) }}" class="text-slate-400 hover:text-rose-600 p-1" title="Quitar búsqueda">
              <i class="fa-solid fa-xmark"></i>
            </a>
          </div>
        @endif

        <!-- Ordenamiento -->
        <div>
          <label for="sort" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Ordenar Por</label>
          <select name="sort" id="sort" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Más Recientes</option>
            <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Productos Destacados</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
          </select>
        </div>

        <!-- Categorías -->
        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Categoría</label>
          <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
            <label class="flex items-center justify-between text-xs text-slate-700 hover:text-slate-900 cursor-pointer py-1.5 px-2.5 rounded-xl hover:bg-slate-50 transition {{ !request('category') ? 'bg-emerald-50 text-emerald-800 font-bold' : '' }}">
              <div class="flex items-center gap-2">
                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="text-emerald-600 focus:ring-emerald-500">
                <span>Todas</span>
              </div>
            </label>
            @foreach($categories as $cat)
              <label class="flex items-center justify-between text-xs text-slate-700 hover:text-slate-900 cursor-pointer py-1.5 px-2.5 rounded-xl hover:bg-slate-50 transition {{ request('category') == $cat->slug || request('category') == $cat->id ? 'bg-emerald-50 text-emerald-800 font-bold' : '' }}">
                <div class="flex items-center gap-2">
                  <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug || request('category') == $cat->id ? 'checked' : '' }} onchange="this.form.submit()" class="text-emerald-600 focus:ring-emerald-500">
                  <span>{{ $cat->name }}</span>
                </div>
              </label>
            @endforeach
          </div>
        </div>

        <!-- Modalidad de Inventario -->
        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Disponibilidad en Cuba</label>
          <div class="space-y-2 text-xs">
            <label class="flex items-center gap-2.5 text-slate-700 hover:text-slate-900 cursor-pointer">
              <input type="radio" name="inventory_type" value="" {{ !request('inventory_type') ? 'checked' : '' }} onchange="this.form.submit()" class="text-emerald-600 focus:ring-emerald-500">
              <span>Cualquier Modalidad</span>
            </label>
            <label class="flex items-center gap-2.5 text-slate-700 hover:text-slate-900 cursor-pointer">
              <input type="radio" name="inventory_type" value="controlled_stock" {{ request('inventory_type') === 'controlled_stock' ? 'checked' : '' }} onchange="this.form.submit()" class="text-emerald-600 focus:ring-emerald-500">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-semibold text-emerald-800">Stock Cuba (24-48h)</span>
              </span>
            </label>
            <label class="flex items-center gap-2.5 text-slate-700 hover:text-slate-900 cursor-pointer">
              <input type="radio" name="inventory_type" value="on_demand" {{ request('inventory_type') === 'on_demand' ? 'checked' : '' }} onchange="this.form.submit()" class="text-blue-600 focus:ring-blue-500">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span class="font-semibold text-blue-800">Bajo Pedido (15-20 días)</span>
              </span>
            </label>
          </div>
        </div>

        <!-- Rango de Precios en USD -->
        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Rango de Precio (USD)</label>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <span class="text-[10px] text-slate-400 block mb-1">Mínimo $</span>
              <input type="number" step="10" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
              <span class="text-[10px] text-slate-400 block mb-1">Máximo $</span>
              <input type="number" step="10" name="max_price" value="{{ request('max_price') }}" placeholder="50000" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
          </div>
        </div>

        <!-- Botones de Acción de Filtro -->
        <div class="pt-2 flex flex-col gap-2">
          <button type="submit" class="w-full py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
            Aplicar Filtros
          </button>
          <a href="{{ route('shop.products.index') }}" class="w-full py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-center font-semibold text-xs transition">
            Limpiar Filtros
          </a>
        </div>

      </form>
    </aside>

    <!-- Products Grid (Span 3) -->
    <main class="lg:col-span-3 space-y-6">
      
      @if($products->isEmpty())
        <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 space-y-4 shadow-2xs">
          <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>
          <h3 class="text-base font-bold text-slate-800">No encontramos productos con estos criterios</h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Prueba ajustando los filtros de categoría, precio o modalidad de entrega.
          </p>
          <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">
            <span>Ver todo el catálogo</span>
          </a>
        </div>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          @foreach($products as $product)
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
          @endforeach
        </div>

        <!-- Paginación -->
        <div class="pt-6">
          {{ $products->links() }}
        </div>
      @endif

    </main>

  </div>

</div>

<!-- Toast flotante estilo Tuambia -->
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
        if (typeof window.updateGlobalCartBadge === 'function') {
          window.updateGlobalCartBadge(data.cart_count, data.subtotal);
        } else {
          const badge = document.getElementById('cart-counter-badge');
          if (badge) badge.textContent = data.cart_count;
        }
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
