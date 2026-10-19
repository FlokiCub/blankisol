@extends('layouts.app', ['title' => 'Gestión Comercial y Gestores'])

@section('page_title', 'Torre Financiera — Red Comercial & Conciliación de Gestores')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-amber-500/10 text-amber-400 border-amber-500/30">
          <i class="fa-solid fa-users-gear mr-1.5"></i>Hoja GESTORES
        </span>
        <span class="text-xs font-mono text-slate-400">21 Gestores Comerciales en 3 Grupos</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Red Comercial, Comisiones & Conciliación</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Administración de gestores comerciales, cálculo de retribución mixta (fijo mensual + comisión por ingreso o por kg), liquidación mensual y cuadre contra comisiones reales de contenedores.
      </p>
    </div>

    <!-- Filtro de Período y Acciones -->
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('profitability.management.commercial') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:ring-2 focus:ring-amber-500">
        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs border border-slate-700">
          <i class="fa-solid fa-filter"></i>
        </button>
      </form>

      <form action="{{ route('profitability.management.commercial.liquidate') }}" method="POST">
        @csrf
        <input type="hidden" name="year_month" value="{{ $selectedMonth }}">
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
          <i class="fa-solid fa-file-invoice-dollar"></i>
          <span>Liquidar Mes {{ $selectedMonth }}</span>
        </button>
      </form>

      <a href="{{ route('profitability.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 font-bold text-xs">
        <i class="fa-solid fa-arrow-left mr-1.5"></i>Volver
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-600/40 text-emerald-300 text-xs sm:text-sm flex items-center gap-3">
      <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- Tarjetas de Resumen & Conciliación Comercial -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Total Fijos Mensuales Gestores</p>
      <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($totalFixed, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">21 gestores en catálogo</p>
    </div>

    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Total Liquidado (Gestores)</p>
      <p class="text-2xl font-black text-amber-400 font-mono mt-2">${{ number_format($totalLiquidated, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Fijo + Comisiones devengadas</p>
    </div>

    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Comisión Operativa (Contenedores)</p>
      <p class="text-2xl font-black text-cyan-400 font-mono mt-2">${{ number_format($totalOperations, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Asignada en análisis económicos</p>
    </div>

    <!-- Conciliación Estado Card -->
    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 border {{ $reconciliation && $reconciliation->status === 'cuadrado' ? 'border-emerald-600/40' : 'border-amber-600/40' }} flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-300">Conciliación Período</span>
        @if($reconciliation)
          <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase border {{ $reconciliation->status === 'cuadrado' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
            {{ $reconciliation->status }}
          </span>
        @else
          <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-400 border border-slate-700">Pendiente</span>
        @endif
      </div>
      <div class="mt-2">
        <p class="text-2xl font-black font-mono {{ $reconciliation && abs($reconciliation->difference) < 0.05 ? 'text-emerald-400' : 'text-amber-400' }}">
          ${{ $reconciliation ? number_format($reconciliation->difference, 2) : '0.00' }}
        </p>
        <p class="text-[11px] text-slate-500 mt-0.5">Diferencia Declarado vs Operativo</p>
      </div>
    </div>
  </div>

  <!-- Tabla de 21 Gestores Comerciales y Liquidación del Mes -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-address-book text-amber-400"></i>
          <span>Padrón de Gestores y Estado de Liquidación (Mes {{ $selectedMonth }})</span>
        </h4>
        <p class="text-xs text-slate-400 mt-0.5">Configuración por gestor, modalidad de comisión y retribución calculada en el período.</p>
      </div>

      <form action="{{ route('profitability.management.commercial.reconcile') }}" method="POST">
        @csrf
        <input type="hidden" name="year_month" value="{{ $selectedMonth }}">
        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs border border-amber-600/30 shadow-md active:scale-95 transition-all flex items-center gap-1.5">
          <i class="fa-solid fa-scale-balanced"></i>
          <span>Re-ejecutar Conciliación</span>
        </button>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">#</th>
            <th class="py-3 px-4">Nombre de Gestor</th>
            <th class="py-3 px-4">Grupo</th>
            <th class="py-3 px-4">Territorio / Región</th>
            <th class="py-3 px-4 text-center">Modalidad Comisión</th>
            <th class="py-3 px-4 text-right">Fijo ($)</th>
            <th class="py-3 px-4 text-right">Comisión Mes ($)</th>
            <th class="py-3 px-4 text-right">Total a Pagar ($)</th>
            <th class="py-3 px-4 text-center">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
          @foreach($agents as $agent)
            @php
              $liq = $agent->liquidations->first();
            @endphp
            <tr class="hover:bg-slate-800/30 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-white">{{ $agent->agent_number }}</td>
              <td class="py-3 px-4 font-bold text-slate-200">{{ $agent->name }}</td>
              <td class="py-3 px-4 text-slate-400">{{ $agent->group }}</td>
              <td class="py-3 px-4">
                <span class="text-slate-300">{{ $agent->territory }}</span>
                <span class="text-[10px] text-slate-500 font-mono block">{{ $agent->region }}</span>
              </td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $agent->commission_mode === '% DEL INGRESO' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                  {{ $agent->commission_mode }}
                </span>
              </td>
              <td class="py-3 px-4 text-right font-mono text-slate-400">${{ number_format($agent->monthly_fixed_fee, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-amber-400">
                ${{ $liq ? number_format($liq->commission_amount, 2) : '0.00' }}
              </td>
              <td class="py-3 px-4 text-right font-mono font-bold text-white">
                ${{ $liq ? number_format($liq->total_payable, 2) : number_format($agent->monthly_fixed_fee, 2) }}
              </td>
              <td class="py-3 px-4 text-center">
                @if($liq && $liq->status === 'liquidado')
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Liquidado</span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">Pendiente</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
