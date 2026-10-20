@extends('shop.layouts.admin')

@section('page_title', 'Expediente de Pedido #' . $order->order_number)

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="glass-card rounded-3xl p-6 sm:p-7 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="font-mono text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-bold border border-slate-700">ORDEN COMERCIAL</span>
        <span class="text-xs text-slate-400 font-mono">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-black text-white font-mono tracking-tight flex items-center gap-3">
        <span>#{{ $order->order_number }}</span>
        @if($order->isBusiness())
          <span class="px-2.5 py-0.5 rounded text-xs font-bold font-sans bg-purple-500/15 text-purple-300 border border-purple-500/30">CLIENTE B2B</span>
        @else
          <span class="px-2.5 py-0.5 rounded text-xs font-bold font-sans bg-blue-500/15 text-blue-300 border border-blue-500/30">CLIENTE B2C</span>
        @endif
      </h1>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('shop.admin.orders.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-semibold transition-all">
        &larr; Volver a pedidos
      </a>
    </div>
  </div>

  <!-- Status Transition Timeline Bar -->
  <div class="glass-card rounded-3xl p-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
      
      <!-- Creado -->
      <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
        <span class="text-[10px] uppercase font-bold text-slate-400 block">Registrado</span>
        <span class="text-xs font-mono font-semibold text-white mt-1 block">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        <span class="text-[10px] text-emerald-400 mt-0.5 block"><i class="fa-solid fa-check"></i> Orden Creada</span>
      </div>

      <!-- Pagado -->
      <div class="p-3 rounded-2xl {{ $order->paid_at ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-slate-900/40 border-slate-800' }} border">
        <span class="text-[10px] uppercase font-bold {{ $order->paid_at ? 'text-emerald-400' : 'text-slate-500' }} block">Pago Recibido</span>
        <span class="text-xs font-mono font-semibold text-white mt-1 block">{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : 'Pendiente' }}</span>
        <span class="text-[10px] {{ $order->paid_at ? 'text-emerald-300' : 'text-slate-500' }} mt-0.5 block font-mono">USD ${{ number_format((float)$order->total, 2) }}</span>
      </div>

      <!-- Despachado -->
      <div class="p-3 rounded-2xl {{ $order->shipped_at ? 'bg-blue-500/10 border-blue-500/20' : 'bg-slate-900/40 border-slate-800' }} border">
        <span class="text-[10px] uppercase font-bold {{ $order->shipped_at ? 'text-blue-400' : 'text-slate-500' }} block">Despachado</span>
        <span class="text-xs font-mono font-semibold text-white mt-1 block">{{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : 'En almacén' }}</span>
        <span class="text-[10px] {{ $order->shipped_at ? 'text-blue-300' : 'text-slate-500' }} mt-0.5 block">Guía de Entrega</span>
      </div>

      <!-- Entregado -->
      <div class="p-3 rounded-2xl {{ $order->delivered_at ? 'bg-purple-500/10 border-purple-500/20' : 'bg-slate-900/40 border-slate-800' }} border">
        <span class="text-[10px] uppercase font-bold {{ $order->delivered_at ? 'text-purple-400' : 'text-slate-500' }} block">Entregado</span>
        <span class="text-xs font-mono font-semibold text-white mt-1 block">{{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : 'En proceso' }}</span>
        <span class="text-[10px] {{ $order->delivered_at ? 'text-purple-300' : 'text-slate-500' }} mt-0.5 block">Hito Final</span>
      </div>

    </div>
  </div>

  <!-- Main Grid: Items & Calculation Left (2 cols), Details & Actions Right (1 col) -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Items & Financial Breakdown (Span 2) -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- Purchased Items Table -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
          <span>Artículos Comprados (Snapshot Inmutable)</span>
        </h2>

        <div class="mt-4 overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 border-b border-slate-800">
                <th class="pb-3 font-semibold">SKU / Producto</th>
                <th class="pb-3 font-semibold text-center">Modalidad</th>
                <th class="pb-3 font-semibold text-center">Cantidad</th>
                <th class="pb-3 font-semibold text-right">Precio Unit. (USD)</th>
                <th class="pb-3 font-semibold text-right">Subtotal (USD)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
              @foreach($order->items as $item)
                <tr class="hover:bg-slate-900/40 transition-colors">
                  <td class="py-3.5">
                    <span class="font-mono text-[10px] text-emerald-400 font-bold block">{{ $item->sku }}</span>
                    <span class="font-bold text-white text-sm block">{{ $item->product_name }}</span>
                  </td>
                  <td class="py-3.5 text-center">
                    @if($item->inventory_type === 'on_demand')
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30">
                        Bajo Pedido
                      </span>
                    @else
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                        Stock Físico
                      </span>
                    @endif
                  </td>
                  <td class="py-3.5 text-center font-mono font-bold text-white">
                    {{ $item->quantity }}
                  </td>
                  <td class="py-3.5 text-right font-mono text-slate-300">
                    ${{ number_format((float)$item->unit_price, 2) }}
                  </td>
                  <td class="py-3.5 text-right font-mono font-bold text-white">
                    ${{ number_format((float)$item->total_price, 2) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Financial Calculation Snapshot in USD -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-calculator text-emerald-400"></i>
          <span>Liquidación Financiera Oficial (Moneda Base: USD)</span>
        </h2>

        <div class="mt-4 space-y-2.5 text-xs">
          <div class="flex items-center justify-between text-slate-300">
            <span>Subtotal de Productos:</span>
            <span class="font-mono font-bold text-white">${{ number_format((float)$order->subtotal, 2) }} USD</span>
          </div>

          @if($order->discount_amount > 0)
            <div class="flex items-center justify-between text-emerald-400">
              <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-tag"></i> Descuento Cupón ({{ $order->coupon_code ?? 'PROMO' }}):
              </span>
              <span class="font-mono font-bold">-${{ number_format((float)$order->discount_amount, 2) }} USD</span>
            </div>
          @endif

          <div class="flex items-center justify-between text-slate-300">
            <span>Tarifa de Entrega / Envío:</span>
            <span class="font-mono font-bold text-white">${{ number_format((float)$order->shipping_fee, 2) }} USD</span>
          </div>

          @if($order->tax_amount > 0)
            <div class="flex items-center justify-between text-slate-300">
              <span>Impuestos Calculados:</span>
              <span class="font-mono font-bold text-white">${{ number_format((float)$order->tax_amount, 2) }} USD</span>
            </div>
          @endif

          <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-base">
            <span class="font-bold text-white">Total a Cobrar:</span>
            <span class="font-mono font-black text-xl text-emerald-400">
              ${{ number_format((float)$order->total, 2) }} <span class="text-xs font-sans text-emerald-300 font-bold">USD</span>
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Right Column: Customer Info, Shipping & Status Action (Span 1) -->
    <div class="space-y-6">
      
      <!-- Change Order Status Form -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-arrows-spin text-emerald-400"></i>
          <span>Actualizar Estado del Pedido</span>
        </h2>

        <form method="POST" action="{{ route('shop.admin.orders.update-status', $order) }}" class="mt-4 space-y-4">
          @csrf

          <div>
            <label for="status" class="block text-xs font-semibold text-slate-300 mb-1.5">Estado Operativo</label>
            <select name="status" id="status" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
              <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pendiente (Esperando Validación)</option>
              <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Pagado (Fondos Confirmados)</option>
              <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En Preparación (Picking / Almacén)</option>
              <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Despachado (En Tránsito / Ruta)</option>
              <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Entregado (Completado con Éxito)</option>
              <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado (Devolución de Inventario)</option>
            </select>
          </div>

          <div>
            <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1.5">Observaciones / Notas Internas</label>
            <textarea name="notes" id="notes" rows="3" placeholder="Añadir notas operativas..." class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('notes', $order->notes) }}</textarea>
          </div>

          <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40">
            Actualizar Estado
          </button>
        </form>
      </div>

      <!-- Customer Details Card -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-user-tie text-purple-400"></i>
          <span>Datos del Cliente</span>
        </h2>

        <div class="mt-4 space-y-2 text-xs">
          @if($order->isBusiness())
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Razón Social B2B:</span>
              <span class="font-bold text-white text-sm block">{{ $order->customer_data['business_name'] ?? 'Empresa' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">ID Fiscal / CIF:</span>
              <span class="font-mono text-white block">{{ $order->customer_data['tax_id'] ?? 'N/D' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Contacto de Compras:</span>
              <span class="text-white block">{{ $order->customer_data['contact'] ?? ($order->customer_data['first_name'] ?? '') }}</span>
            </div>
          @else
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Nombre Completo B2C:</span>
              <span class="font-bold text-white text-sm block">{{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}</span>
            </div>
          @endif

          <div class="pt-2 border-t border-slate-800/80">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Correo Electrónico:</span>
            <span class="font-mono text-emerald-400 block">{{ $order->customer_data['email'] ?? 'N/D' }}</span>
          </div>

          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Teléfono de Contacto:</span>
            <span class="font-mono text-slate-200 block">{{ $order->customer_data['phone'] ?? 'N/D' }}</span>
          </div>
        </div>
      </div>

      <!-- Shipping Address Card -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-location-dot text-blue-400"></i>
          <span>Destino de Entrega</span>
        </h2>

        <div class="mt-4 space-y-2 text-xs text-slate-300">
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Dirección:</span>
            <span class="font-semibold text-white block">{{ $order->shipping_address['address'] ?? 'Dirección no provista' }}</span>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Provincia:</span>
              <span class="text-white block">{{ $order->shipping_address['province'] ?? 'N/D' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Municipio:</span>
              <span class="text-white block">{{ $order->shipping_address['municipality'] ?? 'N/D' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection
