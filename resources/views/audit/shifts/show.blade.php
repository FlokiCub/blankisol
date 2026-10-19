@extends('layouts.app')

@section('page_title', "Auditoría de Turno #{$shift->id}")

@section('content')
<div class="space-y-6 animate-fade-in">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-bold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-receipt"></i> Detalle de Turno
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Turno #{{ substr($shift->id, 0, 8) }} — {{ $shift->cashRegister?->name }}</h2>
      <p class="text-sm text-slate-400">Sucursal: {{ $shift->cashRegister?->regionalWarehouse?->name }} • Cajero: {{ $shift->cashier?->name }}</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('audit.shifts.print', $shift) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all shadow-md">
        <i class="fa-solid fa-print"></i>
        <span>Imprimir Acta Oficial</span>
      </a>

      <a href="{{ route('audit.shifts.csv', $shift) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-600/30">
        <i class="fa-solid fa-file-csv"></i>
        <span>Exportar CSV</span>
      </a>

      <a href="{{ route('audit.shifts.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition-all">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Volver</span>
      </a>
    </div>
  </div>

  <!-- Financial Balances Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- USD Balance Card -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
          <i class="fa-solid fa-dollar-sign text-emerald-400"></i> Balance Moneda Extranjera (USD)
        </h3>
        <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">USD</span>
      </div>

      <div class="mt-4 space-y-3 text-xs">
        <div class="flex justify-between text-slate-300">
          <span>Fondo Inicial de Apertura:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->opening_balance_usd, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300">
          <span>Total Recaudado en Ventanilla:</span>
          <span class="font-mono font-bold text-emerald-400">+${{ number_format((float) $shift->total_collected_usd, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300 pt-2 border-t border-slate-800">
          <span>Saldo Teórico Esperado:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->expected_balance_usd, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300">
          <span>Arqueo Físico Declarado:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->closing_balance_usd, 2) }}</span>
        </div>
        <div class="flex justify-between font-bold pt-2 border-t border-slate-800">
          <span>Diferencia / Descuadre:</span>
          <span class="font-mono {{ (float) $shift->difference_usd != 0 ? 'text-rose-400' : 'text-emerald-400' }}">
            ${{ number_format((float) $shift->difference_usd, 2) }}
          </span>
        </div>
      </div>
    </div>

    <!-- CUP Balance Card -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
          <i class="fa-solid fa-coins text-blue-400"></i> Balance Moneda Nacional (CUP)
        </h3>
        <span class="text-xs font-mono font-bold text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-lg border border-blue-500/20">CUP</span>
      </div>

      <div class="mt-4 space-y-3 text-xs">
        <div class="flex justify-between text-slate-300">
          <span>Fondo Inicial de Apertura:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->opening_balance_cup, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300">
          <span>Total Recaudado en Ventanilla:</span>
          <span class="font-mono font-bold text-blue-400">+${{ number_format((float) $shift->total_collected_cup, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300 pt-2 border-t border-slate-800">
          <span>Saldo Teórico Esperado:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->expected_balance_cup, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-300">
          <span>Arqueo Físico Declarado:</span>
          <span class="font-mono font-bold text-white">${{ number_format((float) $shift->closing_balance_cup, 2) }}</span>
        </div>
        <div class="flex justify-between font-bold pt-2 border-t border-slate-800">
          <span>Diferencia / Descuadre:</span>
          <span class="font-mono {{ (float) $shift->difference_cup != 0 ? 'text-rose-400' : 'text-blue-400' }}">
            ${{ number_format((float) $shift->difference_cup, 2) }}
          </span>
        </div>
      </div>
    </div>

  </div>

  <!-- Transactions Table Card -->
  <div class="bg-slate-900/60 rounded-3xl border border-slate-800 shadow-xl overflow-hidden backdrop-blur-xl">
    <div class="p-6 border-b border-slate-800 flex items-center justify-between">
      <div>
        <h3 class="text-base font-bold text-white">Transacciones y Pagos del Turno</h3>
        <p class="text-xs text-slate-400">{{ $shift->payments->count() }} pagos procesados</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-700">
          <tr>
            <th class="py-3.5 px-4">No. Recibo</th>
            <th class="py-3.5 px-4">Hora</th>
            <th class="py-3.5 px-4">PIN / Equipo</th>
            <th class="py-3.5 px-4">Cliente</th>
            <th class="py-3.5 px-4">Método</th>
            <th class="py-3.5 px-4 text-right">USD Cobrado</th>
            <th class="py-3.5 px-4 text-right">CUP Cobrado</th>
            <th class="py-3.5 px-4 text-right">Eq. Total USD</th>
            <th class="py-3.5 px-4 text-center">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
          @forelse($shift->payments as $p)
            <tr class="hover:bg-slate-800/30 transition-all">
              <td class="py-3 px-4 font-mono font-bold text-white">{{ $p->receipt_number }}</td>
              <td class="py-3 px-4 text-slate-400">{{ $p->paid_at ? $p->paid_at->format('H:i:s') : $p->created_at->format('H:i:s') }}</td>
              <td class="py-3 px-4">
                <span class="font-mono text-indigo-400 font-bold">{{ $p->equipment?->tracking_pin ?? 'N/A' }}</span>
                <p class="text-[11px] text-slate-400">{{ $p->equipment?->brand }} {{ $p->equipment?->model }}</p>
              </td>
              <td class="py-3 px-4 font-semibold text-white">{{ $p->client_name }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 border border-slate-700 uppercase text-slate-300">
                  {{ $p->payment_method }}
                </span>
              </td>
              <td class="py-3 px-4 text-right font-mono text-emerald-400 font-bold">
                ${{ number_format((float) $p->amount_paid_usd, 2) }}
              </td>
              <td class="py-3 px-4 text-right font-mono text-blue-400 font-bold">
                ${{ number_format((float) $p->amount_paid_cup, 2) }}
              </td>
              <td class="py-3 px-4 text-right font-mono text-white font-bold">
                ${{ number_format((float) $p->equivalent_total_usd, 2) }}
              </td>
              <td class="py-3 px-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                  ✓ Completado
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-slate-500 py-12">
                No se registraron transacciones durante este turno.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
