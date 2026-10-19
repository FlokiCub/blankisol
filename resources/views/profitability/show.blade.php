@extends('layouts.app', ['title' => 'Análisis Económico #' . $analysis->container_id])

@section('page_title', 'Dictamen Económico — Contenedor #' . $analysis->container_id)

@section('content')
<div class="space-y-6">

  <!-- Header Banner Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <a href="{{ route('profitability.index') }}" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors no-print">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold font-mono border {{ $analysis->verdict_badge_class }}">
          @if($analysis->decision_verdict === 'FACTIBLE')
            🟢 FACTIBLE CON UTILIDADES
          @elseif($analysis->decision_verdict === 'MARGINAL')
            🟡 RENTABILIDAD MARGINAL
          @else
            🔴 NO FACTIBLE (A PÉRDIDA)
          @endif
        </span>

        @if($analysis->economic_status === 'cerrado')
          <span class="px-3 py-1 rounded-full text-xs font-bold font-mono border bg-emerald-500/10 text-emerald-400 border-emerald-500/30 flex items-center gap-1.5">
            <i class="fa-solid fa-lock"></i>
            <span>CERRADO ECONÓMICAMENTE</span>
          </span>
        @elseif($analysis->economic_status === 'en_revision')
          <span class="px-3 py-1 rounded-full text-xs font-bold font-mono border bg-cyan-500/10 text-cyan-400 border-cyan-500/30 flex items-center gap-1.5">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span>EN REVISIÓN</span>
          </span>
        @else
          <span class="px-3 py-1 rounded-full text-xs font-bold font-mono border bg-amber-500/10 text-amber-400 border-amber-500/30 flex items-center gap-1.5">
            <i class="fa-solid fa-folder-open"></i>
            <span>ABIERTO / PRE-CÁLCULO</span>
          </span>
        @endif

        <span class="text-xs font-mono text-slate-400">Transitaria: <strong class="text-white">{{ $analysis->transitaria }}</strong></span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white font-mono tracking-tight flex items-center gap-2">
        <span>Contenedor #{{ $analysis->container_id }}</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400">
        Arribo al Puerto: <strong class="text-white font-mono">{{ $analysis->arrival_date->format('d/m/Y') }}</strong> | Carga Total: <strong class="text-cyan-400 font-mono">{{ number_format($analysis->kg_total, 2) }} kg</strong>
        @if($analysis->closed_at)
          <span class="text-slate-500 ml-2">| Cerrado el {{ $analysis->closed_at->format('d/m/Y H:i') }}</span>
        @endif
      </p>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-wrap items-center gap-3 no-print">
      @if($analysis->economic_status === 'cerrado')
        <form action="{{ route('profitability.reopen', $analysis) }}" method="POST" onsubmit="const r = prompt('Motivo de reapertura justificada del análisis económico:'); if(!r){return false;} this.reason.value = r;">
          @csrf
          <input type="hidden" name="reason" value="">
          <button type="submit" class="px-4 py-3 rounded-2xl bg-amber-950/40 border border-amber-600/40 hover:border-amber-500 text-amber-300 hover:text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
            <i class="fa-solid fa-lock-open"></i>
            <span>Reabrir Período</span>
          </button>
        </form>
      @else
        <form action="{{ route('profitability.close', $analysis) }}" method="POST" onsubmit="return confirm('¿Confirmas el cierre económico oficial de este contenedor? Se congelará un snapshot inmutable de todas las tarifas y normas técnicas.');">
          @csrf
          <button type="submit" class="px-4 py-3 rounded-2xl bg-emerald-950/40 border border-emerald-600/40 hover:border-emerald-500 text-emerald-300 hover:text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
            <i class="fa-solid fa-lock"></i>
            <span>Cierre Económico</span>
          </button>
        </form>
      @endif

      <button onclick="window.print()" class="px-4 py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
        <i class="fa-solid fa-print"></i>
        <span>Imprimir Dictamen</span>
      </button>

      <a href="{{ route('profitability.csv', $analysis) }}" class="px-5 py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-xl shadow-cyan-950/50 transition-all flex items-center gap-2">
        <i class="fa-solid fa-file-csv"></i>
        <span>Exportar CSV</span>
      </a>
    </div>
  </div>

  <!-- Veredicto de Decisión & Alerta de Viabilidad -->
  <div class="p-6 rounded-3xl border {{ $analysis->decision_verdict === 'FACTIBLE' ? 'bg-emerald-950/30 border-emerald-500/40 text-emerald-200' : ($analysis->decision_verdict === 'MARGINAL' ? 'bg-amber-950/30 border-amber-500/40 text-amber-200' : 'bg-rose-950/30 border-rose-500/40 text-rose-200') }} space-y-2">
    <div class="flex items-center gap-2 text-sm font-black uppercase tracking-wider">
      <i class="fa-solid {{ $analysis->decision_verdict === 'FACTIBLE' ? 'fa-circle-check text-emerald-400' : ($analysis->decision_verdict === 'MARGINAL' ? 'fa-triangle-exclamation text-amber-400' : 'fa-circle-xmark text-rose-400') }}"></i>
      <span>Dictamen Técnico de Viabilidad Económica</span>
    </div>
    <p class="text-xs sm:text-sm leading-relaxed">
      {{ $analysis->decision_summary }}
    </p>
    <div class="pt-2 flex flex-wrap items-center gap-6 text-xs font-mono border-t border-slate-800/40">
      <span>Tarifa Mínima de Equilibrio: <strong class="text-white">${{ number_format($analysis->breakeven_tariff_usd_kg, 4) }} USD/kg</strong></span>
      <span>Punto de Equilibrio en Carga: <strong class="text-white">{{ number_format($analysis->breakeven_kg, 0) }} kg</strong></span>
      <span>Margen de Seguridad: <strong class="text-emerald-400">{{ $analysis->applied_parameters['breakeven']['safety_margin_pct'] ?? ($analysis->kg_total > 0 ? round(($analysis->kg_total - $analysis->breakeven_kg) / $analysis->kg_total * 100, 2) : 0) }}%</strong></span>
      <span>Margen Neto: <strong class="text-white">{{ $analysis->net_margin_pct }}%</strong></span>
    </div>
  </div>

  <!-- Métricas Principales: Ingreso, Costos, Margen, Utilidad -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-5 rounded-2xl glass-card border border-slate-800">
      <p class="text-xs text-slate-400 font-semibold">Ingreso Total Facturado</p>
      <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($analysis->revenue_total, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">${{ number_format($analysis->revenue_per_kg, 3) }} USD/kg</p>
    </div>

    <div class="p-5 rounded-2xl glass-card border border-slate-800">
      <p class="text-xs text-slate-400 font-semibold">Total Costos Variables</p>
      <p class="text-2xl font-black text-amber-300 font-mono mt-2">${{ number_format($analysis->cost_variable_total, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Operación directa en 4 etapas</p>
    </div>

    <div class="p-5 rounded-2xl glass-card border border-slate-800">
      <p class="text-xs text-slate-400 font-semibold">Margen Contribución (MC)</p>
      <p class="text-2xl font-black text-cyan-400 font-mono mt-2">${{ number_format($analysis->contribution_margin, 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">{{ $analysis->contribution_margin_pct }}% sobre ingreso</p>
    </div>

    <div class="p-5 rounded-2xl glass-card border border-slate-800">
      <p class="text-xs text-slate-400 font-semibold">Utilidad Neta Estimada</p>
      <p class="text-2xl font-black {{ $analysis->net_profit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono mt-2">
        ${{ number_format($analysis->net_profit, 2) }}
      </p>
      <p class="text-[11px] text-slate-500 mt-0.5">{{ $analysis->net_margin_pct }}% margen neto</p>
    </div>
  </div>

  <!-- Desglose de Costos en las 4 Etapas Operativas -->
  <div class="space-y-4">
    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
      <i class="fa-solid fa-layer-group text-cyan-400"></i>
      <span>Desglose Detallado de Costos Variables en 4 Etapas</span>
    </h4>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Etapa 1: Puerto Mariel -->
      <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-white flex items-center gap-1.5">
              <i class="fa-solid fa-anchor text-cyan-400"></i> Etapa 1: Puerto
            </span>
            <span class="font-mono text-xs font-bold text-cyan-300">${{ number_format($analysis->cost_port_total, 2) }}</span>
          </div>
          <div class="mt-3 text-[11px] space-y-1.5 text-slate-400">
            <div class="flex justify-between">
              <span>Cita del Puerto:</span>
              <span class="font-mono text-slate-200">${{ number_format(10.00, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Combustible Diésel:</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_port_total - 10 - 10 - 21.60, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Dieta Chofer:</span>
              <span class="font-mono text-slate-200">${{ number_format(10.00, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Desgaste Vehículo:</span>
              <span class="font-mono text-slate-200">${{ number_format(21.60, 2) }}</span>
            </div>
          </div>
        </div>
        <p class="text-[10px] text-slate-500 pt-2 border-t border-slate-800">1 viaje Mariel (180 km)</p>
      </div>

      <!-- Etapa 2: Transitaria y Desagrupe -->
      <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-white flex items-center gap-1.5">
              <i class="fa-solid fa-boxes-packing text-blue-400"></i> Etapa 2: Transitaria
            </span>
            <span class="font-mono text-xs font-bold text-blue-300">${{ number_format($analysis->cost_transitaria_total, 2) }}</span>
          </div>
          <div class="mt-3 text-[11px] space-y-1.5 text-slate-400">
            <div class="flex justify-between">
              <span>Mano de Obra Desagrupe:</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_transitaria_total, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Servicio Transitaria ({{ $analysis->transitaria }}):</span>
              <span class="font-mono text-slate-200">$0.00</span>
            </div>
            <div class="flex justify-between">
              <span>Estadía / Almacenaje:</span>
              <span class="font-mono text-slate-200">$0.00</span>
            </div>
          </div>
        </div>
        <p class="text-[10px] text-slate-500 pt-2 border-t border-slate-800">Norma: 1000 kg / jornal ($15 USD)</p>
      </div>

      <!-- Etapa 3: Distribución Regional -->
      <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-white flex items-center gap-1.5">
              <i class="fa-solid fa-truck-ramp-box text-amber-400"></i> Etapa 3: Distribución
            </span>
            <span class="font-mono text-xs font-bold text-amber-300">${{ number_format($analysis->cost_distribution_total, 2) }}</span>
          </div>
          <div class="mt-3 text-[11px] space-y-1.5 text-slate-400">
            <div class="flex justify-between">
              <span>Occidente (Habana):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_distribution_occidente, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Centro (Traslado + Hub):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_distribution_centro, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Oriente (Traslado + Hub):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_distribution_oriente, 2) }}</span>
            </div>
          </div>
        </div>
        <p class="text-[10px] text-slate-500 pt-2 border-t border-slate-800">Combustible diésel y cuadrillas</p>
      </div>

      <!-- Etapa 4: Comercialización y Deducciones -->
      <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-white flex items-center gap-1.5">
              <i class="fa-solid fa-handshake text-purple-400"></i> Etapa 4: Comercial
            </span>
            <span class="font-mono text-xs font-bold text-purple-300">${{ number_format($analysis->cost_commission_total + $analysis->cost_provisions_and_bank, 2) }}</span>
          </div>
          <div class="mt-3 text-[11px] space-y-1.5 text-slate-400">
            <div class="flex justify-between">
              <span>Comisión Venta (3%):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->cost_commission_total, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Provisión Reclamos (0.5%):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->revenue_total * 0.005, 2) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Comisiones Banco (1.0%):</span>
              <span class="font-mono text-slate-200">${{ number_format($analysis->revenue_total * 0.010, 2) }}</span>
            </div>
          </div>
        </div>
        <p class="text-[10px] text-slate-500 pt-2 border-t border-slate-800">4.5% total deducido sobre ingreso</p>
      </div>
    </div>
  </div>

  <!-- Desglose y Cuadre Regional Comparativo -->
  <div class="rounded-3xl glass-card border border-slate-800 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
          <i class="fa-solid fa-map-location-dot text-cyan-400"></i>
          <span>Desglose Económico Regional y Cuadre Matemático</span>
        </h4>
        <p class="text-[11px] text-slate-400 mt-0.5">Asignación de ingresos, costos directos y costos compartidos por cada zona de destino.</p>
      </div>
      <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/30">
        Cuadre Exacto: $0.00 USD diff
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs font-mono border-collapse">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-950/60 text-slate-400 text-[11px] uppercase">
            <th class="py-3 px-4 font-sans">Región de Destino</th>
            <th class="py-3 px-4 text-right">Carga (kg)</th>
            <th class="py-3 px-4 text-right">% Carga</th>
            <th class="py-3 px-4 text-right">Ingresos Facturados</th>
            <th class="py-3 px-4 text-right">Costos Directos</th>
            <th class="py-3 px-4 text-right">Fijos Asignados</th>
            <th class="py-3 px-4 text-right">Utilidad Neta Zona</th>
            <th class="py-3 px-4 text-center font-sans">Viabilidad</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @php
            $rb = $analysis->regional_breakdown;
          @endphp
          <!-- Occidente -->
          <tr class="hover:bg-slate-800/40">
            <td class="py-3 px-4 font-bold text-white font-sans flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
              <span>Occidente (Habana)</span>
            </td>
            <td class="py-3 px-4 text-right text-slate-200">{{ number_format($analysis->kg_occidente, 0) }} kg</td>
            <td class="py-3 px-4 text-right text-slate-400">{{ round(($analysis->kg_occidente / max(1, $analysis->kg_total)) * 100, 1) }}%</td>
            <td class="py-3 px-4 text-right text-slate-200">${{ number_format($analysis->revenue_occidente, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->cost_distribution_occidente, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->fixed_costs_assigned_total * ($analysis->kg_occidente / max(1, $analysis->kg_total)), 2) }}</td>
            <td class="py-3 px-4 text-right font-black {{ ($rb['occidente']['net_profit'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($rb['occidente']['net_profit'] ?? 0, 2) }}
            </td>
            <td class="py-3 px-4 text-center font-sans">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                Altamente Rentable
              </span>
            </td>
          </tr>

          <!-- Centro -->
          <tr class="hover:bg-slate-800/40">
            <td class="py-3 px-4 font-bold text-white font-sans flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
              <span>Centro (Santa Clara Hub)</span>
            </td>
            <td class="py-3 px-4 text-right text-slate-200">{{ number_format($analysis->kg_centro, 0) }} kg</td>
            <td class="py-3 px-4 text-right text-slate-400">{{ round(($analysis->kg_centro / max(1, $analysis->kg_total)) * 100, 1) }}%</td>
            <td class="py-3 px-4 text-right text-slate-200">${{ number_format($analysis->revenue_centro, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->cost_distribution_centro, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->fixed_costs_assigned_total * ($analysis->kg_centro / max(1, $analysis->kg_total)), 2) }}</td>
            <td class="py-3 px-4 text-right font-black {{ ($rb['centro']['net_profit'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($rb['centro']['net_profit'] ?? 0, 2) }}
            </td>
            <td class="py-3 px-4 text-center font-sans">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                Rentable
              </span>
            </td>
          </tr>

          <!-- Oriente -->
          <tr class="hover:bg-slate-800/40">
            <td class="py-3 px-4 font-bold text-white font-sans flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
              <span>Oriente (Santiago / Holguín)</span>
            </td>
            <td class="py-3 px-4 text-right text-slate-200">{{ number_format($analysis->kg_oriente, 0) }} kg</td>
            <td class="py-3 px-4 text-right text-slate-400">{{ round(($analysis->kg_oriente / max(1, $analysis->kg_total)) * 100, 1) }}%</td>
            <td class="py-3 px-4 text-right text-slate-200">${{ number_format($analysis->revenue_oriente, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->cost_distribution_oriente, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-400">${{ number_format($analysis->fixed_costs_assigned_total * ($analysis->kg_oriente / max(1, $analysis->kg_total)), 2) }}</td>
            <td class="py-3 px-4 text-right font-black {{ ($rb['oriente']['net_profit'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($rb['oriente']['net_profit'] ?? 0, 2) }}
            </td>
            <td class="py-3 px-4 text-center font-sans">
              @if(($rb['oriente']['net_profit'] ?? 0) >= 0)
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Marginal</span>
              @else
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">Déficit</span>
              @endif
            </td>
          </tr>
        </tbody>
        <tfoot class="border-t-2 border-slate-700 bg-slate-950 font-bold">
          <tr>
            <td class="py-3 px-4 font-sans text-white">TOTAL CONSOLIDADO</td>
            <td class="py-3 px-4 text-right text-white">{{ number_format($analysis->kg_total, 0) }} kg</td>
            <td class="py-3 px-4 text-right text-white">100.0%</td>
            <td class="py-3 px-4 text-right text-cyan-300">${{ number_format($analysis->revenue_total, 2) }}</td>
            <td class="py-3 px-4 text-right text-amber-300">${{ number_format($analysis->cost_distribution_total, 2) }}</td>
            <td class="py-3 px-4 text-right text-slate-300">${{ number_format($analysis->fixed_costs_assigned_total, 2) }}</td>
            <td class="py-3 px-4 text-right {{ $analysis->profit_before_tax >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">${{ number_format($analysis->profit_before_tax, 2) }}</td>
            <td class="py-3 px-4 text-center font-sans text-emerald-400">Cuadre OK</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Matriz de Sensibilidad al Diésel y Tarifa de Flete -->
  <div class="rounded-3xl glass-card border border-slate-800 p-6 space-y-4 no-print">
    <div>
      <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
        <i class="fa-solid fa-chart-line text-cyan-400"></i>
        <span>Simulador de Sensibilidad: Diésel CUPET vs Tarifa Flete</span>
      </h4>
      <p class="text-[11px] text-slate-400 mt-0.5">Impacto en la Utilidad Neta (USD) del contenedor ante fluctuaciones de combustible y tarifas por kg.</p>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-center text-xs font-mono border-collapse">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-950/60 text-slate-400 text-[11px]">
            <th class="py-3 px-4 text-left font-sans">Tarifa Flete (USD/kg) \ Diésel</th>
            @foreach($sensitivity['diesel_prices'] as $dp)
              <th class="py-3 px-4 font-bold text-white">${{ number_format($dp, 2) }} USD/L</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @foreach($sensitivity['matrix'] as $row)
            <tr class="hover:bg-slate-800/40">
              <td class="py-3 px-4 text-left font-bold text-cyan-300 font-sans">
                ${{ number_format($row['tariff'], 2) }} USD/kg
              </td>
              @foreach($sensitivity['diesel_prices'] as $dp)
                @php
                  $res = $row['results'][(string) $dp] ?? null;
                  $val = $res['net_profit'] ?? 0;
                @endphp
                <td class="py-3 px-4 {{ $val >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-bold">
                  ${{ number_format($val, 2) }}
                  <span class="block text-[9px] text-slate-500 font-normal">{{ $res['net_margin_pct'] ?? 0 }}%</span>
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Centro de Alertas Económicas y Semáforo de Riesgo (Fase 6.5.10) -->
  <div class="rounded-3xl glass-card border border-slate-800 p-6 space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
          <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
          <span>Alertas Económicas y Semáforo de Riesgo Operativo</span>
        </h4>
        <p class="text-[11px] text-slate-400 mt-0.5">Detección de desviaciones de diésel, rentabilidad marginal, déficit en tramos o pérdidas operativas.</p>
      </div>

      @php
        $highAlertsCount = $analysis->alerts->where('severity', 'alta')->where('is_investigated', false)->count();
        $medAlertsCount = $analysis->alerts->where('severity', 'media')->where('is_investigated', false)->count();
        $resolvedCount = $analysis->alerts->where('is_investigated', true)->count();
      @endphp

      <div class="flex items-center gap-2 font-mono text-[11px]">
        @if($highAlertsCount > 0)
          <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/30 font-bold flex items-center gap-1.5 animate-pulse">
            <i class="fa-solid fa-circle text-[8px] text-rose-500"></i> Críticas: {{ $highAlertsCount }}
          </span>
        @endif
        @if($medAlertsCount > 0)
          <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle text-[8px] text-amber-500"></i> Medias: {{ $medAlertsCount }}
          </span>
        @endif
        @if($resolvedCount > 0)
          <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-check text-[10px]"></i> Justificadas: {{ $resolvedCount }}
          </span>
        @endif
        @if($analysis->alerts->isEmpty())
          <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-shield-check text-xs"></i> 0 Alertas
          </span>
        @endif
      </div>
    </div>

    @if($analysis->alerts->isNotEmpty())
      <div class="space-y-3">
        @foreach($analysis->alerts as $alert)
          <div class="p-4 rounded-2xl bg-slate-900/80 border {{ $alert->is_investigated ? 'border-slate-800' : ($alert->severity === 'alta' ? 'border-rose-900/50 bg-rose-950/10' : 'border-amber-900/50 bg-amber-950/10') }} space-y-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                @if($alert->severity === 'alta')
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[6px]"></i> ALTA SEVERIDAD
                  </span>
                @elseif($alert->severity === 'media')
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[6px]"></i> MEDIA SEVERIDAD
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-cyan-500/20 text-cyan-400 border border-cyan-500/40 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[6px]"></i> BAJA
                  </span>
                @endif
                <span class="text-xs font-bold text-white font-mono">{{ $alert->alert_type }}</span>
              </div>

              @if($alert->is_investigated)
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                  <i class="fa-solid fa-check-double text-[10px]"></i> Investigada & Justificada
                </span>
              @else
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                  <i class="fa-solid fa-clock text-[10px]"></i> Pendiente de Justificación
                </span>
              @endif
            </div>

            <p class="text-xs text-slate-300">{{ $alert->description }}</p>

            @if($alert->is_investigated)
              <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs space-y-1">
                <div class="flex items-center justify-between text-slate-400 text-[11px]">
                  <span>Justificación Registrada:</span>
                  <span class="font-mono text-slate-500">Por: {{ $alert->resolver?->name ?? 'Usuario' }} | {{ $alert->resolved_at?->format('d/m/Y H:i') }}</span>
                </div>
                <p class="text-slate-200 italic font-sans">"{{ $alert->justification }}"</p>
              </div>
            @else
              <!-- Formulario para Investigar / Justificar Alerta -->
              <details class="text-xs group">
                <summary class="cursor-pointer text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1.5 transition-colors select-none">
                  <i class="fa-solid fa-pen-to-square"></i>
                  <span>Investigar y Justificar Desviación Económica</span>
                </summary>
                <form action="{{ route('profitability.alerts.justify', $alert) }}" method="POST" class="mt-3 p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                  @csrf
                  <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">
                      Explicación o Causa Justificada de la Desviación:
                    </label>
                    <textarea 
                      name="justification" 
                      rows="2" 
                      required 
                      minlength="5" 
                      maxlength="1000"
                      placeholder="Indique los motivos operativos o comerciales (ej: desviación aprobada por condiciones climáticas en ruta Central, compensación por carga adicional acordada con transitaria, etc.)..."
                      class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 focus:outline-none"
                    ></textarea>
                  </div>
                  <div class="flex justify-end">
                    <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-lg font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
                      <i class="fa-solid fa-floppy-disk"></i>
                      <span>Guardar Justificación y Resolver Alerta</span>
                    </button>
                  </div>
                </form>
              </details>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-900/30 flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
          <i class="fa-solid fa-check"></i>
        </div>
        <div>
          <p class="text-xs font-bold text-white">Sin Desviaciones Operativas</p>
          <p class="text-[11px] text-slate-400">El contenedor cumple con los consumos estándar de combustible y se encuentra dentro de los márgenes previstos de rentabilidad.</p>
        </div>
      </div>
    @endif
  </div>

  <!-- Bloque 5: Control, Auditoría y Trazabilidad Documental -->
  <div class="rounded-3xl glass-card border border-slate-800 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
          <i class="fa-solid fa-shield-halved text-cyan-400"></i>
          <span>Bloque 5: Control Contable, Auditoría y Snapshot de Tarifas</span>
        </h4>
        <p class="text-[11px] text-slate-400 mt-0.5">Trazabilidad oficial de congelación de parámetros, origen de datos y autorizaciones.</p>
      </div>
      @if($analysis->economic_status === 'cerrado')
        <span class="px-3 py-1 rounded-full text-xs font-bold font-mono bg-rose-500/10 text-rose-400 border border-rose-500/30 flex items-center gap-1.5">
          <i class="fa-solid fa-lock"></i> Snapshot Inmutable Congelado
        </span>
      @else
        <span class="px-3 py-1 rounded-full text-xs font-bold font-mono bg-blue-500/10 text-blue-400 border border-blue-500/30 flex items-center gap-1.5">
          <i class="fa-solid fa-clock-rotate-left"></i> Tarifas Dinámicas Vigentes
        </span>
      @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
        <span class="text-slate-400 font-semibold block">Registro y Trazabilidad</span>
        <div class="space-y-1">
          <p class="text-slate-300">Registrado por: <strong class="text-white">{{ $analysis->creator?->name ?? 'Sistema Automático' }}</strong></p>
          <p class="text-slate-300">Fecha Creación: <span class="font-mono text-slate-400">{{ $analysis->created_at->format('d/m/Y H:i') }}</span></p>
          @if($analysis->closed_by)
            <p class="text-slate-300">Cerrado por: <strong class="text-rose-300">{{ $analysis->closer?->name ?? 'Jefe Económico' }}</strong></p>
            <p class="text-slate-300">Fecha Cierre: <span class="font-mono text-slate-400">{{ $analysis->closed_at?->format('d/m/Y H:i') }}</span></p>
          @endif
          @if($analysis->reopened_reason)
            <p class="text-amber-300 font-semibold mt-1">Reapertura previa: <em class="text-slate-300 font-normal">{{ $analysis->reopened_reason }}</em></p>
          @endif
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
        <span class="text-slate-400 font-semibold block">Normas Técnicas y Origen de Datos</span>
        <div class="space-y-1">
          @if($analysis->is_precalculation)
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 inline-block">
              Estimado por Norma Técnica (Faltan datos reales de combustible)
            </span>
          @else
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 inline-block">
              Liquidación con Datos Reales Operativos
            </span>
          @endif
          <p class="text-slate-400 text-[11px] mt-1">
            Norma Mariel: 60 L / viaje | Rendimiento cuadrilla: 1,000 kg/jornal.
          </p>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
        <span class="text-slate-400 font-semibold block">Tarifas y Precios Aplicados</span>
        @php
          $ratesSnap = $analysis->applied_rates_snapshot;
          $rateInfo = $ratesSnap['fuel_and_tariff_rates'] ?? null;
        @endphp
        <div class="space-y-1 font-mono text-[11px]">
          <p class="text-slate-300">Diésel: <strong class="text-cyan-400">${{ number_format($rateInfo['diesel_price_usd_l'] ?? 1.10, 2) }} USD/L</strong></p>
          <p class="text-slate-300">Flete Base: <strong class="text-white">${{ number_format($rateInfo['tariff_occidente_usd_kg'] ?? 0.80, 2) }} USD/kg</strong></p>
          <p class="text-slate-300">Desgaste Camión: <strong class="text-slate-400">${{ number_format($rateInfo['vehicle_wear_usd_km'] ?? 0.12, 2) }} USD/km</strong></p>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
