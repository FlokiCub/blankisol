@extends('shop.layouts.store')

@section('title', 'Pedido Confirmado #' . $order->order_number . ' — BLANKISOL SHOP')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 animate-fade-in">

  <!-- Success Hero Card (Estilo Tuambia) -->
  <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 text-center relative overflow-hidden shadow-2xs">
    
    <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-4xl mx-auto shadow-2xs">
      <i class="fa-solid fa-circle-check"></i>
    </div>

    <div class="mt-5 space-y-2">
      <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold font-mono uppercase">
        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
        ORDEN REGISTRADA EXITOSAMENTE
      </span>
      <h1 class="text-3xl sm:text-4xl font-black text-slate-900 font-mono tracking-tight">
        #{{ $order->order_number }}
      </h1>
      <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto">
        Hemos registrado tu orden comercial en nuestro sistema logístico. Enviamos el comprobante y snapshot inmutable a <strong>{{ $order->customer_data['email'] ?? 'tu correo electrónico' }}</strong>.
      </p>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
      <button onclick="window.print()" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-2">
        <i class="fa-solid fa-print"></i>
        <span>Imprimir Comprobante</span>
      </button>
      <a href="{{ route('shop.products.index') }}" class="px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
        Continuar Comprando
      </a>
    </div>
  </div>

  <!-- Snapshot Inmutable del Pedido -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Datos de Cliente & Envío -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4">
      <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
        <i class="fa-solid fa-user-check text-emerald-600"></i>
        <span>Datos del Comprador & Entrega</span>
      </h2>

      <div class="space-y-2.5 text-xs text-slate-600">
        @if($order->isBusiness())
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Razón Social B2B:</span>
            <span class="font-bold text-slate-900 text-sm block">{{ $order->customer_data['business_name'] ?? '' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">ID Fiscal:</span>
            <span class="font-mono text-slate-900 font-bold block">{{ $order->customer_data['tax_id'] ?? '' }}</span>
          </div>
        @else
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Cliente Particular (B2C):</span>
            <span class="font-bold text-slate-900 text-sm block">{{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}</span>
          </div>
        @endif

        <div class="pt-2 border-t border-slate-100">
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Dirección de Destino:</span>
          <span class="text-slate-900 font-semibold block">{{ $order->shipping_address['address'] ?? '' }}</span>
          <span class="text-slate-500 block">{{ $order->shipping_address['municipality'] ?? '' }}, {{ $order->shipping_address['province'] ?? '' }}</span>
        </div>

        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Teléfono de Contacto:</span>
          <span class="text-emerald-700 font-mono font-bold block">{{ $order->customer_data['phone'] ?? '' }}</span>
        </div>
      </div>
    </div>

    <!-- Liquidación Financiera Inmutable -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4">
      <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
        <i class="fa-solid fa-calculator text-emerald-600"></i>
        <span>Liquidación Oficial en USD</span>
      </h2>

      <div class="space-y-2.5 text-xs">
        <div class="flex items-center justify-between text-slate-600">
          <span>Subtotal Productos:</span>
          <span class="font-mono font-bold text-slate-900">${{ number_format((float)$order->subtotal, 2) }} USD</span>
        </div>

        @if($order->discount_amount > 0)
          <div class="flex items-center justify-between text-emerald-700 font-semibold">
            <span>Descuento Cupón ({{ $order->coupon_code }}):</span>
            <span class="font-mono font-bold">-${{ number_format((float)$order->discount_amount, 2) }} USD</span>
          </div>
        @endif

        <div class="flex items-center justify-between text-slate-600">
          <span>Tarifa de Entrega:</span>
          <span class="font-mono font-bold text-slate-900">${{ number_format((float)$order->shipping_fee, 2) }} USD</span>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
          <strong class="text-slate-900 text-sm font-extrabold">Total Liquidado:</strong>
          <span class="text-2xl font-black text-slate-900 font-mono">
            ${{ number_format((float)$order->total, 2) }} <span class="text-xs text-emerald-700 font-sans font-bold">USD</span>
          </span>
        </div>

        <div class="pt-2 flex items-center justify-between text-xs">
          <span class="text-slate-500">Estado de Liquidación:</span>
          <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase {{ $order->isPaid() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
            {{ $order->isPaid() ? 'Pagado en USD' : 'Pendiente de Pago' }}
          </span>
        </div>
      </div>
    </div>

  </div>

  <!-- Detalle de Ítems Comprados -->
  <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4">
    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
      Artículos Adquiridos en este Pedido
    </h3>

    <div class="divide-y divide-slate-100 text-xs">
      @foreach($order->items as $item)
        <div class="py-3 flex items-center justify-between">
          <div>
            <span class="font-bold text-slate-900 block">{{ $item->product_name }}</span>
            <span class="text-[10px] text-slate-400 font-mono">SKU: {{ $item->sku }} · Cantidad: {{ $item->quantity }}</span>
          </div>
          <span class="font-mono font-bold text-slate-900">
            ${{ number_format((float)$item->total_price, 2) }} USD
          </span>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
