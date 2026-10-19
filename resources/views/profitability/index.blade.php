@extends('layouts.app', ['title' => 'Rentabilidad de Contenedores y Rutas'])

@section('page_title', 'Torre Financiera — Inteligencia y Rentabilidad de Contenedores')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo & Acciones -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
          <i class="fa-solid fa-coins mr-1.5"></i>Modelo Económico SCGI 2026
        </span>
        <span class="text-xs font-mono text-slate-400">Tarifas CUPET & Ley 113</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Control de Rentabilidad & Viabilidad</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Evaluación integral en 4 etapas (Puerto Mariel, Transitaria, Distribución Regional escalonada y Comercialización), margen de contribución (<strong class="text-cyan-400 font-mono">MC</strong>), punto de equilibrio y rentabilidad neta auditada.
      </p>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('profitability.operational-containers') }}" class="px-5 py-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-600/40 hover:border-emerald-500 text-emerald-300 hover:text-white font-bold text-xs shadow-lg active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
        <span>Contenedores Operativos</span>
      </a>

      <a href="{{ route('profitability.simulator') }}" class="px-5 py-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-200 hover:text-white font-bold text-xs shadow-lg active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-sliders text-cyan-400"></i>
        <span>Simulador de Escenarios</span>
      </a>

      <a href="{{ route('profitability.create') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-xl shadow-cyan-950/50 active:scale-98 transition-all flex items-center gap-2">
        <i class="fa-solid fa-calculator text-sm"></i>
        <span>Evaluar Nuevo Contenedor</span>
      </a>
    </div>
  </div>

  <!-- Barra de Navegación de Gestión Económica -->
  <div class="flex flex-wrap items-center gap-2 p-2 rounded-2xl bg-slate-900/60 border border-slate-800">
    <span class="px-3 py-1 text-slate-400 font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
      <i class="fa-solid fa-briefcase text-cyan-400"></i> Gestión Económica:
    </span>
    <a href="{{ route('profitability.management.fixed-costs') }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-file-invoice-dollar text-cyan-400"></i> Gastos Fijos
    </a>
    <a href="{{ route('profitability.management.assets') }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-truck-ramp-box text-purple-400"></i> Activos & Deprec.
    </a>
    <a href="{{ route('profitability.management.commercial') }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-users-gear text-amber-400"></i> Red Comercial
    </a>
    <a href="{{ route('profitability.management.parameters') }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-sliders text-blue-400"></i> Parámetros & Tarifas
    </a>
    <a href="{{ route('profitability.management.closings') }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-lock text-rose-400"></i> Cierre Mensual
    </a>
    <a href="{{ route('profitability.monthly-summary') }}" class="px-3 py-1.5 rounded-xl bg-cyan-950/40 border border-cyan-500/30 hover:border-cyan-400 text-cyan-300 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
      <i class="fa-solid fa-calendar-days text-cyan-400"></i> Resumen Mes a Mes (YTD)
    </a>
  </div>

  <!-- Nivel 1: KPIs Ejecutivos Principales (Carga, Ingresos, Costos Variables, Margen, Utilidad Neta) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Carga Total Evaluada -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Carga Total Analizada</span>
        <i class="fa-solid fa-weight-hanging text-cyan-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-white font-mono">{{ number_format($totalKg, 0) }} <span class="text-xs text-slate-400 font-sans">kg</span></p>
        <p class="text-[11px] text-slate-500 mt-0.5">{{ $totalContainers }} contenedores (Med: {{ number_format($avgKgPerContainer, 0) }} kg/cont)</p>
      </div>
    </div>

    <!-- Ingresos Totales -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Ingresos Facturados</span>
        <i class="fa-solid fa-file-invoice-dollar text-blue-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-white font-mono">${{ number_format($totalRevenue, 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Ingreso medio: <strong class="text-slate-300 font-mono">${{ number_format($avgRevenuePerKg, 2) }}/kg</strong></p>
      </div>
    </div>

    <!-- Costes Variables Directos -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Costos Variables Totales</span>
        <i class="fa-solid fa-route text-amber-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-amber-300 font-mono">${{ number_format($totalVariableCost, 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Coste variable: <strong class="text-slate-300 font-mono">${{ number_format($avgVariableCostPerKg, 2) }}/kg</strong></p>
      </div>
    </div>

    <!-- Margen de Contribución -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Margen Contribución (MC)</span>
        <i class="fa-solid fa-chart-line text-emerald-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-emerald-400 font-mono">${{ number_format($totalContributionMargin, 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Ratio MC: <strong class="text-emerald-400">{{ $contributionMarginRatio }}%</strong> de ingresos</p>
      </div>
    </div>

    <!-- Utilidad Neta Total -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Utilidad Neta Total</span>
        <i class="fa-solid fa-sack-dollar text-purple-400"></i>
      </div>
      <div class="mt-3">
        <p class="text-2xl font-black text-purple-300 font-mono">${{ number_format($totalNetProfit, 2) }}</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Margen neto global: <strong class="text-white">{{ $avgNetMargin }}%</strong></p>
      </div>
    </div>
  </div>

  <!-- Nivel 2: Matriz Financiera Detallada (Fijos, Tributos, Umbrales y Veredictos) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Costos Fijos Asignados vs No Absorbidos -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Gastos Fijos Imputados</span>
        <i class="fa-solid fa-building text-cyan-400"></i>
      </div>
      <div class="mt-2">
        <p class="text-xl font-black text-white font-mono">${{ number_format($totalFixedAssigned, 2) }}</p>
        <div class="mt-1 flex items-center justify-between text-[11px] text-slate-400">
          <span>No absorbidos período:</span>
          <strong class="{{ ($unabsorbed['unabsorbed_fixed'] ?? 0) > 0 ? 'text-amber-400 font-mono' : 'text-emerald-400 font-mono' }}">
            ${{ number_format($unabsorbed['unabsorbed_fixed'] ?? 0.0, 2) }}
          </strong>
        </div>
      </div>
    </div>

    <!-- Provisión Fiscal y Tributos -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Tributos Ley 113 + Utilidades</span>
        <i class="fa-solid fa-landmark text-blue-400"></i>
      </div>
      <div class="mt-2">
        <p class="text-xl font-black text-blue-300 font-mono">${{ number_format($totalTaxSalesServices + $totalTaxTerritorial + $totalEstimatedIncomeTax, 2) }}</p>
        <p class="text-[11px] text-slate-400 mt-1">
          10%+1%: ${{ number_format($totalTaxSalesServices + $totalTaxTerritorial, 2) }} | 35% Util: ${{ number_format($totalEstimatedIncomeTax, 2) }}
        </p>
      </div>
    </div>

    <!-- Umbral y Margen de Seguridad -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Punto Equilibrio & Seguridad</span>
        <i class="fa-solid fa-scale-balanced text-emerald-400"></i>
      </div>
      <div class="mt-2">
        <p class="text-xl font-black text-emerald-300 font-mono">{{ number_format($avgBreakevenKg, 0) }} <span class="text-xs text-slate-400 font-sans">kg/cont</span></p>
        <p class="text-[11px] text-slate-400 mt-1">
          Margen de seguridad: <strong class="{{ $avgSafetyMarginPct >= 20 ? 'text-emerald-400 font-mono' : 'text-amber-400 font-mono' }}">{{ $avgSafetyMarginPct }}%</strong>
        </p>
      </div>
    </div>

    <!-- Veredictos & Ciclo Económico -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col justify-between">
      <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
        <span>Veredictos & Ciclo Económico</span>
        <i class="fa-solid fa-traffic-light text-amber-400"></i>
      </div>
      <div class="mt-2 flex items-center justify-between gap-1 text-center font-mono">
        <div class="p-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex-1">
          <span class="block text-sm font-black">{{ $feasibleCount }}</span>
          <span class="text-[8px] font-sans font-bold uppercase">Factible</span>
        </div>
        <div class="p-1 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 flex-1">
          <span class="block text-sm font-black">{{ $marginalCount }}</span>
          <span class="text-[8px] font-sans font-bold uppercase">Marginal</span>
        </div>
        <div class="p-1 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 flex-1">
          <span class="block text-sm font-black">{{ $unfeasibleCount }}</span>
          <span class="text-[8px] font-sans font-bold uppercase">Déficit</span>
        </div>
        <div class="p-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 flex-1">
          <span class="block text-sm font-black">{{ $closedCount }}</span>
          <span class="text-[8px] font-sans font-bold uppercase">Cerrados</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Parámetros Económicos Vigentes Banner -->
  @if($activeRate)
  <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 via-cyan-950/30 to-slate-900 border border-cyan-800/40 flex flex-wrap items-center justify-between gap-4 text-xs">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold">
        <i class="fa-solid fa-gas-pump"></i>
      </div>
      <div>
        <p class="font-bold text-white">Tarifario Vigente: Diésel CUPET <span class="font-mono text-cyan-300 font-extrabold">${{ number_format($activeRate->diesel_price_usd_l, 2) }} USD/L</span> | Flete Base: <span class="font-mono text-cyan-300 font-extrabold">${{ number_format($activeRate->tariff_occidente_usd_kg, 2) }} USD/kg</span> | Desgaste: <span class="font-mono text-cyan-300 font-extrabold">${{ number_format($activeRate->vehicle_wear_usd_km, 2) }} USD/km</span></p>
        <p class="text-slate-400 text-[11px]">Vigencia desde {{ $activeRate->effective_from->format('d/m/Y') }} — Base legal Ley 113 (10% Ventas/Servicios + 1% Territorial + 35% Utilidades).</p>
      </div>
    </div>
    <div class="text-[11px] text-slate-400 font-mono">
      Cita Puerto: ${{ number_format($activeRate->port_appointment_usd, 2) }} | Dieta Chofer: ${{ number_format($activeRate->driver_diet_usd, 2) }}
    </div>
  </div>
  @endif

  <!-- Sección de Gráficos Financieros Ejecutivos (6.5.2, 6.5.3, 6.5.4) -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 6.5.2: Gráfico de Evolución Mensual -->
    <div class="lg:col-span-2 p-6 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col justify-between">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
        <div>
          <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-cyan-400"></i>
            <span>Evolución Económica Mensual (Ingresos, Costes & Utilidad)</span>
          </h4>
          <p class="text-xs text-slate-400 mt-0.5">Tendencia histórica de facturación bruta, costos imputados y margen neto.</p>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
          Mensual
        </span>
      </div>
      <div class="mt-4 relative h-64 w-full">
        <canvas id="monthlyEvolutionChart"></canvas>
      </div>
    </div>

    <!-- 6.5.3: Estructura de Costes por Etapa -->
    <div class="p-6 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col justify-between">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
        <div>
          <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-purple-400"></i>
            <span>Estructura de Costes</span>
          </h4>
          <p class="text-xs text-slate-400 mt-0.5">Distribución porcentual por etapa operativa.</p>
        </div>
      </div>
      <div class="mt-4 relative h-64 w-full flex items-center justify-center">
        <canvas id="costStructureChart"></canvas>
      </div>
    </div>
  </div>

  <!-- 6.5.4: Comparativa de Rentabilidad por Región (Occ vs Cen vs Ori) -->
  <div class="p-6 rounded-3xl glass-card border border-slate-800 shadow-2xl space-y-4">
    <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
      <div>
        <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
          <span>Rentabilidad y Desempeño Regional (Occidente vs Centro vs Oriente)</span>
        </h4>
        <p class="text-xs text-slate-400 mt-0.5">Volumen transportado (kg), facturación flete y margen directo por zona geográfica.</p>
      </div>
      <span class="text-xs font-mono text-slate-400">3 Polos Operativos</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-cyan-400 font-mono">POLO OCCIDENTE</span>
          <span class="text-xs font-mono text-slate-400">{{ number_format($regionalComparison['occidente']['kg'], 0) }} kg</span>
        </div>
        <div class="mt-2 space-y-1">
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Ingreso:</span>
            <span class="font-mono text-white font-bold">${{ number_format($regionalComparison['occidente']['revenue'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Coste Distribución:</span>
            <span class="font-mono text-slate-300">${{ number_format($regionalComparison['occidente']['cost'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs border-t border-slate-800 pt-1">
            <span class="text-slate-300 font-bold">Margen Directo:</span>
            <span class="font-mono font-black {{ $regionalComparison['occidente']['profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($regionalComparison['occidente']['profit'], 2) }}
            </span>
          </div>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-blue-400 font-mono">POLO CENTRO</span>
          <span class="text-xs font-mono text-slate-400">{{ number_format($regionalComparison['centro']['kg'], 0) }} kg</span>
        </div>
        <div class="mt-2 space-y-1">
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Ingreso:</span>
            <span class="font-mono text-white font-bold">${{ number_format($regionalComparison['centro']['revenue'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Coste Distribución:</span>
            <span class="font-mono text-slate-300">${{ number_format($regionalComparison['centro']['cost'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs border-t border-slate-800 pt-1">
            <span class="text-slate-300 font-bold">Margen Directo:</span>
            <span class="font-mono font-black {{ $regionalComparison['centro']['profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($regionalComparison['centro']['profit'], 2) }}
            </span>
          </div>
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-purple-400 font-mono">POLO ORIENTE</span>
          <span class="text-xs font-mono text-slate-400">{{ number_format($regionalComparison['oriente']['kg'], 0) }} kg</span>
        </div>
        <div class="mt-2 space-y-1">
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Ingreso:</span>
            <span class="font-mono text-white font-bold">${{ number_format($regionalComparison['oriente']['revenue'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs">
            <span class="text-slate-400">Coste Distribución:</span>
            <span class="font-mono text-slate-300">${{ number_format($regionalComparison['oriente']['cost'], 2) }}</span>
          </div>
          <div class="flex justify-between text-xs border-t border-slate-800 pt-1">
            <span class="text-slate-300 font-bold">Margen Directo:</span>
            <span class="font-mono font-black {{ $regionalComparison['oriente']['profit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
              ${{ number_format($regionalComparison['oriente']['profit'], 2) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filtros Multi-Criterio Avanzados -->
  <div class="p-4 rounded-2xl glass-card border border-slate-800/80">
    <form method="GET" action="{{ route('profitability.index') }}" class="flex flex-wrap items-center gap-3">
      <!-- Search Input -->
      <div class="relative min-w-[200px] flex-1 sm:flex-none">
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Buscar ID Contenedor..." 
          class="w-full pl-9 pr-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 font-mono"
        />
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
      </div>

      <!-- Filtro Período Mes -->
      <div class="flex items-center gap-1.5">
        <span class="text-slate-400 text-[11px] font-bold">Mes:</span>
        <input 
          type="month" 
          name="period" 
          value="{{ request('period') }}" 
          class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:ring-2 focus:ring-cyan-500 font-mono"
        />
      </div>

      <!-- Transitaria Filter -->
      <select name="transitaria" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:ring-2 focus:ring-cyan-500">
        <option value="">Todas las Transitarias</option>
        <option value="ENCI" {{ request('transitaria') === 'ENCI' ? 'selected' : '' }}>ENCI</option>
        <option value="A.V" {{ request('transitaria') === 'A.V' ? 'selected' : '' }}>Aerovaradero (A.V)</option>
        <option value="OTRA" {{ request('transitaria') === 'OTRA' ? 'selected' : '' }}>Otra Transitaria</option>
      </select>

      <!-- Región Filter -->
      <select name="region" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:ring-2 focus:ring-cyan-500">
        <option value="">Todas las Regiones</option>
        <option value="OCC" {{ request('region') === 'OCC' ? 'selected' : '' }}>Occidente</option>
        <option value="CEN" {{ request('region') === 'CEN' ? 'selected' : '' }}>Centro</option>
        <option value="ORI" {{ request('region') === 'ORI' ? 'selected' : '' }}>Oriente</option>
      </select>

      <!-- Estado Económico Filter -->
      <select name="economic_status" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:ring-2 focus:ring-cyan-500">
        <option value="">Todos los Estados Económicos</option>
        <option value="abierto" {{ request('economic_status') === 'abierto' ? 'selected' : '' }}>Abierto (En Operación)</option>
        <option value="en_revision" {{ request('economic_status') === 'en_revision' ? 'selected' : '' }}>En Revisión Contable</option>
        <option value="cerrado" {{ request('economic_status') === 'cerrado' ? 'selected' : '' }}>Cerrado (Bloqueado)</option>
      </select>

      <!-- Verdict Filter -->
      <select name="verdict" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 focus:ring-2 focus:ring-cyan-500">
        <option value="">Todos los Veredictos</option>
        <option value="FACTIBLE" {{ request('verdict') === 'FACTIBLE' ? 'selected' : '' }}>🟢 Factible</option>
        <option value="MARGINAL" {{ request('verdict') === 'MARGINAL' ? 'selected' : '' }}>🟡 Marginal</option>
        <option value="NO_FACTIBLE" {{ request('verdict') === 'NO_FACTIBLE' ? 'selected' : '' }}>🔴 No Factible (Déficit)</option>
      </select>

      <button type="submit" class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-md">
        <i class="fa-solid fa-filter"></i>
        <span>Aplicar Filtros</span>
      </button>

      @if(request()->hasAny(['search', 'period', 'transitaria', 'region', 'economic_status', 'verdict']))
        <a href="{{ route('profitability.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all">
          <i class="fa-solid fa-xmark mr-1"></i>Limpiar
        </a>
      @endif

      <div class="ml-auto flex items-center gap-2">
        <a href="{{ route('profitability.report.pdf', request()->query()) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700/80 hover:border-rose-500/60 text-slate-200 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" title="Informe Oficial Imprimible / PDF">
          <i class="fa-solid fa-file-pdf text-rose-400"></i>
          <span>Informe PDF</span>
        </a>

        <a href="{{ route('profitability.report.excel', request()->query()) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700/80 hover:border-emerald-500/60 text-slate-200 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" title="Descargar Planilla Excel con fórmulas y datos filtrados">
          <i class="fa-solid fa-file-excel text-emerald-400"></i>
          <span>Exportar Excel</span>
        </a>
      </div>
    </form>
  </div>

  <!-- Listado Tabular de Contenedores Analizados -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-950/60 text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">
            <th class="py-4 px-6">Contenedor / Arribo</th>
            <th class="py-4 px-4 text-center">Estado Económico</th>
            <th class="py-4 px-4">Transitaria</th>
            <th class="py-4 px-4 text-right">Peso (Kg)</th>
            <th class="py-4 px-4 text-right">Ingresos ($)</th>
            <th class="py-4 px-4 text-right">Costos Var. ($)</th>
            <th class="py-4 px-4 text-right">Margen (MC)</th>
            <th class="py-4 px-4 text-right">Fijos Asig. ($)</th>
            <th class="py-4 px-4 text-right">Utilidad Neta ($)</th>
            <th class="py-4 px-4 text-center">Veredicto</th>
            <th class="py-4 px-6 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 text-xs">
          @forelse($analyses as $item)
            <tr class="hover:bg-slate-800/40 transition-colors group">
              <!-- Container ID & Date -->
              <td class="py-4 px-6">
                <a href="{{ route('profitability.show', $item) }}" class="font-mono font-bold text-white group-hover:text-cyan-400 transition-colors flex items-center gap-2">
                  <i class="fa-solid fa-box text-cyan-500/70 text-xs"></i>
                  <span>{{ $item->container_id }}</span>
                </a>
                <span class="block text-[11px] text-slate-400 mt-0.5">
                  Arribo: {{ $item->arrival_date->format('d/m/Y') }}
                </span>
              </td>

              <!-- Estado Económico -->
              <td class="py-4 px-4 text-center">
                @if($item->economic_status === 'cerrado')
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono bg-rose-500/10 text-rose-400 border border-rose-500/30">
                    <i class="fa-solid fa-lock text-[9px] mr-1"></i>Cerrado
                  </span>
                @elseif($item->economic_status === 'en_revision')
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    <i class="fa-solid fa-magnifying-glass text-[9px] mr-1"></i>Revisión
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono bg-blue-500/10 text-blue-400 border border-blue-500/30">
                    <i class="fa-solid fa-door-open text-[9px] mr-1"></i>Abierto
                  </span>
                @endif
              </td>

              <!-- Transitaria -->
              <td class="py-4 px-4 font-semibold text-slate-300">
                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 font-mono text-[11px]">
                  {{ $item->transitaria }}
                </span>
              </td>

              <!-- Weights -->
              <td class="py-4 px-4 text-right font-mono">
                <strong class="text-white">{{ number_format($item->kg_total, 0) }}</strong> kg
                <span class="block text-[10px] text-slate-500">
                  Occ: {{ number_format($item->kg_occidente, 0) }} | Cen: {{ number_format($item->kg_centro, 0) }} | Ori: {{ number_format($item->kg_oriente, 0) }}
                </span>
              </td>

              <!-- Revenues -->
              <td class="py-4 px-4 text-right font-mono text-slate-200">
                ${{ number_format($item->revenue_total, 2) }}
              </td>

              <!-- Variable Costs -->
              <td class="py-4 px-4 text-right font-mono text-slate-400">
                ${{ number_format($item->cost_variable_total, 2) }}
              </td>

              <!-- Contribution Margin -->
              <td class="py-4 px-4 text-right font-mono">
                <strong class="text-cyan-300">${{ number_format($item->contribution_margin, 2) }}</strong>
                <span class="block text-[10px] text-slate-500">{{ $item->contribution_margin_pct }}%</span>
              </td>

              <!-- Fixed Costs Assigned -->
              <td class="py-4 px-4 text-right font-mono text-slate-400">
                ${{ number_format($item->fixed_costs_assigned_total, 2) }}
              </td>

              <!-- Net Profit -->
              <td class="py-4 px-4 text-right font-mono">
                <strong class="{{ $item->net_profit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                  ${{ number_format($item->net_profit, 2) }}
                </strong>
                <span class="block text-[10px] text-slate-500">{{ $item->net_margin_pct }}% neto</span>
              </td>

              <!-- Verdict Badge -->
              <td class="py-4 px-4 text-center">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase font-mono {{ $item->verdict_badge_class }}">
                  @if($item->decision_verdict === 'FACTIBLE')
                    🟢 Factible
                  @elseif($item->decision_verdict === 'MARGINAL')
                    🟡 Marginal
                  @else
                    🔴 Déficit
                  @endif
                </span>
              </td>

              <!-- Actions -->
              <td class="py-4 px-6 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('profitability.show', $item) }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition-colors" title="Ver Análisis Completo">
                    <i class="fa-solid fa-eye text-xs"></i>
                  </a>
                  <a href="{{ route('profitability.csv', $item) }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-colors" title="Descargar CSV">
                    <i class="fa-solid fa-file-csv text-xs"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="py-12 text-center text-slate-500">
                <i class="fa-solid fa-calculator text-3xl mb-3 block opacity-40"></i>
                <span>No se encontraron análisis de contenedores registrados con los filtros aplicados.</span>
                <div class="mt-4">
                  <a href="{{ route('profitability.create') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs rounded-xl inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Evaluar Primer Contenedor</span>
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($analyses->hasPages())
      <div class="p-4 border-t border-slate-800 bg-slate-950/40">
        {{ $analyses->links() }}
      </div>
    @endif
  </div>

</div>

<!-- Chart.js CDN & Inicializadores de Gráficos Financieros -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Chart 1 (6.5.2): Evolución Económica Mensual
  const ctxMonthly = document.getElementById('monthlyEvolutionChart')?.getContext('2d');
  if (ctxMonthly) {
    const labels = {!! json_encode($monthlyEvolution['labels']) !!};
    const revenues = {!! json_encode($monthlyEvolution['revenues']) !!};
    const costs = {!! json_encode($monthlyEvolution['costs']) !!};
    const netProfits = {!! json_encode($monthlyEvolution['net_profits']) !!};

    new Chart(ctxMonthly, {
      type: 'bar',
      data: {
        labels: labels.length ? labels : ['Sin datos'],
        datasets: [
          {
            label: 'Ingresos Facturados ($)',
            data: revenues.length ? revenues : [0],
            backgroundColor: 'rgba(6, 182, 212, 0.75)',
            borderColor: '#06b6d4',
            borderWidth: 1,
            borderRadius: 6,
            order: 2
          },
          {
            label: 'Costes Totales ($)',
            data: costs.length ? costs : [0],
            backgroundColor: 'rgba(244, 63, 94, 0.75)',
            borderColor: '#f43f5e',
            borderWidth: 1,
            borderRadius: 6,
            order: 3
          },
          {
            label: 'Utilidad Neta ($)',
            data: netProfits.length ? netProfits : [0],
            type: 'line',
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderWidth: 3,
            tension: 0.35,
            pointRadius: 5,
            pointHoverRadius: 7,
            order: 1
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { color: '#94a3b8', font: { size: 11, weight: 'bold' }, padding: 15 }
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            borderColor: 'rgba(51, 65, 85, 0.6)',
            borderWidth: 1,
            padding: 10,
            callbacks: {
              label: function (ctx) {
                return ctx.dataset.label + ': $' + Number(ctx.raw).toLocaleString('en-US', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        scales: {
          x: {
            grid: { color: 'rgba(51, 65, 85, 0.25)' },
            ticks: { color: '#94a3b8', font: { family: 'monospace', size: 10 } }
          },
          y: {
            grid: { color: 'rgba(51, 65, 85, 0.25)' },
            ticks: {
              color: '#94a3b8',
              font: { family: 'monospace', size: 10 },
              callback: function (val) { return '$' + Number(val).toLocaleString(); }
            }
          }
        }
      }
    });
  }

  // Chart 2 (6.5.3): Estructura de Costes por Etapa
  const ctxCost = document.getElementById('costStructureChart')?.getContext('2d');
  if (ctxCost) {
    const costData = [
      {{ $costStructure['puerto'] }},
      {{ $costStructure['transitaria'] }},
      {{ $costStructure['distribucion'] }},
      {{ $costStructure['comisiones'] }},
      {{ $costStructure['fijos_asignados'] }},
      {{ $costStructure['tributos'] }}
    ];

    const hasData = costData.some(v => v > 0);

    new Chart(ctxCost, {
      type: 'doughnut',
      data: {
        labels: ['Puerto Mariel', 'Transitaria', 'Distribución Reg.', 'Comisiones Gest.', 'Fijos Asignados', 'Tributos Ley 113/Util.'],
        datasets: [{
          data: hasData ? costData : [1],
          backgroundColor: hasData ? [
            '#06b6d4',
            '#3b82f6',
            '#f59e0b',
            '#a855f7',
            '#64748b',
            '#ef4444'
          ] : ['#334155'],
          borderColor: '#0f172a',
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#94a3b8', font: { size: 10 }, boxWidth: 10, padding: 8 }
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            borderColor: 'rgba(51, 65, 85, 0.6)',
            borderWidth: 1,
            callbacks: {
              label: function (ctx) {
                return ctx.label + ': $' + Number(ctx.raw).toLocaleString('en-US', { minimumFractionDigits: 2 });
              }
            }
          }
        }
      }
    });
  }
});
</script>
@endsection
