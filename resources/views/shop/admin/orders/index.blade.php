@extends('shop.layouts.admin')

@section('page_title', 'Gestión de Pedidos & Ventas')

@section('content')
<div class="space-y-6">

  <!-- Header / Action bar -->
  <div class="glass-card rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-receipt text-emerald-400"></i>
        <span>Órdenes de Compra B2C / B2B</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        Control de estados de pago, trazabilidad de envíos y snapshots financieros inmutables en USD.
      </p>
    </div>
  </div>

  <!-- Filters & Search Bar -->
  <div class="glass-card rounded-3xl p-5">
    <form method="GET" action="{{ route('shop.admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      
      <!-- Búsqueda por Número de Orden o Cliente -->
      <div class="lg:col-span-2">
        <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Buscar Orden</label>
        <div class="relative">
          <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Número (ej. BK-), email o empresa..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-mono">
          <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs absolute left-3 top-3"></i>
        </div>
      </div>

      <!-- Filtro Estado Pedido -->
      <div>
        <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estado de la Orden</label>
        <select name="status" id="status" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <option value="">Todos los estados</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
          <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
          <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>En Preparación</option>
          <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Despachado / Tránsito</option>
          <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregado</option>
          <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
      </div>

      <!-- Filtro Tipo Cliente -->
      <div>
        <label for="customer_type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Segmento Cliente</label>
        <select name="customer_type" id="customer_type" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <option value="">B2C y B2B</option>
          <option value="individual" {{ request('customer_type') === 'individual' ? 'selected' : '' }}>B2C (Consumidor Final)</option>
          <option value="business" {{ request('customer_type') === 'business' ? 'selected' : '' }}>B2B (Empresa / Institución)</option>
        </select>
      </div>

      <!-- Botón Filtrar -->
      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition-all">
          <i class="fa-solid fa-filter mr-1"></i> Filtrar
        </button>
        @if(request()->hasAny(['search', 'status', 'payment_status', 'customer_type']))
          <a href="{{ route('shop.admin.orders.index') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs" title="Limpiar filtros">
            <i class="fa-solid fa-rotate-left"></i>
          </a>
        @endif
      </div>

    </form>
  </div>

  <!-- Orders Table -->
  <div class="glass-card rounded-3xl overflow-hidden p-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 border-b border-slate-800">
            <th class="pb-3 font-semibold">Orden #</th>
            <th class="pb-3 font-semibold">Fecha</th>
            <th class="pb-3 font-semibold">Cliente</th>
            <th class="pb-3 font-semibold">Tipo</th>
            <th class="pb-3 font-semibold text-center">Ítems</th>
            <th class="pb-3 font-semibold text-right">Total USD</th>
            <th class="pb-3 font-semibold text-center">Pago</th>
            <th class="pb-3 font-semibold text-center">Estado</th>
            <th class="pb-3 font-semibold text-right">Detalle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @forelse($orders as $order)
            <tr class="hover:bg-slate-900/40 transition-colors">
              <td class="py-3.5 font-mono font-bold text-white">
                #{{ $order->order_number }}
              </td>
              <td class="py-3.5 text-slate-400 font-mono text-[11px]">
                {{ $order->created_at->format('d/m/Y H:i') }}
              </td>
              <td class="py-3.5 text-slate-300">
                @if($order->isBusiness())
                  <span class="font-bold text-white block">{{ $order->customer_data['business_name'] ?? 'Empresa' }}</span>
                  <span class="text-[10px] text-slate-400 block">ID: {{ $order->customer_data['tax_id'] ?? 'N/D' }} · {{ $order->customer_data['email'] ?? '' }}</span>
                @else
                  <span class="font-bold text-white block">{{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}</span>
                  <span class="text-[10px] text-slate-400 block">{{ $order->customer_data['email'] ?? '' }}</span>
                @endif
              </td>
              <td class="py-3.5">
                @if($order->isBusiness())
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30">B2B</span>
                @else
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">B2C</span>
                @endif
              </td>
              <td class="py-3.5 text-center font-mono text-slate-300">
                {{ $order->items->count() }}
              </td>
              <td class="py-3.5 text-right font-mono font-bold text-white">
                ${{ number_format((float)$order->total, 2) }}
              </td>
              <td class="py-3.5 text-center">
                @if($order->payment_status === 'paid')
                  <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">Pagado</span>
                @elseif($order->payment_status === 'refunded')
                  <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/15 text-rose-300 border border-rose-500/30">Reembolsado</span>
                @else
                  <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30">Pendiente</span>
                @endif
              </td>
              <td class="py-3.5 text-center">
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
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $color }}">
                  {{ ucfirst($order->status) }}
                </span>
              </td>
              <td class="py-3.5 text-right">
                <a href="{{ route('shop.admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                  <span>Ver Expediente</span>
                  <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="py-12 text-center text-slate-500">
                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-600 block"></i>
                No se encontraron órdenes registradas con los filtros actuales.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
      <div class="pt-4 border-t border-slate-800">
        {{ $orders->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
