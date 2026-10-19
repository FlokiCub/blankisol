@extends('layouts.app')

@section('page_title', 'Libro Contable de Pagos — Auditoría')

@section('content')
<div class="space-y-6 animate-fade-in">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-bold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-file-invoice-dollar"></i> Libro Mayor de Ventas
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Libro de Recaudación y Liquidaciones</h2>
      <p class="text-sm text-slate-400">Auditoría detallada de cada cobro de arancel y flete con trazabilidad y métodos de pago.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('audit.payments.csv', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-600/30">
        <i class="fa-solid fa-file-csv"></i>
        <span>Exportar Libro a CSV</span>
      </a>

      <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <!-- Filters Bar -->
  <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-800 backdrop-blur-xl">
    <form method="GET" action="{{ route('audit.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Búsqueda (Recibo / PIN / Cliente)</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: REC-2026 o BK-78492" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 outline-none">
      </div>

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sucursal</label>
        <select name="warehouse_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 outline-none">
          <option value="">Todas las Sucursales</option>
          @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') === $wh->id ? 'selected' : '' }}>
              {{ $wh->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Método de Pago</label>
        <select name="payment_method" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 outline-none">
          <option value="">Todos los Métodos</option>
          <option value="cash_usd" {{ request('payment_method') === 'cash_usd' ? 'selected' : '' }}>Efectivo USD</option>
          <option value="cash_cup" {{ request('payment_method') === 'cash_cup' ? 'selected' : '' }}>Efectivo CUP</option>
          <option value="transfer_cup" {{ request('payment_method') === 'transfer_cup' ? 'selected' : '' }}>Transferencia CUP (EnZona / Transfermóvil)</option>
          <option value="online_usd" {{ request('payment_method') === 'online_usd' ? 'selected' : '' }}>Pasarela Online USD</option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Fecha Desde</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 outline-none">
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all shadow-md">
          <i class="fa-solid fa-filter mr-1"></i> Filtrar
        </button>
        @if(request()->hasAny(['search', 'warehouse_id', 'payment_method', 'date_from', 'date_to']))
          <a href="{{ route('audit.payments.index') }}" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all">
            Limpiar
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Table Card -->
  <div class="bg-slate-900/60 rounded-3xl border border-slate-800 shadow-xl overflow-hidden backdrop-blur-xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-700">
          <tr>
            <th class="py-3.5 px-4">No. Recibo</th>
            <th class="py-3.5 px-4">Fecha & Cajero</th>
            <th class="py-3.5 px-4">PIN / Equipo</th>
            <th class="py-3.5 px-4">Cliente</th>
            <th class="py-3.5 px-4">Método</th>
            <th class="py-3.5 px-4 text-right">USD Cobrado</th>
            <th class="py-3.5 px-4 text-right">CUP Cobrado</th>
            <th class="py-3.5 px-4 text-right">Eq. Total USD</th>
            <th class="py-3.5 px-4 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
          @forelse($payments as $p)
            <tr class="hover:bg-slate-800/30 transition-all">
              <td class="py-3.5 px-4">
                <span class="font-mono font-bold text-white">{{ $p->receipt_number }}</span>
                <p class="text-[10px] text-slate-400">{{ $p->transaction_reference ? 'Ref: ' . $p->transaction_reference : '' }}</p>
              </td>
              <td class="py-3.5 px-4">
                <p class="text-white">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : $p->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-[11px] text-slate-400">{{ $p->cashShift?->cashRegister?->regionalWarehouse?->name ?? 'Sucursal' }} • {{ $p->cashier?->name }}</p>
              </td>
              <td class="py-3.5 px-4">
                <a href="{{ route('audit.equipment.timeline', ['q' => $p->equipment?->tracking_pin]) }}" class="font-mono text-indigo-400 hover:text-indigo-300 font-bold">
                  {{ $p->equipment?->tracking_pin ?? 'N/A' }}
                </a>
                <p class="text-[11px] text-slate-400">{{ $p->equipment?->brand }} {{ $p->equipment?->model }}</p>
              </td>
              <td class="py-3.5 px-4">
                <p class="font-bold text-white">{{ $p->client_name }}</p>
                <p class="text-[11px] text-slate-400">{{ $p->client_phone }}</p>
              </td>
              <td class="py-3.5 px-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 border border-slate-700 uppercase text-slate-300">
                  {{ $p->payment_method }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                ${{ number_format((float) $p->amount_paid_usd, 2) }}
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold text-blue-400">
                ${{ number_format((float) $p->amount_paid_cup, 2) }}
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-black text-white">
                ${{ number_format((float) $p->equivalent_total_usd, 2) }}
              </td>
              <td class="py-3.5 px-4 text-center">
                <a href="{{ route('cashier.receipt', $p) }}" target="_blank" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-400 hover:text-indigo-300 transition-all font-bold" title="Ver Recibo de Pago">
                  <i class="fa-solid fa-receipt text-xs"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-slate-500 py-12">
                No se encontraron pagos con los filtros especificados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($payments->hasPages())
      <div class="p-4 border-t border-slate-800">
        {{ $payments->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
