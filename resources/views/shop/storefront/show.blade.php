@extends('shop.layouts.store')

@section('title', $product->name . ' — BLANKISOL SHOP (USD)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

  <!-- Breadcrumbs -->
  <div class="flex items-center gap-2 text-xs text-slate-500">
    <a href="{{ route('shop.index') }}" class="hover:text-emerald-700 transition">Tienda</a>
    <span>/</span>
    <a href="{{ route('shop.products.index') }}" class="hover:text-emerald-700 transition">Catálogo</a>
    @if($product->category)
      <span>/</span>
      <a href="{{ route('shop.categories.show', $product->category->slug) }}" class="hover:text-emerald-700 transition">{{ $product->category->name }}</a>
    @endif
    <span>/</span>
    <span class="text-slate-800 font-bold truncate">{{ $product->name }}</span>
  </div>

  <!-- Product Showcase Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Left Column: Gallery & Technical Specs (Span 7) -->
    <div class="lg:col-span-7 space-y-6">
      
      <!-- Main Visual Showcase (Estilo Tuambia) -->
      <div class="bg-white rounded-3xl p-8 sm:p-12 flex flex-col items-center justify-center min-h-[380px] relative border border-slate-200 shadow-2xs overflow-hidden">
        
        <div class="w-48 h-48 rounded-3xl bg-slate-50 border border-slate-100 text-emerald-600 flex items-center justify-center text-7xl shadow-inner">
          <i class="fa-solid fa-box-open"></i>
        </div>

        <!-- Badges on top -->
        <div class="absolute top-4 left-4 flex flex-col gap-1.5 items-start">
          @if($product->isOnDemand())
            <span class="tuambia-badge-ondemand">
              <i class="fa-solid fa-clock text-[9px]"></i>
              <span>Bajo Pedido (15-20 días)</span>
            </span>
          @else
            @if($product->stock_quantity == 0)
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                Agotado Temporalmente
              </span>
            @else
              <span class="tuambia-badge-stock">
                <i class="fa-solid fa-circle-check text-[9px]"></i>
                <span>En Stock Cuba (Entrega 24-48h)</span>
              </span>
            @endif
          @endif

          @if($product->sale_price && $product->sale_price < $product->price)
            <span class="tuambia-badge-discount">
              OFERTA -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
            </span>
          @endif
        </div>

        <span class="absolute top-4 right-4 text-xs font-mono text-slate-400 uppercase font-bold">
          SKU: {{ $product->sku }}
        </span>
      </div>

      <!-- Technical Specifications Sheet -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fa-solid fa-sliders text-emerald-600 text-sm"></i>
          <span>Ficha Técnica & Especificaciones</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Código SKU:</span>
            <span class="font-mono text-slate-900 font-bold mt-0.5 block">{{ $product->sku }}</span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Modalidad:</span>
            <span class="text-slate-900 font-bold mt-0.5 block">
              {{ $product->isOnDemand() ? 'Bajo Pedido (On Demand)' : 'Existencia Física' }}
            </span>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Tiempo Estimado:</span>
            <span class="text-emerald-700 font-bold mt-0.5 block">
              {{ $product->estimated_delivery_days ?: ($product->isOnDemand() ? '15 a 20 días' : 'Inmediato (24-48h)') }}
            </span>
          </div>

          @if($product->weight_kg)
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Peso Estimado:</span>
              <span class="font-mono text-slate-900 font-bold mt-0.5 block">{{ $product->weight_kg }} kg</span>
            </div>
          @endif

          @if($product->dimensions)
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Dimensiones:</span>
              <span class="font-mono text-slate-900 font-bold mt-0.5 block">{{ $product->dimensions }}</span>
            </div>
          @endif

          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Garantía:</span>
            <span class="text-slate-900 font-bold mt-0.5 block">Oficial BLANKISOL</span>
          </div>
        </div>
      </div>

      <!-- Detailed Description -->
      @if($product->description)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-3">
          <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fa-solid fa-align-left text-emerald-600 text-sm"></i>
            <span>Descripción del Producto</span>
          </h2>
          <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-2 whitespace-pre-line">
            {{ $product->description }}
          </div>
        </div>
      @endif

    </div>

    <!-- Right Column: Pricing, Inventory & Purchase Box (Span 5) -->
    <div class="lg:col-span-5 space-y-6">
      
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6 sticky top-28">
        
        <!-- Category & Title -->
        <div>
          <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider block">
            {{ $product->category?->name ?? 'Catálogo Oficial' }}
          </span>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
            {{ $product->name }}
          </h1>
          @if($product->short_description)
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
              {{ $product->short_description }}
            </p>
          @endif
        </div>

        <!-- Official Pricing in USD (Estilo Tuambia) -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
          <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Precio Oficial</span>
            @if($product->sale_price && $product->sale_price < $product->price)
              <div class="flex items-baseline gap-2 font-mono mt-0.5">
                <span class="text-3xl font-black text-slate-900">${{ number_format((float)$product->sale_price, 2) }}</span>
                <span class="text-xs text-slate-400 line-through">${{ number_format((float)$product->price, 2) }}</span>
                <span class="text-xs text-emerald-700 font-sans font-bold">USD</span>
              </div>
            @else
              <div class="flex items-baseline gap-1 font-mono mt-0.5">
                <span class="text-3xl font-black text-slate-900">${{ number_format((float)$product->price, 2) }}</span>
                <span class="text-xs text-emerald-700 font-sans font-bold">USD</span>
              </div>
            @endif
          </div>

          <div class="text-right">
            <span class="text-[10px] text-slate-500 block font-mono">Facturación en USD</span>
            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-bold">
              <i class="fa-solid fa-lock text-[8px]"></i> Pago Seguro
            </span>
          </div>
        </div>

        <!-- Zone Delivery Estimator Pill (Tuambia Style) -->
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs">
          <i class="fa-solid fa-location-dot text-amber-600 text-base mt-0.5 shrink-0"></i>
          <div class="leading-tight flex-1">
            <div class="flex items-center justify-between flex-wrap gap-1">
              <span>Lugar de entrega: <strong id="product-dest-zone" class="font-bold underline decoration-amber-400">Playa, La Habana</strong></span>
              <span id="product-dest-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-100 text-emerald-800 border-emerald-200">
                ✓ Entrega Disponible
              </span>
            </div>
            <span id="product-dest-eta" class="block text-[11px] font-semibold text-amber-900 mt-1">
              {{ $product->isOnDemand() ? '15 a 20 días hábiles (Despacho Marítimo Mariel)' : '24 a 48 horas (Entrega inmediata puerta a puerta)' }}
            </span>
            <span id="product-dest-detail" class="block text-[10px] text-amber-800/80 mt-0.5">
              Entrega directa garantizada para este destino en Cuba.
            </span>
            <button type="button" onclick="openZoneModal()" class="text-[10px] text-amber-900 font-bold underline mt-1.5 inline-block">
              Cambiar municipio o provincia de entrega
            </button>
          </div>
        </div>

        <!-- Availability Details -->
        <div class="space-y-3 text-xs">
          @if($product->isOnDemand())
            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 flex items-start gap-3">
              <i class="fa-solid fa-clock text-base mt-0.5 text-blue-600"></i>
              <div>
                <strong class="block text-slate-900">Modalidad Bajo Pedido (On Demand)</strong>
                <span class="text-[11px] text-slate-600">
                  Este artículo se fabrica o importa bajo solicitud. Tiempo estimado: <strong>{{ $product->estimated_delivery_days ?: '15 a 20 días' }}</strong>.
                </span>
              </div>
            </div>
          @else
            @if($product->stock_quantity == 0)
              <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-base mt-0.5 text-rose-600"></i>
                <div>
                  <strong class="block text-slate-900">Stock Físico Agotado</strong>
                  <span class="text-[11px] text-slate-600">
                    Actualmente no hay unidades disponibles en almacén.
                  </span>
                </div>
              </div>
            @else
              <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3">
                <i class="fa-solid fa-boxes-packing text-base mt-0.5 text-emerald-600"></i>
                <div>
                  <strong class="block text-slate-900">Disponibilidad en Cuba</strong>
                  <span class="text-[11px] text-slate-600">
                    Quedan <strong class="text-emerald-700 font-mono">{{ $product->stock_quantity }}</strong> unidades en almacén Mariel/Habana.
                  </span>
                </div>
              </div>
            @endif
          @endif
        </div>

        <!-- Quantity & Add to Cart -->
        <div class="space-y-3 pt-2">
          <div class="flex items-center gap-3">
            <div class="flex items-center rounded-2xl bg-slate-100 border border-slate-200 px-3 py-2">
              <button type="button" onclick="decrementQty()" class="text-slate-600 hover:text-slate-950 px-2 py-0.5 text-base font-bold">-</button>
              <input type="number" id="purchase_quantity" value="1" min="1" {{ !$product->isOnDemand() ? 'max=' . $product->stock_quantity : '' }} class="w-12 bg-transparent text-center font-mono text-slate-900 text-xs font-bold focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
              <button type="button" onclick="incrementQty()" class="text-slate-600 hover:text-slate-950 px-2 py-0.5 text-base font-bold">+</button>
            </div>

            <button type="button" onclick="addToCartDetail()" class="flex-1 py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center justify-center gap-2">
              <i class="fa-solid fa-cart-plus"></i>
              <span>Añadir al Carrito</span>
            </button>
          </div>

          <div id="cart_added_alert" class="hidden p-3 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-semibold text-center">
            <i class="fa-solid fa-circle-check mr-1"></i> ¡Producto añadido al carrito con éxito!
          </div>
        </div>

        <!-- Reassurance Points -->
        <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs text-slate-600 font-medium">
          <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-truck-fast text-emerald-600 w-4 text-center"></i>
            <span>Despacho y entrega a toda Cuba</span>
          </div>
          <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-passport text-sky-600 w-4 text-center"></i>
            <span>Nacionalización y aranceles portuarios cubiertos</span>
          </div>
          <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-file-invoice text-purple-600 w-4 text-center"></i>
            <span>Factura comercial y contrato para empresas B2B</span>
          </div>
        </div>

      </div>

    </div>

  <!-- Related Products (Tuambia Style) -->
  @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
    <div class="pt-8 border-t border-slate-200 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-emerald-600"></i>
            <span>Productos Relacionados</span>
          </h2>
          <p class="text-xs text-slate-500">Equipos y accesorios complementarios con entrega en Cuba</p>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($relatedProducts as $rel)
          <div class="tuambia-product-card group">
            <div class="tuambia-card-img-wrap">
              <a href="{{ route('shop.products.show', $rel->slug) }}" class="w-full h-full flex items-center justify-center p-4">
                <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 text-emerald-600 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-2xs">
                  <i class="fa-solid fa-box-open"></i>
                </div>
              </a>
              <div class="absolute top-2 left-2">
                @if($rel->isOnDemand())
                  <span class="tuambia-badge-ondemand">Bajo Pedido</span>
                @else
                  <span class="tuambia-badge-stock">Stock Cuba</span>
                @endif
              </div>
            </div>

            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
              <div>
                <a href="{{ route('shop.products.show', $rel->slug) }}" class="font-bold text-slate-900 text-xs sm:text-sm hover:text-emerald-700 transition line-clamp-2">
                  {{ $rel->name }}
                </a>
              </div>

              <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-sm font-black text-slate-900 font-mono">${{ number_format((float)$rel->price, 2) }} USD</span>
                <a href="{{ route('shop.products.show', $rel->slug) }}" class="text-xs font-bold text-emerald-700 hover:underline">
                  Ver detalle →
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

</div>

@push('scripts')
<script>
  function incrementQty() {
    const input = document.getElementById('purchase_quantity');
    const max = input.getAttribute('max');
    let val = parseInt(input.value) || 1;
    if (!max || val < parseInt(max)) {
      input.value = val + 1;
    }
  }

  function decrementQty() {
    const input = document.getElementById('purchase_quantity');
    let val = parseInt(input.value) || 1;
    if (val > 1) {
      input.value = val - 1;
    }
  }

  function addToCartDetail() {
    const qty = parseInt(document.getElementById('purchase_quantity').value) || 1;

    fetch('{{ route('shop.cart.add') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        product_id: {{ $product->id }},
        quantity: qty
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

        const alertEl = document.getElementById('cart_added_alert');
        alertEl.classList.remove('hidden');
        setTimeout(() => alertEl.classList.add('hidden'), 3500);
      } else {
        alert(data.message || 'No se pudo agregar al carrito');
      }
    })
    .catch(err => {
      console.error(err);
      window.location.href = '{{ route('shop.cart.index') }}';
    });
  }

  function syncProductDeliveryLocation() {
    const prov = localStorage.getItem('blankisol_delivery_province') || 'La Habana';
    const mun = localStorage.getItem('blankisol_delivery_municipality') || 'Playa';

    const destZone = document.getElementById('product-dest-zone');
    const destBadge = document.getElementById('product-dest-badge');
    const destEta = document.getElementById('product-dest-eta');
    const destDetail = document.getElementById('product-dest-detail');

    if (destZone) destZone.textContent = `${mun}, ${prov}`;

    if (typeof checkDeliveryAvailability === 'function') {
      const info = checkDeliveryAvailability(prov, mun);
      if (destBadge) {
        destBadge.textContent = info.badgeText;
        destBadge.className = `px-2 py-0.5 rounded-full text-[10px] font-bold border ${info.badgeClass}`;
      }
      if (destDetail) {
        destDetail.textContent = info.detailText;
      }
      if (destEta) {
        const isOnDemand = {{ $product->isOnDemand() ? 'true' : 'false' }};
        if (info.type === 'hub_only') {
          destEta.textContent = 'Retiro personal en Almacén Provincial Central o HUB Mariel.';
        } else {
          destEta.textContent = isOnDemand 
            ? '15 a 20 días hábiles (Despacho Marítimo Mariel a Domicilio)' 
            : (['La Habana', 'Artemisa', 'Mayabeque'].includes(prov) ? '24 a 48 horas (Entrega directa puerta a puerta)' : '3 a 5 días hábiles (Envío interprovincial a domicilio)');
        }
      }
    }
  }

  document.addEventListener('DOMContentLoaded', syncProductDeliveryLocation);
  window.addEventListener('blankisol:location-changed', syncProductDeliveryLocation);
</script>
@endpush
@endsection
