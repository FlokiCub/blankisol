@extends('layouts.app', ['title' => 'Simulador Interactivo de Rentabilidad'])

@section('page_title', 'Simulador Dinámico de Rentabilidad y Sensibilidad')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <a href="{{ route('profitability.index') }}" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
          <i class="fa-solid fa-sliders mr-1.5"></i>Simulador en Tiempo Real
        </span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
        Simulador de Escenarios Económicos
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Experimenta instantáneamente con variaciones en el precio del combustible (CUPET), tarifas de flete y balance de carga entre regiones para predecir la rentabilidad de las importaciones.
      </p>
    </div>

    <div>
      <button onclick="resetDefaults()" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all flex items-center gap-2">
        <i class="fa-solid fa-rotate-left"></i>
        <span>Restablecer Parámetros Base</span>
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Columna Izquierda: Controles & Sliders -->
    <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-800 space-y-6">
      <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2 pb-3 border-b border-slate-800">
        <i class="fa-solid fa-wrench text-cyan-400"></i>
        <span>Variables Operativas y Tarifarias</span>
      </h4>

      <!-- Slider 1: Precio Diésel (USD/L) -->
      <div class="space-y-2">
        <div class="flex justify-between items-center text-xs">
          <label class="font-bold text-slate-300">Precio Diésel CUPET</label>
          <span class="font-mono font-black text-cyan-400 text-sm" id="diesel_val_display">$2.00 USD/L</span>
        </div>
        <input 
          type="range" 
          id="diesel_slider" 
          min="1.0" 
          max="4.0" 
          step="0.1" 
          value="{{ $defaultDiesel }}" 
          oninput="onParamChange()" 
          class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-cyan-500"
        />
        <div class="flex justify-between text-[10px] text-slate-500 font-mono">
          <span>$1.00 USD/L</span>
          <span>$2.00 (Oficial)</span>
          <span>$4.00 USD/L</span>
        </div>
      </div>

      <!-- Slider 2: Tarifa de Flete Base (USD/kg) -->
      <div class="space-y-2">
        <div class="flex justify-between items-center text-xs">
          <label class="font-bold text-slate-300">Tarifa Flete Cliente</label>
          <span class="font-mono font-black text-cyan-400 text-sm" id="tariff_val_display">$0.80 USD/kg</span>
        </div>
        <input 
          type="range" 
          id="tariff_slider" 
          min="0.5" 
          max="1.5" 
          step="0.05" 
          value="{{ $defaultTariff }}" 
          oninput="onParamChange()" 
          class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-cyan-500"
        />
        <div class="flex justify-between text-[10px] text-slate-500 font-mono">
          <span>$0.50 USD/kg</span>
          <span>$0.80 (Base)</span>
          <span>$1.50 USD/kg</span>
        </div>
      </div>

      <!-- Pesos Regionales -->
      <div class="pt-4 border-t border-slate-800 space-y-4">
        <label class="block text-xs font-bold text-slate-300">
          Distribución de Peso por Región (kg):
        </label>

        <!-- Occidente -->
        <div>
          <div class="flex justify-between text-xs text-slate-400 mb-1">
            <span>Occidente (Habana)</span>
            <span class="font-mono text-white font-bold" id="kg_occ_display">5,200 kg</span>
          </div>
          <input 
            type="range" 
            id="kg_occ_slider" 
            min="0" 
            max="10000" 
            step="100" 
            value="5200" 
            oninput="onParamChange()" 
            class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-cyan-500"
          />
        </div>

        <!-- Centro -->
        <div>
          <div class="flex justify-between text-xs text-slate-400 mb-1">
            <span>Centro (Santa Clara)</span>
            <span class="font-mono text-white font-bold" id="kg_cen_display">2,600 kg</span>
          </div>
          <input 
            type="range" 
            id="kg_cen_slider" 
            min="0" 
            max="10000" 
            step="100" 
            value="2600" 
            oninput="onParamChange()" 
            class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-blue-500"
          />
        </div>

        <!-- Oriente -->
        <div>
          <div class="flex justify-between text-xs text-slate-400 mb-1">
            <span>Oriente (Santiago)</span>
            <span class="font-mono text-white font-bold" id="kg_ori_display">2,300 kg</span>
          </div>
          <input 
            type="range" 
            id="kg_ori_slider" 
            min="0" 
            max="10000" 
            step="100" 
            value="2300" 
            oninput="onParamChange()" 
            class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-purple-500"
          />
        </div>

        <!-- Total Weight Metric -->
        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 flex justify-between items-center text-xs">
          <span class="text-slate-400">Peso Total Contenedor:</span>
          <span class="font-mono font-bold text-white text-sm" id="sim_total_kg">10,100 kg</span>
        </div>
      </div>
    </div>

    <!-- Columna Central y Derecha: Resultados Dinámicos -->
    <div class="lg:col-span-2 space-y-6">

      <!-- Veredicto Card en Vivo -->
      <div id="sim_verdict_card" class="p-6 rounded-3xl border bg-emerald-950/40 border-emerald-500/40 text-emerald-200 transition-all shadow-xl">
        <div class="flex items-center gap-2.5 text-base font-black uppercase font-mono tracking-wide" id="sim_verdict_title">
          <i class="fa-solid fa-circle-check text-emerald-400"></i>
          <span>🟢 OPERACIÓN FACTIBLE CON UTILIDADES</span>
        </div>
        <p class="text-xs sm:text-sm mt-2 leading-relaxed" id="sim_verdict_summary">
          Cargando análisis dinámico...
        </p>
      </div>

      <!-- Métricas Resumen Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl glass-card border border-slate-800">
          <p class="text-[11px] text-slate-400 font-semibold">Ingresos Proyectados</p>
          <p class="text-xl font-black text-white font-mono mt-1" id="sim_revenue">$8,080.00</p>
          <p class="text-[10px] text-slate-500 mt-0.5" id="sim_rev_per_kg">$0.800 USD/kg</p>
        </div>

        <div class="p-4 rounded-2xl glass-card border border-slate-800">
          <p class="text-[11px] text-slate-400 font-semibold">Costos Variables (4 Etapas)</p>
          <p class="text-xl font-black text-amber-300 font-mono mt-1" id="sim_var_costs">$3,339.20</p>
          <p class="text-[10px] text-slate-500 mt-0.5">Operación directa</p>
        </div>

        <div class="p-4 rounded-2xl glass-card border border-slate-800">
          <p class="text-[11px] text-slate-400 font-semibold">Margen Contribución (MC)</p>
          <p class="text-xl font-black text-cyan-400 font-mono mt-1" id="sim_mc">$4,740.80</p>
          <p class="text-[10px] text-slate-500 mt-0.5" id="sim_mc_pct">58.7% sobre ingreso</p>
        </div>

        <div class="p-4 rounded-2xl glass-card border border-slate-800">
          <p class="text-[11px] text-slate-400 font-semibold">Utilidad Neta Proyectada</p>
          <p class="text-xl font-black text-emerald-400 font-mono mt-1" id="sim_net_profit">$1,414.28</p>
          <p class="text-[10px] text-slate-500 mt-0.5" id="sim_net_margin">17.5% margen neto</p>
        </div>
      </div>

      <!-- Umbrales de Equilibrio Card -->
      <div class="p-6 rounded-3xl glass-card border border-slate-800 space-y-4">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
          <i class="fa-solid fa-scale-balanced text-cyan-400"></i>
          <span>Puntos de Equilibrio (Break-Even) Calculados</span>
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-1">
            <span class="text-slate-400 block font-sans">Tarifa Mínima de Equilibrio:</span>
            <span class="text-lg font-black text-white" id="sim_breakeven_tariff">$0.585 USD/kg</span>
            <p class="text-[10px] text-slate-500 font-sans">Precio mínimo al cliente para evitar pérdidas</p>
          </div>

          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-1">
            <span class="text-slate-400 block font-sans">Carga Mínima de Equilibrio:</span>
            <span class="text-lg font-black text-white" id="sim_breakeven_kg">5,450 kg</span>
            <p class="text-[10px] text-slate-500 font-sans">Kilogramos necesarios para cubrir estructura y flete</p>
          </div>
        </div>
      </div>

      <!-- Desglose por Etapas -->
      <div class="p-6 rounded-3xl glass-card border border-slate-800 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
          <i class="fa-solid fa-receipt text-amber-400"></i>
          <span>Distribución del Costo Variable por Etapas (USD)</span>
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-850">
            <span class="text-[10px] text-slate-400 block font-sans">Puerto Mariel:</span>
            <strong class="text-white" id="sim_cost_port">$165.60</strong>
          </div>
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-850">
            <span class="text-[10px] text-slate-400 block font-sans">Transitaria:</span>
            <strong class="text-white" id="sim_cost_transitaria">$165.00</strong>
          </div>
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-850">
            <span class="text-[10px] text-slate-400 block font-sans">Transporte Regional:</span>
            <strong class="text-white" id="sim_cost_distribution">$2,645.00</strong>
          </div>
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-850">
            <span class="text-[10px] text-slate-400 block font-sans">Comisión & Banca:</span>
            <strong class="text-white" id="sim_cost_commercial">$363.60</strong>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>

