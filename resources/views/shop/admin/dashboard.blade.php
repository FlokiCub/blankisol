@extends('shop.layouts.admin')

@section('page_title', 'Torre de Control Comercial')

@section('content')
<div class="space-y-8 animate-fade-in">

  <!-- Top Hero / Header Summary -->
  <div class="glass-card rounded-3xl p-6 sm:p-8 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold mb-3">
          <i class="fa-solid fa-store"></i>
          <span>BLANKISOL SHOP B2C / B2B</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
          Panel de Control Ejecutivo
        </h1>
        <p class="text-slate-400 text-sm mt-1 max-w-2xl">
          Monitoreo integral de ventas, inventario físico vs. bajo pedido, cobranzas en USD y procesamiento de despachos comerciales.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('shop.admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40 hover:scale-[1.02]">
          <i class="fa-solid fa-plus"></i>
          <span>Nuevo Producto</span>
        </a>
        <a href="{{ route('shop.admin.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-xs transition-all">
          <i class="fa-solid fa-receipt"></i>
          <span>Ver Todos los Pedidos</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Primary KPI Metrics Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <!-- KPI 1: Ventas Totales en USD -->
    <div class="glass-card rounded-3xl p-6 flex flex-col justify-between hover:border-emerald-500/30 transition-all group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Facturación Pagada</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 border border-emerald-500/25 text-emerald-400 flex items-center justify-center text-sm shadow-inner group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-dollar-sign"></i>
        </div>
      </div>
      <div class="mt-4">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-mono tracking-tight">
          ${{ number_format($totalSales, 2) }} <span class="text-xs text-emerald-400 font-sans font-bold">USD</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
          <span class="text-emerald-400 font-semibold font-mono">{{ $paidOrdersCount }}</span> pedidos liquidados
        </p>
      </div>
    </div>

    <!-- KPI 2: Pedidos Totales & En Flujo -->
    <div class="glass-card rounded-3xl p-6 flex flex-col justify-between hover:border-brand-500/30 transition-all group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Flujo de Órdenes</span>
        <div class="w-10 h-10 rounded-2xl bg-brand-500/15 border border-brand-500/25 text-brand-400 flex items-center justify-center text-sm shadow-inner group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-boxes-packing"></i>
        </div>
      </div>
      <div class="mt-4">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-mono tracking-tight">
          {{ $ordersCount }} <span class="text-xs text-slate-400 font-sans font-normal">totales</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
          <span class="text-amber-400 font-semibold font-mono">{{ $pendingOrdersCount }}</span> pendientes ·
          <span class="text-cyan-400 font-semibold font-mono">{{ $processingOrdersCount }}</span> preparación
        </p>
      </div>
    </div>

    <!-- KPI 3: Catálogo Activo & Modalidades -->
    <div class="glass-card rounded-3xl p-6 flex flex-col justify-between hover:border-purple-500/30 transition-all group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Catálogo Activo</span>
        <div class="w-10 h-10 rounded-2xl bg-purple-500/15 border border-purple-500/25 text-purple-400 flex items-center justify-center text-sm shadow-inner group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-layer-group"></i>
        </div>
      </div>
      <div class="mt-4">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-mono tracking-tight">
          {{ $activeProductsCount }} <span class="text-xs text-slate-400 font-sans font-normal">publicados</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
          <span class="text-purple-400 font-semibold font-mono">{{ $onDemandProductsCount }}</span> bajo pedido (On Demand)
        </p>
      </div>
    </div>

    <!-- KPI 4: Alertas de Inventario Físico -->
    <div class="glass-card rounded-3xl p-6 flex flex-col justify-between hover:border-rose-500/30 transition-all group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alertas de Stock</span>
        <div class="w-10 h-10 rounded-2xl {{ $lowStockCount > 0 ? 'bg-rose-500/15 border-rose-500/25 text-rose-400' : 'bg-slate-800 border-slate-700 text-slate-400' }} border flex items-center justify-center text-sm shadow-inner group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
      </div>
      <div class="mt-4">
        <h3 class="text-2xl sm:text-3xl font-extrabold {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-slate-300' }} font-mono tracking-tight">
          {{ $lowStockCount }} <span class="text-xs text-slate-400 font-sans font-normal">críticos</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1">
          Stock controlado &le; 5 unidades
        </p>
      </div>
    </div>

  </div>

  <!-- Order Flow Status Pipeline Breakdown -->
  <div class="glass-card rounded-3xl p-6 sm:p-7">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h2 class="text-base font-bold text-white tracking-tight">Embudo Operativo de Pedidos</h2>
        <p class="text-xs text-slate-400">Distribución de estados en el ciclo de vida del pedido</p>
      </div>
      <span class="text-xs font-mono text-slate-400">{{ $ordersCount }} Pedidos Registrados</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
      <!-- 1. Pendientes -->
      <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex flex-col items-center text-center">
        <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Pendiente</span>
        <span class="text-2xl font-black text-white font-mono mt-1">{{ $pendingOrdersCount }}</span>
        <span class="text-[10px] text-slate-400 mt-0.5">Esperando Pago / Validación</span>
      </div>

      <!-- 2. Pagados -->
      <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col items-center text-center">
        <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Pagado</span>
        <span class="text-2xl font-black text-white font-mono mt-1">{{ $paidOrdersCount }}</span>
        <span class="text-[10px] text-slate-400 mt-0.5">Fondos Recibidos</span>
      </div>

      <!-- 3. En Preparación -->
      <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex flex-col items-center text-center">
        <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider">Preparación</span>
        <span class="text-2xl font-black text-white font-mono mt-1">{{ $processingOrdersCount }}</span>
        <span class="text-[10px] text-slate-400 mt-0.5">Picking / Embalaje</span>
      </div>

      <!-- 4. Despachado -->
      <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex flex-col items-center text-center">
        <span class="text-[11px] font-bold text-blue-400 uppercase tracking-wider">Despachado</span>
        <span class="text-2xl font-black text-white font-mono mt-1">{{ $shippedOrdersCount }}</span>
        <span class="text-[10px] text-slate-400 mt-0.5">En Tránsito / Ruta</span>
      </div>

      <!-- 5. Entregado -->
      <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex flex-col items-center text-center col-span-2 sm:col-span-1">
        <span class="text-[11px] font-bold text-purple-400 uppercase tracking-wider">Entregado</span>
        <span class="text-2xl font-black text-white font-mono mt-1">{{ $deliveredOrdersCount }}</span>
        <span class="text-[10px] text-slate-400 mt-0.5">Completado con Éxito</span>
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Recent Orders & Stock Alerts / Secondary Info -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Ultimos Pedidos Recientes (Span 2) -->
    <div class="lg:col-span-2 glass-card rounded-3xl p-6 sm:p-7">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
          <h2 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-emerald-400 text-sm"></i>
            <span>Últimos Pedidos Registrados</span>
          </h2>
          <p class="text-xs text-slate-400">Transacciones comerciales B2C y B2B más recientes</p>
        </div>
        <a href="{{ route('shop.admin.orders.index') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
          Ver todos &rarr;
        </a>
      </div>

      <div class="mt-4 overflow-x-auto">
        @if($recentOrders->count() > 0)
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-slate-400 border-b border-slate-800/80">
                <th class="pb-3 font-semibold">Orden</th>
                <th class="pb-3 font-semibold">Cliente</th>
                <th class="pb-3 font-semibold">Tipo</th>
                <th class="pb-3 font-semibold text-right">Total USD</th>
                <th class="pb-3 font-semibold text-center">Estado Pago</th>
                <th class="pb-3 font-semibold text-center">Estado Pedido</th>
                <th class="pb-3 font-semibold text-right">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
              @foreach($recentOrders as $order)
                <tr class="hover:bg-slate-900/40 transition-colors">
                  <td class="py-3 font-mono font-bold text-white">
                    #{{ $order->order_number }}
                  </td>
                  <td class="py-3 text-slate-300">
                    @if($order->isBusiness())
                      <span class="font-semibold text-white block">{{ $order->customer_data['business_name'] ?? 'Empresa' }}</span>
                      <span class="text-[10px] text-slate-400">{{ $order->customer_data['email'] ?? '' }}</span>
                    @else
                      <span class="font-semibold text-white block">{{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}</span>
                      <span class="text-[10px] text-slate-400">{{ $order->customer_data['email'] ?? '' }}</span>
                    @endif
                  </td>
                  <td class="py-3">
                    @if($order->isBusiness())
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30">B2B</span>
                    @else
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">B2C</span>
                    @endif
                  </td>
                  <td class="py-3 text-right font-mono font-bold text-white">
                    ${{ number_format((float)$order->total, 2) }}
                  </td>
                  <td class="py-3 text-center">
                    @if($order->payment_status === 'paid')
                      <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">Pagado</span>
                    @elseif($order->payment_status === 'refunded')
                      <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/15 text-rose-300 border border-rose-500/30">Reembolsado</span>
                    @else
                      <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30">Sin pagar</span>
                    @endif
                  </td>
                  <td class="py-3 text-center">
                    @php
                      $statusColors = [
                        'pending' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                        'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                        'processing' => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                        'shipped' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
                        'delivered' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                        'cancelled' => 'bg-slate-800 text-slate-400 border-slate-700',
                      ];
                      $color = $statusColors[$order->status] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                    @endphp
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $color }}">
                      {{ ucfirst($order->status) }}
                    </span>
                  </td>
                  <td class="py-3 text-right">
                    <a href="{{ route('shop.admin.orders.show', $order) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold transition-colors">
                      <span>Ver</span>
                      <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="py-12 text-center text-slate-400">
            <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-600 block"></i>
            <p class="font-medium">No hay pedidos registrados en la tienda todavía.</p>
            <p class="text-[11px] text-slate-500 mt-1">Los pedidos ingresados en la tienda virtual aparecerán listados aquí.</p>
          </div>
        @endif
      </div>
    </div>

    <!-- Right Column: Alertas de Stock y Clientes / Cupones (Span 1) -->
    <div class="space-y-6">
      
      <!-- Box 1: Alertas de Stock Bajo -->
      <div class="glass-card rounded-3xl p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h2 class="text-sm font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
            <span>Stock Físico Crítico</span>
          </h2>
          <span class="text-xs font-mono font-bold text-amber-400">&le; 5 unids</span>
        </div>

        <div class="mt-4 space-y-3">
          @forelse($lowStockProducts as $lowItem)
            <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ $lowItem->name }}</p>
                <p class="text-[10px] font-mono text-slate-400">SKU: {{ $lowItem->sku }}</p>
              </div>
              <div class="shrink-0 text-right">
                <span class="px-2 py-0.5 rounded text-[10px] font-black font-mono {{ $lowItem->stock_quantity == 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                  {{ $lowItem->stock_quantity }} unids
                </span>
              </div>
            </div>
          @empty
            <div class="py-6 text-center text-slate-500 text-xs">
              <i class="fa-solid fa-circle-check text-emerald-400 text-xl mb-1 block"></i>
              No hay productos con stock crítico en este momento.
            </div>
          @endforelse
        </div>
      </div>

      <!-- Box 2: Ecosistema Clientes y Cupones -->
      <div class="glass-card rounded-3xl p-6">
        <h2 class="text-sm font-bold text-white pb-3 border-b border-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-users text-purple-400"></i>
          <span>Audiencia Comercial B2C / B2B</span>
        </h2>

        <div class="mt-4 grid grid-cols-2 gap-3">
          <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Clientes B2C</span>
            <span class="text-xl font-extrabold text-white font-mono mt-1 block">{{ $totalCustomers - $b2bCustomersCount }}</span>
            <span class="text-[10px] text-slate-500">Personas Particulares</span>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800">
            <span class="text-[10px] uppercase font-bold text-purple-400 block">Clientes B2B</span>
            <span class="text-xl font-extrabold text-white font-mono mt-1 block">{{ $b2bCustomersCount }}</span>
            <span class="text-[10px] text-slate-500">Empresas e Instituciones</span>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
          <span class="text-slate-400">Cupones de Descuento Activos:</span>
          <span class="font-mono font-bold text-emerald-400">{{ $activeCouponsCount }}</span>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection
