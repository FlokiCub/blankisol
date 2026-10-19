@extends('layouts.app')

@section('page_title', 'Torre de Control — Auditoría y Finanzas')

@section('content')
<div class="space-y-8 animate-fade-in">

  <!-- Header & Actions -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-bold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-shield-halved"></i> Perfil Auditor & Dirección
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Auditoría Financiera y Trazabilidad</h2>
      <p class="text-sm text-slate-400">Consolidado general de recaudación multimoneda, arqueos de sucursales y bitácora de estados.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('audit.shifts.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all shadow-md">
        <i class="fa-solid fa-cash-register text-emerald-400"></i>
        <span>Arqueos de Caja</span>
      </a>

      <a href="{{ route('audit.payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all shadow-md">
        <i class="fa-solid fa-receipt text-indigo-400"></i>
        <span>Libro de Pagos</span>
      </a>

      <a href="{{ route('audit.equipment.timeline') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all shadow-lg shadow-purple-600/30">
        <i class="fa-solid fa-timeline"></i>
        <span>Buscar Trazabilidad PIN</span>
      </a>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <!-- Total USD -->
    <div class="bg-gradient-to-br from-emerald-950/40 via-slate-900 to-slate-900 p-6 rounded-3xl border border-emerald-500/20 shadow-xl relative overflow-hidden group">
      <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:scale-125 transition-all"></div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Recaudado USD</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
          <i class="fa-solid fa-dollar-sign text-lg"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-black text-white tracking-tight">${{ number_format($summary['total_usd'], 2) }}</div>
        <p class="text-xs text-slate-400 mt-1">Cobros directos en divisa</p>
      </div>
    </div>

    <!-- Total CUP -->
    <div class="bg-gradient-to-br from-blue-950/40 via-slate-900 to-slate-900 p-6 rounded-3xl border border-blue-500/20 shadow-xl relative overflow-hidden group">
      <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:scale-125 transition-all"></div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Recaudado CUP</span>
        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
          <i class="fa-solid fa-coins text-lg"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-black text-white tracking-tight">${{ number_format($summary['total_cup'], 2) }}</div>
        <p class="text-xs text-slate-400 mt-1">Total acumulado en moneda nacional</p>
      </div>
    </div>

    <!-- Total Consolidado Eq. USD -->
    <div class="bg-gradient-to-br from-indigo-950/40 via-slate-900 to-slate-900 p-6 rounded-3xl border border-indigo-500/20 shadow-xl relative overflow-hidden group">
      <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl group-hover:scale-125 transition-all"></div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Total Eq. USD</span>
        <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
          <i class="fa-solid fa-scale-balanced text-lg"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-black text-white tracking-tight">${{ number_format($summary['total_equivalent_usd'], 2) }}</div>
        <p class="text-xs text-slate-400 mt-1">{{ $summary['total_transactions'] }} transacciones procesadas</p>
      </div>
    </div>

    <!-- Turnos con Discrepancia / Descuadre -->
    <div class="bg-gradient-to-br from-rose-950/40 via-slate-900 to-slate-900 p-6 rounded-3xl border {{ $summary['shifts_with_discrepancy'] > 0 ? 'border-rose-500/30' : 'border-slate-800' }} shadow-xl relative overflow-hidden group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider {{ $summary['shifts_with_discrepancy'] > 0 ? 'text-rose-400' : 'text-slate-400' }}">Descuadres en Arqueo</span>
        <div class="w-10 h-10 rounded-2xl {{ $summary['shifts_with_discrepancy'] > 0 ? 'bg-rose-500/10 border border-rose-500/20 text-rose-400' : 'bg-slate-800 text-slate-400' }} flex items-center justify-center">
          <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-black {{ $summary['shifts_with_discrepancy'] > 0 ? 'text-rose-400' : 'text-white' }} tracking-tight">
          {{ $summary['shifts_with_discrepancy'] }}
        </div>
        <p class="text-xs text-slate-400 mt-1">
          {{ $summary['open_shifts_count'] }} abiertos / {{ $summary['closed_shifts_count'] }} cerrados
        </p>
      </div>
    </div>

  </div>

  <!-- Recent Shifts & Transactions Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Turnos de Caja Recientes -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-bold text-white tracking-tight">Últimos Turnos y Arqueos</h3>
          <p class="text-xs text-slate-400">Control de apertura y cierre por sucursal regional</p>
        </div>
        <a href="{{ route('audit.shifts.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
          Ver todos →
        </a>
      </div>

      <div class="space-y-3 flex-1">
        @forelse($recentShifts as $shift)
          @php
            $hasDiff = ((float) $shift->difference_usd != 0) || ((float) $shift->difference_cup != 0);
          @endphp
          <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 transition-all flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl {{ $shift->isOpen() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($hasDiff ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-slate-700 text-slate-300') }} flex items-center justify-center font-bold text-xs">
                {{ $shift->isOpen() ? 'ACT' : 'CIE' }}
              </div>
              <div>
                <p class="text-sm font-bold text-white">{{ $shift->cashRegister?->name ?? 'Caja' }}</p>
                <p class="text-xs text-slate-400">{{ $shift->cashRegister?->regionalWarehouse?->name ?? 'Sucursal' }} • {{ $shift->cashier?->name }}</p>
              </div>
            </div>

            <div class="text-right">
              <div class="text-xs font-bold text-white">
                ${{ number_format((float) $shift->total_collected_usd, 2) }} USD / ${{ number_format((float) $shift->total_collected_cup, 2) }} CUP
              </div>
              <div class="flex items-center justify-end gap-2 mt-1">
                @if($hasDiff)
                  <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                    Descuadre: ${{ number_format((float) $shift->difference_usd, 2) }}
                  </span>
                @else
                  <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    Cuadrado
                  </span>
                @endif
                <a href="{{ route('audit.shifts.show', $shift) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold ml-2">
                  Detalle
                </a>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-slate-500 py-8 text-sm">No hay turnos registrados aún.</p>
        @endforelse
      </div>
    </div>

    <!-- Pagos Recientes -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-bold text-white tracking-tight">Últimos Pagos Registrados</h3>
          <p class="text-xs text-slate-400">Liquidaciones de aranceles y fletes en ventanilla</p>
        </div>
        <a href="{{ route('audit.payments.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
          Ver todos →
        </a>
      </div>

      <div class="space-y-3 flex-1">
        @forelse($recentPayments as $payment)
          <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 transition-all flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">
                <i class="fa-solid fa-receipt"></i>
              </div>
              <div>
                <p class="text-sm font-bold text-white">{{ $payment->receipt_number }} • {{ $payment->client_name }}</p>
                <p class="text-xs text-slate-400">PIN: <code class="text-indigo-300">{{ $payment->equipment?->tracking_pin ?? 'N/A' }}</code> • {{ strtoupper($payment->payment_method) }}</p>
              </div>
            </div>

            <div class="text-right">
              <span class="text-sm font-black text-emerald-400">
                +${{ number_format((float) $payment->equivalent_total_usd, 2) }} USD
              </span>
              <p class="text-[11px] text-slate-400">
                {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y H:i') }}
              </p>
            </div>
          </div>
        @empty
          <p class="text-center text-slate-500 py-8 text-sm">No hay pagos registrados aún.</p>
        @endforelse
      </div>
    </div>

  </div>

</div>
@endsection
