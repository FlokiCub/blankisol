@extends('shop.layouts.store')

@section('title', 'Carrito de Compras — BLANKISOL SHOP (USD)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Breadcrumbs & Title -->
  <div>
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
      <a href="{{ route('shop.index') }}" class="hover:text-emerald-700 transition">Tienda</a>
      <span>/</span>
      <span class="text-emerald-700 font-bold">Carrito de Compras</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
      <i class="fa-solid fa-cart-shopping text-emerald-600"></i>
      <span>Tu Carrito de Compras</span>
    </h1>
  </div>

  @if($summary['is_empty'])
    <!-- Empty Cart State (Tuambia Style) -->
    <div class="bg-white rounded-3xl p-16 text-center text-slate-500 border border-slate-200 space-y-4 shadow-2xs">
      <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-4xl mx-auto">
        <i class="fa-solid fa-cart-shopping"></i>
      </div>
      <div>
        <h3 class="text-lg font-bold text-slate-800">Tu carrito está vacío</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
          Explora nuestro catálogo en USD para añadir motos eléctricas, maquinaria, generadores o sistemas solares con entrega en Cuba.
        </p>
      </div>
      <div class="pt-2">
        <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
          <i class="fa-solid fa-box-open"></i>
          <span>Explorar Catálogo</span>
        </a>
      </div>
    </div>
  @else
    <!-- Cart Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Left Column: Items & Coupon (Span 2) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Items Box -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs">
          <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-emerald-600"></i>
            <span>Artículos Seleccionados ({{ count($summary['items']) }})</span>
          </h2>

          <div class="divide-y divide-slate-100">
            @foreach($cart->items as $cartItem)
              @if($cartItem->product)
                <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                  
                  <!-- Product Info -->
                  <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                      <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div class="min-w-0">
                      <span class="text-[10px] font-mono text-emerald-700 font-bold block">{{ $cartItem->product->sku }}</span>
                      <a href="{{ route('shop.products.show', $cartItem->product->slug) }}" class="font-bold text-slate-900 text-xs sm:text-sm hover:text-emerald-700 transition block truncate">
                        {{ $cartItem->product->name }}
                        @if($cartItem->variant)
                          <span class="text-slate-500 text-xs font-normal">({{ $cartItem->variant->name }})</span>
                        @endif
                      </a>
                      <div class="mt-1">
                        @if($cartItem->product->isOnDemand())
                          <span class="tuambia-badge-ondemand">
                            Bajo Pedido ({{ $cartItem->product->estimated_delivery_days ?: '15-20 días' }})
                          </span>
                        @else
                          <span class="tuambia-badge-stock">
                            Stock Cuba 24-48h
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>

                  <!-- Qty and Price Actions -->
                  <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-slate-100">
                    
                    <!-- Formulario de Cantidad -->
                    <form method="POST" action="{{ route('shop.cart.update', $cartItem) }}" class="flex items-center rounded-2xl bg-slate-100 border border-slate-200 px-2.5 py-1">
                      @csrf
                      @method('PATCH')
                      <button type="submit" name="quantity" value="{{ $cartItem->quantity - 1 }}" class="text-slate-600 hover:text-slate-950 px-1.5 py-0.5 text-xs font-bold">-</button>
                      <span class="w-8 text-center font-mono text-slate-900 text-xs font-bold">{{ $cartItem->quantity }}</span>
                      <button type="submit" name="quantity" value="{{ $cartItem->quantity + 1 }}" class="text-slate-600 hover:text-slate-950 px-1.5 py-0.5 text-xs font-bold">+</button>
                    </form>

                    <!-- Precio Total Línea -->
                    <div class="text-right min-w-[80px]">
                      <span class="text-sm font-black text-slate-900 font-mono block">
                        ${{ number_format((float)($cartItem->unit_price * $cartItem->quantity), 2) }}
                      </span>
                      <span class="text-[10px] text-slate-400 font-mono">
                        ${{ number_format((float)$cartItem->unit_price, 2) }} c/u
                      </span>
                    </div>

                    <!-- Botón Eliminar Ítem -->
                    <form method="POST" action="{{ route('shop.cart.remove', $cartItem) }}" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" title="Eliminar ítem" class="p-2 text-slate-400 hover:text-rose-600 transition text-xs">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </form>

                  </div>

                </div>
              @endif
            @endforeach
          </div>
        </div>

        <!-- Cupón de Descuento en USD -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-3">
          <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-ticket text-emerald-600"></i>
            <span>¿Tienes un Cupón de Descuento?</span>
          </h3>

          @if($cart->coupon_code)
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-emerald-900 font-semibold">Cupón aplicado: <strong class="font-mono text-emerald-700">{{ $cart->coupon_code }}</strong></span>
              </div>
              <form method="POST" action="{{ route('shop.cart.coupon.remove') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold underline">
                  Quitar
                </button>
              </form>
            </div>
          @else
            <form method="POST" action="{{ route('shop.cart.coupon.apply') }}" class="flex gap-2">
              @csrf
              <input 
                type="text" 
                name="coupon_code" 
                placeholder="Código promocional (ej: BIENVENIDO)" 
                class="flex-1 px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white"
                required
              >
              <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                Aplicar
              </button>
            </form>
          @endif
        </div>

      </div>

      <!-- Right Column: Order Summary (Span 1) -->
      <div class="space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-2xs space-y-6 sticky top-28">
          
          <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-emerald-600"></i>
            <span>Resumen del Pedido</span>
          </h3>

          <!-- Destino de Entrega Seleccionado -->
          <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-center justify-between">
            <div class="flex items-center gap-2 truncate">
              <i class="fa-solid fa-location-dot text-amber-600 shrink-0"></i>
              <span class="truncate">Destino: <strong id="cart-dest-label">Playa, La Habana</strong></span>
            </div>
            <button type="button" onclick="openZoneModal()" class="text-[11px] font-bold text-amber-900 underline shrink-0 ml-1">
              Cambiar
            </button>
          </div>

          <!-- Subtotales y Desglose en USD -->
          <div class="space-y-3 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Subtotal Productos:</span>
              <span class="font-mono font-bold text-slate-900">${{ number_format((float) ($summary['subtotal'] ?? $summary['totals']['subtotal'] ?? 0), 2) }} USD</span>
            </div>

            @if((float) ($summary['discount_amount'] ?? $summary['totals']['discount_amount'] ?? 0) > 0)
              <div class="flex justify-between text-emerald-700 font-semibold">
                <span>Descuento Cupón:</span>
                <span class="font-mono">-${{ number_format((float) ($summary['discount_amount'] ?? $summary['totals']['discount_amount'] ?? 0), 2) }} USD</span>
              </div>
            @endif

            <div class="flex justify-between text-slate-600">
              <span>Envío Estimado:</span>
              @if((float) ($summary['shipping_fee'] ?? $summary['totals']['shipping_fee'] ?? 0) > 0)
                <span class="font-mono font-bold text-slate-900">${{ number_format((float) ($summary['shipping_fee'] ?? $summary['totals']['shipping_fee'] ?? 0), 2) }} USD</span>
              @else
                <span class="text-emerald-700 font-bold">Por calcular en checkout</span>
              @endif
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
              <div>
                <strong class="text-slate-900 text-sm font-extrabold block">Total Liquidación:</strong>
                <span class="text-[10px] text-slate-400 font-mono">Dólares Americanos</span>
              </div>
              <span class="text-2xl font-black text-slate-900 font-mono">
                ${{ number_format((float) ($summary['total'] ?? $summary['totals']['total'] ?? 0), 2) }} <span class="text-xs text-emerald-700 font-sans font-bold">USD</span>
              </span>
            </div>
          </div>

          <!-- Botón de Checkout -->
          <div>
            <a href="{{ route('shop.checkout.index') }}" class="w-full py-3.5 px-4 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-xs flex items-center justify-center gap-2">
              <i class="fa-solid fa-credit-card"></i>
              <span>Proceder al Checkout</span>
            </a>
          </div>

          <div class="text-[11px] text-slate-500 text-center space-y-1">
            <p><i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> Transacción segura y protegida en USD</p>
            <p class="text-[10px]">Facturas válidas para personas naturales (B2C) y empresas (B2B).</p>
          </div>

        </div>
      </div>

    </div>
  @endif

</div>

@push('scripts')
<script>
  function syncCartLocation() {
    const prov = localStorage.getItem('blankisol_delivery_province') || 'La Habana';
    const mun = localStorage.getItem('blankisol_delivery_municipality') || 'Playa';
    const el = document.getElementById('cart-dest-label');
    if (el) el.textContent = `${mun}, ${prov}`;
  }
  document.addEventListener('DOMContentLoaded', syncCartLocation);
  window.addEventListener('blankisol:location-changed', syncCartLocation);
</script>
@endpush
@endsection
