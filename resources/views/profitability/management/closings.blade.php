@extends('layouts.app', ['title' => 'Cierres Mensuales y Bloqueo Contable'])

@section('page_title', 'Torre Financiera — Cierres Económicos & Checklist Oficial')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-rose-500/10 text-rose-400 border-rose-500/30">
          <i class="fa-solid fa-lock mr-1.5"></i>Hoja CIERRES_MENSUALES
        </span>
        <span class="text-xs font-mono text-slate-400">Auditoría & Checklist de 12 Pasos</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Control de Cierres Mensuales y Blindaje Contable</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Protocolo oficial de verificación en 12 puntos antes de declarar el cierre contable mensual. Bloqueo de modificaciones sobre costos fijos, análisis y comisiones con registro estricto en bitácora de auditoría.
      </p>
    </div>

    <!-- Filtro de Período y Volver -->
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('profitability.management.closings') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:ring-2 focus:ring-rose-500">
        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-rose-400 font-bold text-xs border border-slate-700">
          <i class="fa-solid fa-filter"></i>
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

  @if(session('warning'))
    <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-600/40 text-amber-300 text-xs sm:text-sm flex items-center gap-3">
      <i class="fa-solid fa-triangle-exclamation text-amber-400 text-base"></i>
      <span>{{ session('warning') }}</span>
    </div>
  @endif

  <!-- Estado del Período Actual -->
  <div class="p-6 rounded-3xl {{ $closing && $closing->isClosed() ? 'bg-gradient-to-r from-slate-900 via-rose-950/20 to-slate-900 border-rose-800/40' : 'bg-gradient-to-r from-slate-900 via-blue-950/20 to-slate-900 border-blue-800/40' }} border shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-1">
      <div class="flex items-center gap-3">
        @if($closing && $closing->isClosed())
          <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center gap-1.5">
            <i class="fa-solid fa-lock"></i> Período Cerrado & Bloqueado
          </span>
        @elseif($closing && $closing->status === 'reabierto')
          <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center gap-1.5">
            <i class="fa-solid fa-lock-open"></i> Período Reabierto Autorizado
          </span>
        @else
          <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-blue-500/20 text-blue-400 border border-blue-500/40 flex items-center gap-1.5">
            <i class="fa-solid fa-door-open"></i> Período Abierto en Curso
          </span>
        @endif
        <h4 class="text-xl font-black text-white font-mono">Mes {{ $selectedMonth }}</h4>
      </div>
      <p class="text-xs text-slate-400">
        @if($closing && $closing->isClosed())
          Cerrado por <strong class="text-white">{{ $closing->closer?->name ?? 'Jefe Económico' }}</strong> el {{ $closing->closed_at?->format('d/m/Y H:i') }}.
        @elseif($closing && $closing->status === 'reabierto')
          Reabierto por <strong class="text-white">{{ $closing->reopener?->name ?? 'Dirección' }}</strong>. Motivo: <em class="text-slate-300">{{ $closing->reopen_reason }}</em>
        @else
          El período contable se encuentra en fase de operaciones y precálculo de contenedores.
        @endif
      </p>
    </div>

    <!-- Botones según estado -->
    <div>
      @if($closing && $closing->isClosed())
        <button type="button" onclick="document.getElementById('reopenModal').classList.remove('hidden')" class="px-5 py-3 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
          <i class="fa-solid fa-key"></i>
          <span>Solicitar Reapertura Autorizada</span>
        </button>
      @else
        <form action="{{ route('profitability.management.closings.execute') }}" method="POST" onsubmit="return confirm('¿Confirma el cierre contable del mes {{ $selectedMonth }}? Se bloquearán las modificaciones sobre gastos y análisis.');">
          @csrf
          <input type="hidden" name="year_month" value="{{ $selectedMonth }}">
          <button type="submit" class="px-6 py-3 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
            <i class="fa-solid fa-lock"></i>
            <span>Ejecutar Cierre Contable</span>
          </button>
        </form>
      @endif
    </div>
  </div>

  <!-- Resumen de Costos No Absorbidos en el Mes -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Total Gastos Fijos Mes</p>
      <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($unabsorbed['total_month_fixed'], 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Central + 3 bases regionales</p>
    </div>

    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Costos Absorbidos por Contenedores</p>
      <p class="text-2xl font-black text-cyan-400 font-mono mt-2">${{ number_format($unabsorbed['total_assigned_fixed'] ?? 0.0, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">En {{ $containersInMonth->count() }} contenedores arribados</p>
    </div>

    <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 border {{ ($unabsorbed['unabsorbed_fixed'] ?? 0.0) > 0 ? 'border-amber-600/40' : 'border-emerald-600/40' }}">
      <p class="text-xs text-slate-400 font-semibold">Costos Fijos NO Absorbidos</p>
      <p class="text-2xl font-black font-mono mt-2 {{ ($unabsorbed['unabsorbed_fixed'] ?? 0.0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
        ${{ number_format($unabsorbed['unabsorbed_fixed'] ?? 0.0, 2) }}
      </p>
      <p class="text-[11px] text-slate-500 mt-0.5">Carga no cubierta a imputar a resultados</p>
    </div>
  </div>

  <!-- Checklist de 12 Verificaciones -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
      <div>
        <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-clipboard-check text-rose-400"></i>
          <span>Checklist Oficial de Cierre Mensual (12 Verificaciones)</span>
        </h4>
        <p class="text-xs text-slate-400 mt-0.5">Requisitos previos auditados para el mes {{ $selectedMonth }}.</p>
      </div>
    </div>

    <div class="divide-y divide-slate-800/60 text-xs">
      @php
        $itemsDesc = [
          '1_all_containers_recorded' => ['Contenedores Registrados', 'Todos los arribos del mes tienen su análisis económico abierto o asociado.', 'fa-boxes-stacked'],
          '2_all_containers_economically_closed' => ['Contenedores con Cierre Económico', 'No existen contenedores con estado económico pendiente de liquidar o en revisión.', 'fa-box-archive'],
          '3_fixed_costs_entered' => ['Costos Fijos Registrados', 'Se han cargado los importes reales o bases teóricas de los ~30 conceptos de gastos.', 'fa-file-invoice-dollar'],
          '4_depreciation_calculated' => ['Asiento de Depreciación Generado', 'La amortización lineal de activos del mes está calculada e impactando el concepto CF10.', 'fa-truck-ramp-box'],
          '5_commercial_liquidations_run' => ['Liquidación de Gestores Ejecutada', 'Los 21 gestores comerciales tienen su cálculo de fijos y comisiones procesado.', 'fa-users-gear'],
          '6_commercial_reconciliation_balanced' => ['Conciliación Comercial Cuadrada', 'La diferencia entre comisiones declaradas y aplicadas a contenedores es menor a $0.05.', 'fa-scale-balanced'],
          '7_unabsorbed_costs_computed' => ['Costos Fijos No Absorbidos Determinados', 'Cálculo de la brecha entre fijos devengados y fijos imputados a fletes finalizado.', 'fa-calculator'],
          '8_fuel_telemetry_audited' => ['Auditoría de Combustible vs Norma', 'Los consumos reales de diésel fueron contrastados contra las normas técnicas por ruta.', 'fa-gas-pump'],
          '9_no_pending_economic_alerts' => ['Sin Alertas Críticas Pendientes', 'Todas las desviaciones de consumo o margen deficitario han sido justificadas.', 'fa-triangle-exclamation'],
          '10_taxes_sales_services_provisioned' => ['Provisión Tributaria 10% + 1%', 'Tributos de la Ley 113 calculados sobre el ingreso bruto por servicios de carga.', 'fa-landmark'],
          '11_taxes_profit_estimated' => ['Estimación de Impuesto sobre Utilidades (35%)', 'Provisión fiscal del 35% calculada sobre la utilidad antes de impuestos.', 'fa-coins'],
          '12_executive_reports_reconciled' => ['Emisión de Informes de Dirección', 'Estados financieros y fichas de rentabilidad listos para archivo definitivo.', 'fa-file-shield'],
        ];
      @endphp

      @foreach($checklistStatus as $k => $ok)
        @php $info = $itemsDesc[$k] ?? [$k, '', 'fa-circle-check']; @endphp
        <div class="p-4 flex items-center justify-between hover:bg-slate-800/30 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl {{ $ok ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500 border border-slate-700' }} flex items-center justify-center">
              <i class="fa-solid {{ $info[2] }}"></i>
            </div>
            <div>
              <p class="font-bold text-white">{{ $info[0] }}</p>
              <p class="text-[11px] text-slate-400">{{ $info[1] }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            @if($ok)
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                <i class="fa-solid fa-check"></i> Verificado
              </span>
            @else
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700 flex items-center gap-1">
                <i class="fa-solid fa-clock"></i> Pendiente
              </span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Historial de Cierres Mensuales -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80">
      <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-history text-slate-400"></i>
        <span>Historial de Cierres Contables y Reaperturas</span>
      </h4>
    </div>

    <div class="overflow-x-auto text-xs">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Período</th>
            <th class="py-3 px-4 text-center">Estado</th>
            <th class="py-3 px-4">Cerrado Por</th>
            <th class="py-3 px-4">Fecha Cierre</th>
            <th class="py-3 px-4">Reabierto Por</th>
            <th class="py-3 px-4">Motivo Reapertura</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
          @forelse($history as $item)
            <tr class="hover:bg-slate-800/30 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-white">{{ $item->year_month }}</td>
              <td class="py-3 px-4 text-center">
                @if($item->isClosed())
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">Cerrado</span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Reabierto</span>
                @endif
              </td>
              <td class="py-3 px-4">{{ $item->closer?->name ?? '—' }}</td>
              <td class="py-3 px-4 font-mono text-slate-400">{{ $item->closed_at?->format('d/m/Y H:i') ?? '—' }}</td>
              <td class="py-3 px-4">{{ $item->reopener?->name ?? '—' }}</td>
              <td class="py-3 px-4 text-slate-400">{{ $item->reopen_reason ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-slate-500">No se han registrado cierres históricos previos.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Reapertura Autorizada -->
  @if($closing && $closing->isClosed())
  <div id="reopenModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <i class="fa-solid fa-lock-open text-amber-400"></i> Solicitar Reapertura Autorizada
        </h3>
        <button type="button" onclick="document.getElementById('reopenModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <form action="{{ route('profitability.management.closings.reopen', $closing) }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-bold text-slate-300 mb-1">Motivo / Justificación Obligatoria de Auditoría (mín. 10 caracteres)</label>
          <textarea name="reason" required minlength="10" rows="3" placeholder="Ej: Ajuste de factura real de combustible ENCI autorizada por Dirección General..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
          <button type="button" onclick="document.getElementById('reopenModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">Cancelar</button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold shadow-md">Confirmar Reapertura</button>
        </div>
      </form>
    </div>
  </div>
  @endif

</div>
@endsection
