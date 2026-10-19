@extends('layouts.app')

@section('page_title', 'Arqueos y Cierres de Caja — Auditoría')

@section('content')
<div class="space-y-6 animate-fade-in">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-bold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-cash-register"></i> Auditoría de Turnos
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Historial de Turnos y Arqueos</h2>
      <p class="text-sm text-slate-400">Supervisión de saldos de apertura, cierres declarados, diferencias y faltantes/sobrantes.</p>
    </div>

    <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700">
      <i class="fa-solid fa-arrow-left"></i> Volver a Torre de Control
    </a>
  </div>

  <!-- Filters Filter Bar -->
  <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 backdrop-blur-xl">
    <form method="GET" action="{{ route('audit.shifts.index') }}" class="flex flex-wrap items-center gap-4">
      <div class="flex-1 min-w-[200px]">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sucursal / Almacén</label>
        <select name="warehouse_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 outline-none">
          <option value="">Todos los Almacenes</option>
          @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') === $wh->id ? 'selected' : '' }}>
              {{ $wh->name }} ({{ $wh->province }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="min-w-[150px]">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Estado</label>
        <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-indigo-500 outline-none">
          <option value="">Todos</option>
          <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>En Curso (Abierto)</option>
          <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado / Arqueado</option>
        </select>
      </div>

      <div class="flex items-center pt-5">
        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer select-none">
          <input type="checkbox" name="discrepancy_only" value="1" {{ request('discrepancy_only') ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
          <span>Solo con descuadres</span>
        </label>
      </div>

      <div class="pt-5 flex items-center gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all shadow-md">
          <i class="fa-solid fa-filter mr-1"></i> Filtrar
        </button>
        @if(request()->hasAny(['warehouse_id', 'status', 'discrepancy_only']))
          <a href="{{ route('audit.shifts.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all">
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
            <th class="py-3.5 px-4">ID Turno / Caja</th>
            <th class="py-3.5 px-4">Almacén & Cajero</th>
            <th class="py-3.5 px-4">Apertura / Cierre</th>
            <th class="py-3.5 px-4 text-right">Recaudado USD</th>
            <th class="py-3.5 px-4 text-right">Recaudado CUP</th>
            <th class="py-3.5 px-4 text-right">Descuadre USD</th>
            <th class="py-3.5 px-4 text-center">Estado</th>
            <th class="py-3.5 px-4 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
          @forelse($shifts as $shift)
            @php
              $hasDiff = ((float) $shift->difference_usd != 0) || ((float) $shift->difference_cup != 0);
            @endphp
            <tr class="hover:bg-slate-800/30 transition-all">
              <td class="py-3.5 px-4">
                <span class="font-mono font-bold text-white text-xs">#{{ substr($shift->id, 0, 8) }}</span>
                <p class="text-[11px] text-indigo-400 font-semibold">{{ $shift->cashRegister?->name ?? 'Caja' }}</p>
              </td>
              <td class="py-3.5 px-4">
                <p class="font-bold text-white">{{ $shift->cashRegister?->regionalWarehouse?->name ?? 'N/A' }}</p>
                <p class="text-[11px] text-slate-400">{{ $shift->cashier?->name }}</p>
              </td>
              <td class="py-3.5 px-4">
                <p class="text-white">{{ $shift->opened_at?->format('d/m/Y H:i') }}</p>
                <p class="text-[11px] text-slate-400">{{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : 'En Curso' }}</p>
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                ${{ number_format((float) $shift->total_collected_usd, 2) }}
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold text-blue-400">
                ${{ number_format((float) $shift->total_collected_cup, 2) }}
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold">
                @if($shift->isOpen())
                  <span class="text-slate-500">—</span>
                @elseif($hasDiff)
                  <span class="text-rose-400">${{ number_format((float) $shift->difference_usd, 2) }}</span>
                @else
                  <span class="text-emerald-400">$0.00</span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-center">
                @if($shift->isOpen())
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                    🟢 En Curso
                  </span>
                @elseif($hasDiff)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                    🔴 Con Descuadre
                  </span>
                @else
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300 border border-slate-600">
                    ✓ Cerrado OK
                  </span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('audit.shifts.show', $shift) }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all" title="Ver Detalle">
                    <i class="fa-solid fa-eye text-xs"></i>
                  </a>
                  <a href="{{ route('audit.shifts.print', $shift) }}" target="_blank" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all" title="Imprimir Acta">
                    <i class="fa-solid fa-print text-xs"></i>
                  </a>
                  <a href="{{ route('audit.shifts.csv', $shift) }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all" title="Descargar CSV">
                    <i class="fa-solid fa-file-csv text-xs text-emerald-400"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-slate-500 py-12">
                No se encontraron turnos con los filtros seleccionados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($shifts->hasPages())
      <div class="p-4 border-t border-slate-800">
        {{ $shifts->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