<script>
  let debounceTimeout = null;

  function onParamChange() {
    const diesel = parseFloat(document.getElementById('diesel_slider').value);
    const tariff = parseFloat(document.getElementById('tariff_slider').value);
    const kgOcc = parseFloat(document.getElementById('kg_occ_slider').value);
    const kgCen = parseFloat(document.getElementById('kg_cen_slider').value);
    const kgOri = parseFloat(document.getElementById('kg_ori_slider').value);

    document.getElementById('diesel_val_display').textContent = '$' + diesel.toFixed(2) + ' USD/L';
    document.getElementById('tariff_val_display').textContent = '$' + tariff.toFixed(2) + ' USD/kg';
    document.getElementById('kg_occ_display').textContent = kgOcc.toLocaleString() + ' kg';
    document.getElementById('kg_cen_display').textContent = kgCen.toLocaleString() + ' kg';
    document.getElementById('kg_ori_display').textContent = kgOri.toLocaleString() + ' kg';
    document.getElementById('sim_total_kg').textContent = (kgOcc + kgCen + kgOri).toLocaleString() + ' kg';

    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      fetchSimulation(diesel, tariff, kgOcc, kgCen, kgOri);
    }, 200);
  }

  async function fetchSimulation(diesel, tariff, kgOcc, kgCen, kgOri) {
    try {
      const response = await fetch('{{ route('profitability.simulate-live') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          custom_diesel_price: diesel,
          custom_tariff_rate: tariff,
          kg_occidente: kgOcc,
          kg_centro: kgCen,
          kg_oriente: kgOri,
          transitaria: 'ENCI'
        })
      });

      const res = await response.json();
      if (res.success && res.data) {
        updateUI(res.data);
      }
    } catch (e) {
      console.error('Error fetching simulation:', e);
    }
  }

  function updateUI(d) {
    document.getElementById('sim_revenue').textContent = '$' + d.revenues.total.toLocaleString('en-US', { minimumFractionDigits: 2 });
    document.getElementById('sim_rev_per_kg').textContent = '$' + d.revenues.revenue_per_kg.toFixed(3) + ' USD/kg';
    document.getElementById('sim_var_costs').textContent = '$' + d.costs_breakdown.total_variable.toLocaleString('en-US', { minimumFractionDigits: 2 });
    document.getElementById('sim_mc').textContent = '$' + d.contribution_margin.amount.toLocaleString('en-US', { minimumFractionDigits: 2 });
    document.getElementById('sim_mc_pct').textContent = d.contribution_margin.pct.toFixed(1) + '% sobre ingreso';
    
    const netProfitEl = document.getElementById('sim_net_profit');
    netProfitEl.textContent = '$' + d.net_results.net_profit.toLocaleString('en-US', { minimumFractionDigits: 2 });
    netProfitEl.className = 'text-xl font-black font-mono mt-1 ' + (d.net_results.net_profit >= 0 ? 'text-emerald-400' : 'text-rose-400');
    document.getElementById('sim_net_margin').textContent = d.net_results.net_margin_pct.toFixed(1) + '% margen neto';

    document.getElementById('sim_breakeven_tariff').textContent = '$' + d.breakeven.breakeven_tariff_usd_kg.toFixed(4) + ' USD/kg';
    document.getElementById('sim_breakeven_kg').textContent = d.breakeven.breakeven_kg.toLocaleString() + ' kg';

    document.getElementById('sim_cost_port').textContent = '$' + d.costs_breakdown.port.total.toFixed(2);
    document.getElementById('sim_cost_transitaria').textContent = '$' + d.costs_breakdown.transitaria.total.toFixed(2);
    document.getElementById('sim_cost_distribution').textContent = '$' + d.costs_breakdown.distribution.total.toFixed(2);
    document.getElementById('sim_cost_commercial').textContent = '$' + (d.costs_breakdown.commissions.total + d.costs_breakdown.provisions_and_bank.total).toFixed(2);

    // Verdict card update
    const card = document.getElementById('sim_verdict_card');
    const title = document.getElementById('sim_verdict_title');
    const summary = document.getElementById('sim_verdict_summary');

    if (d.decision.verdict === 'FACTIBLE') {
      card.className = 'p-6 rounded-3xl border bg-emerald-950/40 border-emerald-500/40 text-emerald-200 transition-all shadow-xl';
      title.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400"></i> <span>🟢 OPERACIÓN FACTIBLE CON UTILIDADES</span>';
    } else if (d.decision.verdict === 'MARGINAL') {
      card.className = 'p-6 rounded-3xl border bg-amber-950/40 border-amber-500/40 text-amber-200 transition-all shadow-xl';
      title.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-amber-400"></i> <span>🟡 RENTABILIDAD MARGINAL VULNERABLE</span>';
    } else {
      card.className = 'p-6 rounded-3xl border bg-rose-950/40 border-rose-500/40 text-rose-200 transition-all shadow-xl';
      title.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-400"></i> <span>🔴 OPERACIÓN NO FACTIBLE (A PÉRDIDA)</span>';
    }
    summary.textContent = d.decision.summary;
  }

  function resetDefaults() {
    document.getElementById('diesel_slider').value = 2.0;
    document.getElementById('tariff_slider').value = 0.8;
    document.getElementById('kg_occ_slider').value = 5200;
    document.getElementById('kg_cen_slider').value = 2600;
    document.getElementById('kg_ori_slider').value = 2300;
    onParamChange();
  }

  document.addEventListener('DOMContentLoaded', () => {
    onParamChange();
  });
</script>
@endsection
