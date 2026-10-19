@extends('layouts.app', ['title' => 'Resumen Económico Mensual (RESUMEN_MES)'])

@section('page_title', 'Torre Financiera — Consolidado Mensual & Comparativa Intermensual')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
          <i class="fa-solid fa-calendar-days mr-1.5"></i>Hoja RESUMEN_MES
        </span>
        <span class="text-xs font-mono text-slate-400">Consolidado Histórico & Variación MoM</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Resumen Económico Mensual Consolidado</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Visión agregada mes a mes de facturación flete, absorción de costos fijos, rentabilidad neta auditada y tasa de crecimiento intermensual (MoM).
      </p>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('profitability.index') }}" class="px-5 py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-200 hover:text-white font-bold text-xs shadow-lg active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Volver al Dashboard</span>
      </a>

      <a href="{{ route('profitability.management.closings') }}" class="px-5 py-3 rounded-2xl bg-rose-950/40 border border-rose-600/40 hover:border-rose-500 text-rose-300 hover:text-white font-bold text-xs shadow-lg active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-lock"></i>
        <span>Gestión de Cierres</span>
      </a>
    </div>
  </div>

  <!-- KPI Cards Grid — Totales Acumulados (YTD) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Carga YTD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Carga Total Acumulada</span>
        <i class="fa-solid fa-weight-hanging text-cyan-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-white font-mono">{{ number_format($ytd['kg_total'], 0) }} <span class="text-xs text-slate-400 font-sans">kg</span></p>
        <p class="text-[11px] text-slate-500 mt-0.5">En {{ $ytd['containers_count'] }} contenedores arribados</p>
      </div>
    </div>

    <!-- Facturación YTD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Facturación Bruta (YTD)</span>
        <i class="fa-solid fa-file-invoice-dollar text-blue-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-white font-mono">${{ number_format($ytd['revenue_total'], 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Ingresos totales de flete</p>
      </div>
    </div>

    <!-- Margen de Contribución YTD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Margen Contribución Total</span>
        <i class="fa-solid fa-chart-line text-emerald-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-emerald-400 font-mono">${{ number_format($ytd['contribution_margin'], 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Ratio global: <strong class="text-white">{{ $ytd['contribution_margin_ratio'] }}%</strong></p>
      </div>
    </div>

    <!-- Fijos Asignados YTD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Gastos Fijos Asignados</span>
        <i class="fa-solid fa-building text-amber-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-amber-300 font-mono">${{ number_format($ytd['fixed_costs_assigned'], 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Imputados a fletes</p>
      </div>
    </div>

    <!-- Utilidad Neta YTD -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Utilidad Neta Consolidada</span>
        <i class="fa-solid fa-sack-dollar text-purple-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-purple-300 font-mono">${{ number_format($ytd['net_profit'], 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Margen neto: <strong class="text-white">{{ $ytd['net_margin_pct'] }}%</strong></p>
      </div>
    </div>
  </div>

  <!-- Tabla Histórica Mes a Mes -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-table-list text-cyan-400"></i>
          <span>Desglose Histórico Mensual y Variación Intermensual</span>
        </h4>
        <p class="text-xs text-slate-400 mt-0.5">Consolidación oficial de operaciones por período mensual.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-950/60 text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">
            <th class="py-4 px-4">Mes</th>
            <th class="py-4 px-3 text-center">Cont.</th>
            <th class="py-4 px-4 text-right">Carga (kg)</th>
            <th class="py-4 px-4 text-right">Facturación ($)</th>
            <th class="py-4 px-4 text-right">Costos Var. ($)</th>
            <th class="py-4 px-4 text-right">Margen (MC) ($)</th>
            <th class="py-4 px-4 text-right">Fijos Asig. ($)</th>
            <th class="py-4 px-4 text-right">Fijos No Abs. ($)</th>
            <th class="py-4 px-4 text-right">Utilidad Neta ($)</th>
            <th class="py-4 px-3 text-center">Margen %</th>
            <th class="py-4 px-4 text-center">Cierre Contable</th>
            <th class="py-4 px-4 text-center">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
          @forelse($monthsData as $mKey => $row)
            <tr class="hover:bg-slate-800/40 transition-colors">
              <!-- Mes -->
              <td class="py-4 px-4 font-mono font-bold text-white">
                <span class="text-sm">{{ $mKey }}</span>
              </td>

              <!-- Cant Contenedores -->
              <td class="py-4 px-3 text-center font-mono font-bold text-cyan-400">
                {{ $row['containers_count'] }}
              </td>

              <!-- Carga Total & MoM -->
              <td class="py-4 px-4 text-right font-mono">
                <span class="text-white font-bold">{{ number_format($row['kg_total'], 0) }}</span>
                @if($row['mom_kg'] !== null)
                  <span class="block text-[10px] {{ $row['mom_kg'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $row['mom_kg'] >= 0 ? '+' : '' }}{{ $row['mom_kg'] }}% MoM
                  </span>
                @endif
              </td>

              <!-- Facturación & MoM -->
              <td class="py-4 px-4 text-right font-mono">
                <span class="text-white font-bold">${{ number_format($row['revenue_total'], 2) }}</span>
                @if($row['mom_revenue'] !== null)
                  <span class="block text-[10px] {{ $row['mom_revenue'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $row['mom_revenue'] >= 0 ? '+' : '' }}{{ $row['mom_revenue'] }}%
                  </span>
                @endif
              </td>

              <!-- Costes Variables -->
              <td class="py-4 px-4 text-right font-mono text-slate-400">
                ${{ number_format($row['cost_variable_total'], 2) }}
              </td>

              <!-- Margen de Contribución -->
              <td class="py-4 px-4 text-right font-mono">
                <strong class="text-emerald-400">${{ number_format($row['contribution_margin'], 2) }}</strong>
                <span class="block text-[10px] text-slate-500">{{ $row['contribution_margin_ratio'] }}%</span>
              </td>

              <!-- Fijos Asignados -->
              <td class="py-4 px-4 text-right font-mono text-slate-300">
                ${{ number_format($row['fixed_costs_assigned'], 2) }}
              </td>

              <!-- Fijos No Absorbidos -->
              <td class="py-4 px-4 text-right font-mono {{ $row['unabsorbed_fixed'] > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                ${{ number_format($row['unabsorbed_fixed'], 2) }}
              </td>

              <!-- Utilidad Neta & MoM -->
              <td class="py-4 px-4 text-right font-mono">
                <strong class="{{ $row['net_profit'] >= 0 ? 'text-purple-300' : 'text-rose-400' }}">
                  ${{ number_format($row['net_profit'], 2) }}
                </strong>
                @if($row['mom_profit'] !== null)
                  <span class="block text-[10px] {{ $row['mom_profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $row['mom_profit'] >= 0 ? '+' : '' }}{{ $row['mom_profit'] }}%
                  </span>
                @endif
              </td>

              <!-- Margen Neto % -->
              <td class="py-4 px-3 text-center font-mono">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $row['net_margin_pct'] >= 15 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($row['net_margin_pct'] >= 0 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30') }}">
                  {{ $row['net_margin_pct'] }}%
                </span>
              </td>

              <!-- Estado Cierre -->
              <td class="py-4 px-4 text-center">
                @if($row['is_closed'])
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono bg-rose-500/10 text-rose-400 border border-rose-500/30 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-lock text-[9px]"></i> Cerrado
                  </span>
                @elseif($row['closing_status'] === 'reabierto')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-lock-open text-[9px]"></i> Reabierto
                  </span>
                @else
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono bg-blue-500/10 text-blue-400 border border-blue-500/30 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-door-open text-[9px]"></i> Abierto
                  </span>
                @endif
              </td>

              <!-- Acción -->
              <td class="py-4 px-4 text-center">
                <a href="{{ route('profitability.index', ['period' => $mKey]) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-cyan-400 hover:text-white font-bold text-xs transition-colors inline-flex items-center gap-1">
                  <i class="fa-solid fa-filter text-[10px]"></i> Ver
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="py-12 text-center text-slate-500">
                No hay períodos económicos con datos registrados aún.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
