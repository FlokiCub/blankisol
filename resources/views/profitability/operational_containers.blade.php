@extends('layouts.app', ['title' => 'Contenedores Operativos para Análisis Económico'])

@section('page_title', 'Torre Financiera — Contenedores Operativos Disponibles')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-emerald-500/10 text-emerald-400 border-emerald-500/30">
          <i class="fa-solid fa-boxes-stacked mr-1.5"></i>Integración Operativa Real
        </span>
        <span class="text-xs font-mono text-slate-400">Agrupación por solve_cargo_tracking_id</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Contenedores del Módulo Operativo</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Listado de contenedores reales registrados en el sistema de paquetería. Seleccione cualquier contenedor para auto-recuperar bultos, rutas, transitaria y generar el <strong>precálculo económico de Etapa 1</strong> sin reintroducir datos.
      </p>
    </div>

    <!-- Botones de Navegación -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('profitability.index') }}" class="px-5 py-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-200 hover:text-white font-bold text-xs shadow-lg active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-arrow-left text-slate-400"></i>
        <span>Volver a Análisis</span>
      </a>

      <a href="{{ route('profitability.create') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-xl shadow-cyan-950/50 active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-calculator text-sm"></i>
        <span>Análisis Manual / Proyección</span>
      </a>
    </div>
  </div>

  <!-- Alerta Informativa de Ciclo Económico -->
  <div class="p-4 rounded-2xl bg-cyan-950/20 border border-cyan-800/40 text-cyan-200 flex items-center gap-3 text-xs sm:text-sm">
    <i class="fa-solid fa-circle-info text-cyan-400 text-base"></i>
    <span>
      <strong>Ciclo Económico Independiente:</strong> Al vincular un contenedor, se crea un análisis en estado <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono font-bold">abierto</span>. Los datos operativos no se alteran y el análisis económico puede enriquecerse, simularse o cerrarse de forma autónoma.
    </span>
  </div>

  <!-- Tabla de Contenedores Operativos -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
      <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-container-storage text-cyan-400"></i>
        <span>Contenedores Registrados ({{ $containers->count() }})</span>
      </h4>
    </div>

    @if($containers->isEmpty())
      <div class="p-12 text-center text-slate-500">
        <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-600"></i>
        <p class="text-base font-bold text-slate-400">No hay contenedores registrados en el módulo operativo</p>
        <p class="text-xs text-slate-500 mt-1">Los contenedores aparecerán automáticamente al escanear bultos o importar manifiestos de carga.</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-900/40 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-4 px-6">Identificador Contenedor / B/L</th>
              <th class="py-4 px-6 text-center">Bultos</th>
              <th class="py-4 px-6">Fecha Arribo</th>
              <th class="py-4 px-6 text-center">Transitaria</th>
              <th class="py-4 px-6 text-center">Estado Económico</th>
              <th class="py-4 px-6 text-right">Resultado Precalculado</th>
              <th class="py-4 px-6 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
            @foreach($containers as $c)
              <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-4 px-6">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700/60 flex items-center justify-center text-cyan-400 font-bold">
                      <i class="fa-solid fa-pallet"></i>
                    </div>
                    <div>
                      <span class="font-mono font-bold text-white text-sm tracking-wide">{{ $c['container_id'] }}</span>
                      <p class="text-[10px] text-slate-500">Solve Cargo B/L</p>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-6 text-center">
                  <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-slate-800 text-slate-200 border border-slate-700">
                    {{ $c['total_equipments'] }}
                  </span>
                </td>
                <td class="py-4 px-6 font-mono text-slate-300">
                  <i class="fa-regular fa-calendar text-slate-500 mr-1.5"></i>{{ $c['arrival_date'] }}
                </td>
                <td class="py-4 px-6 text-center">
                  <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $c['transitaria'] === 'ENCI' ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : 'bg-purple-500/10 text-purple-400 border-purple-500/30' }}">
                    {{ $c['transitaria'] }}
                  </span>
                </td>
                <td class="py-4 px-6 text-center">
                  @if($c['has_analysis'])
                    @if($c['economic_status'] === 'cerrado')
                      <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-emerald-500/10 text-emerald-400 border-emerald-500/30">
                        <i class="fa-solid fa-lock mr-1"></i>Cerrado
                      </span>
                    @elseif($c['economic_status'] === 'en_revision')
                      <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
                        <i class="fa-solid fa-arrows-rotate mr-1"></i>En Revisión
                      </span>
                    @else
                      <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-amber-500/10 text-amber-400 border-amber-500/30">
                        <i class="fa-solid fa-folder-open mr-1"></i>Abierto
                      </span>
                    @endif
                  @else
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-slate-800 text-slate-400 border-slate-700">
                      Sin Análisis
                    </span>
                  @endif
                </td>
                <td class="py-4 px-6 text-right font-mono">
                  @if($c['has_analysis'])
                    <span class="text-sm font-bold {{ $c['net_profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                      ${{ number_format($c['net_profit'], 2) }}
                    </span>
                    <span class="text-[10px] text-slate-500 block">({{ $c['net_margin_pct'] }}% margen)</span>
                  @else
                    <span class="text-slate-500 italic">Pendiente de vinculación</span>
                  @endif
                </td>
                <td class="py-4 px-6 text-center">
                  @if($c['has_analysis'])
                    <a href="{{ route('profitability.show', $c['analysis_id']) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition-all inline-flex items-center gap-1.5 shadow">
                      <i class="fa-solid fa-eye text-cyan-400"></i>
                      <span>Ver Ficha</span>
                    </a>
                  @else
                    <form action="{{ route('profitability.link-operational', $c['container_id']) }}" method="POST" class="inline">
                      @csrf
                      <button type="submit" class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-md shadow-emerald-950/40 active:scale-95 transition-all inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-link text-xs"></i>
                        <span>Vincular & Precalcular</span>
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

</div>
@endsection
