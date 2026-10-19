@extends('layouts.app', ['title' => 'Gestión de Gastos Fijos'])

@section('page_title', 'Torre Financiera — Gestión de Gastos Fijos')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
          <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i>Hoja COSTOS_FIJOS
        </span>
        <span class="text-xs font-mono text-slate-400">Centros: GENERAL, OCC, CEN, ORI</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Catálogo de Costos Fijos y Registro Mensual</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Administración de los ~30 conceptos de gastos fijos del modelo económico, bases salariales y cálculo dinámico de tributos (14% Seguridad Social y 5% Impuesto por Fuerza de Trabajo).
      </p>
    </div>

    <!-- Selector de Período y Volver -->
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('profitability.management.fixed-costs') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:ring-2 focus:ring-cyan-500">
        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold text-xs border border-slate-700">
          <i class="fa-solid fa-filter"></i>
        </button>
      </form>

      <a href="{{ route('profitability.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 font-bold text-xs">
        <i class="fa-solid fa-arrow-left mr-1.5"></i>Volver
      </a>
    </div>
  </div>

  @if($isClosed)
    <div class="p-4 rounded-2xl bg-amber-950/30 border border-amber-600/40 text-amber-200 flex items-center gap-3 text-xs sm:text-sm">
      <i class="fa-solid fa-lock text-amber-400 text-lg"></i>
      <span><strong>Período Cerrado Oficialmente:</strong> El mes {{ $selectedMonth }} está bloqueado. Para modificar importes reales o recalcular conceptos, solicite la reapertura autorizada del período.</span>
    </div>
  @endif

  <!-- Resumen de Costos Fijos Calculados para el Período -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">General Central</p>
      <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($monthlyCalc['general_fixed_usd'], 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Sueldos, alquiler, energía, depreciación</p>
    </div>
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Centro Occidente</p>
      <p class="text-2xl font-black text-cyan-400 font-mono mt-2">${{ number_format($monthlyCalc['occidente_fixed_usd'], 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Chofer y gastos occidente</p>
    </div>
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Centro Centro</p>
      <p class="text-2xl font-black text-blue-400 font-mono mt-2">${{ number_format($monthlyCalc['centro_fixed_usd'], 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Chofer y centro transitorio</p>
    </div>
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Centro Oriente</p>
      <p class="text-2xl font-black text-purple-400 font-mono mt-2">${{ number_format($monthlyCalc['oriente_fixed_usd'], 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Chofer y centro transitorio</p>
    </div>
    <div class="p-5 rounded-2xl bg-gradient-to-br from-cyan-950/40 to-slate-900 border border-cyan-800/40">
      <p class="text-xs text-cyan-300 font-semibold">Total Gastos Fijos Mes</p>
      <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($monthlyCalc['total_fixed_usd'], 2) }}</p>
      <p class="text-[11px] text-cyan-400/80 mt-0.5">Incluye $190.40 SS + $68.00 FT</p>
    </div>
  </div>

  <!-- Formulario de Registro Mensual de Importes Reales -->
  <form action="{{ route('profitability.management.fixed-costs.monthly.save') }}" method="POST">
    @csrf
    <input type="hidden" name="year_month" value="{{ $selectedMonth }}">

    <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
      <div class="p-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-list-check text-cyan-400"></i>
            <span>Conceptos de Gastos Fijos — Período {{ $selectedMonth }}</span>
          </h4>
          <p class="text-xs text-slate-400 mt-0.5">Modifique los importes reales devengados para el mes. Si no se introduce importe real, se aplica el valor base estándar.</p>
        </div>

        @if(!$isClosed)
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Guardar Importes Reales & Sincronizar</span>
          </button>
        @endif
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-4">Código</th>
              <th class="py-3 px-4">Concepto / Nombre</th>
              <th class="py-3 px-4 text-center">Centro Coste</th>
              <th class="py-3 px-4 text-center">Tipo</th>
              <th class="py-3 px-4 text-right">Base Teórica ($)</th>
              <th class="py-3 px-4 text-right w-44">Importe Real Mes ($)</th>
              <th class="py-3 px-4">Observaciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
            @foreach($items as $it)
              @php
                $entry = $it->monthlyEntries->first();
                $actual = $entry ? $entry->actual_amount : '';
              @endphp
              <tr class="hover:bg-slate-800/30 transition-colors {{ $it->is_salary ? 'bg-cyan-950/10' : '' }}">
                <td class="py-3 px-4 font-mono font-bold text-white">{{ $it->code }}</td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-200">{{ $it->name }}</span>
                  <span class="text-[10px] text-slate-500 block">{{ $it->classification }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold border {{ $it->cost_center === 'GENERAL' ? 'bg-slate-800 text-slate-300 border-slate-700' : ($it->cost_center === 'OCC' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : ($it->cost_center === 'CEN' ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : 'bg-purple-500/10 text-purple-400 border-purple-500/30')) }}">
                    {{ $it->cost_center }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  @if($it->is_salary)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                      Salarial (+19% Trib)
                    </span>
                  @else
                    <span class="text-slate-500 text-[11px]">Gasto directo</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-right font-mono text-slate-400">
                  ${{ number_format($it->monthly_base_value, 2) }}
                </td>
                <td class="py-3 px-4 text-right">
                  <input 
                    type="number" 
                    step="0.01" 
                    min="0" 
                    name="entries[{{ $it->id }}][amount]" 
                    value="{{ $actual }}" 
                    placeholder="{{ number_format($it->monthly_base_value, 2) }}" 
                    {{ $isClosed ? 'disabled' : '' }}
                    class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono text-right text-xs focus:ring-1 focus:ring-cyan-500"
                  >
                </td>
                <td class="py-3 px-4">
                  <input 
                    type="text" 
                    name="entries[{{ $it->id }}][observations]" 
                    value="{{ $entry?->observations }}" 
                    placeholder="Nota / Justificación..." 
                    {{ $isClosed ? 'disabled' : '' }}
                    class="w-full px-3 py-1.5 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-300 text-[11px] focus:ring-1 focus:ring-cyan-500"
                  >
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </form>

</div>
@endsection
