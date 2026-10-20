@extends('shop.layouts.store')

@section('title', $category->name . ' — BLANKISOL SHOP (USD)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Category Hero Card (Estilo Tuambia) -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl border border-emerald-200 shadow-2xs">
          <i class="{{ $category->icon ?: 'fa-solid fa-folder' }}"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('shop.index') }}" class="hover:text-emerald-700 transition">Tienda</a>
            <span>/</span>
            <a href="{{ route('shop.products.index') }}" class="hover:text-emerald-700 transition">Catálogo</a>
            <span>/</span>
            <span class="text-emerald-700 font-bold">{{ $category->name }}</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            {{ $category->name }}
          </h1>
          @if($category->description)
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
              {{ $category->description }}
            </p>
          @endif
        </div>
      </div>

      <div class="text-xs font-mono text-slate-700 bg-slate-50 px-3.5 py-2 rounded-full border border-slate-200">
        {{ $products->total() }} productos disponibles
      </div>
    </div>
  </div>

  <!-- Products Grid (Tuambia Style) -->
  @if($products->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
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
                {{ $category->name }}
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
  @else
    <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 space-y-4 shadow-2xs">
      <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <h3 class="text-base font-bold text-slate-800">Aún no hay productos disponibles en esta categoría</h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">Estamos ingresando nuevo equipamiento con entrega para Cuba continuamente.</p>
      <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">
        <span>Explorar todo el catálogo</span>
      </a>
    </div>
  @endif

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
