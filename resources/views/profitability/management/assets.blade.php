@extends('layouts.app', ['title' => 'Gestión de Activos y Depreciación'])

@section('page_title', 'Torre Financiera — Activos Fijos & Depreciación Lineal')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-purple-500/10 text-purple-400 border-purple-500/30">
          <i class="fa-solid fa-truck-ramp-box mr-1.5"></i>Hoja ACTIVOS
        </span>
        <span class="text-xs font-mono text-slate-400">Depreciación Lineal Automática (CF10)</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Catálogo de Activos Fijos & Depreciación</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Gestión de equipos, camiones y semirremolques con cálculo de depreciación mensual lineal <code>(Adquisición − Residual) / (Años × 12)</code> vinculada directamente al concepto de costo fijo CF10 ($441.67 USD).
      </p>
    </div>

    <!-- Acciones Rápidas -->
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('profitability.management.assets') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-xs focus:ring-2 focus:ring-purple-500">
        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-400 font-bold text-xs border border-slate-700">
          <i class="fa-solid fa-filter"></i>
        </button>
      </form>

      <button type="button" onclick="document.getElementById('createAssetModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
        <i class="fa-solid fa-plus"></i>
        <span>Registrar Activo</span>
      </button>

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

  <!-- Resumen de Depreciación -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Total Activos Registrados</p>
      <p class="text-2xl font-black text-white font-mono mt-2">{{ $assets->count() }} <span class="text-xs text-slate-400 font-sans">unidades</span></p>
      <p class="text-[11px] text-slate-500 mt-0.5">{{ $assets->where('is_active', true)->count() }} activos operativos</p>
    </div>

    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
      <p class="text-xs text-slate-400 font-semibold">Valor Adquisición Total</p>
      <p class="text-2xl font-black text-indigo-400 font-mono mt-2">${{ number_format($assets->sum('acquisition_value'), 2) }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Valor residual acumulado: ${{ number_format($assets->sum('residual_value'), 2) }}</p>
    </div>

    <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-950/40 to-slate-900 border border-purple-800/40">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs text-purple-300 font-semibold">Depreciación Período {{ $selectedMonth }}</p>
          <p class="text-2xl font-black text-white font-mono mt-2">${{ number_format($totalMonthlyDeprec, 2) }}</p>
          <p class="text-[11px] text-purple-400/80 mt-0.5">Alimenta concepto CF10 en Gastos Fijos</p>
        </div>
        <form action="{{ route('profitability.management.assets.depreciation') }}" method="POST">
          @csrf
          <input type="hidden" name="year_month" value="{{ $selectedMonth }}">
          <button type="submit" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md active:scale-95 transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-rotate"></i>
            <span>Generar Asiento</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Tabla de Activos Fijos -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-list-check text-purple-400"></i>
          <span>Detalle de Activos y Amortización Lineal</span>
        </h4>
        <p class="text-xs text-slate-400 mt-0.5">Cálculo individual de amortización mensual teórica y estado de depreciación para el período.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">No. Activo</th>
            <th class="py-3 px-4">Descripción</th>
            <th class="py-3 px-4 text-center">Tipo</th>
            <th class="py-3 px-4 text-right">Adquisición ($)</th>
            <th class="py-3 px-4 text-right">Residual ($)</th>
            <th class="py-3 px-4 text-center">Vida Útil</th>
            <th class="py-3 px-4 text-right">Deprec. Mensual ($)</th>
            <th class="py-3 px-4 text-right">Asiento Mes ($)</th>
            <th class="py-3 px-4 text-center">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
          @foreach($assets as $asset)
            @php
              $entry = $asset->depreciationEntries->first();
              $theoretical = $asset->getTheoreticalMonthlyDepreciation();
            @endphp
            <tr class="hover:bg-slate-800/30 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-white">{{ $asset->asset_number }}</td>
              <td class="py-3 px-4 font-bold text-slate-200">{{ $asset->description }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                  {{ $asset->asset_type }}
                </span>
              </td>
              <td class="py-3 px-4 text-right font-mono text-slate-300">${{ number_format($asset->acquisition_value, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-400">${{ number_format($asset->residual_value, 2) }}</td>
              <td class="py-3 px-4 text-center font-mono">{{ $asset->useful_life_years }} años</td>
              <td class="py-3 px-4 text-right font-mono font-bold text-purple-400">${{ number_format($theoretical, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono font-bold {{ $entry ? 'text-emerald-400' : 'text-slate-500' }}">
                {{ $entry ? '$' . number_format($entry->monthly_depreciation, 2) : 'Sin generar' }}
              </td>
              <td class="py-3 px-4 text-center">
                @if($asset->is_active)
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Activo</span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">Inactivo</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Crear Activo -->
  <div id="createAssetModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <i class="fa-solid fa-plus text-purple-400"></i> Registrar Nuevo Activo Fijo
        </h3>
        <button type="button" onclick="document.getElementById('createAssetModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <form action="{{ route('profitability.management.assets.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Número / Código</label>
            <input type="text" name="asset_number" required placeholder="Ej: ACT-001" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Tipo de Activo</label>
            <select name="asset_type" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
              <option value="VEHICULO">Vehículo / Camión</option>
              <option value="REMOLQUE">Semirremolque</option>
              <option value="EQUIPO">Equipo / Montacargas</option>
              <option value="INMUEBLE">Inmueble / Instalación</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-300 mb-1">Descripción / Marca / Modelo</label>
          <input type="text" name="description" required placeholder="Ej: Camión Howo 15T Centro" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">F. Registro</label>
            <input type="date" name="registration_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Valor Compra ($)</label>
            <input type="number" step="0.01" name="acquisition_value" required placeholder="50000" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Valor Residual ($)</label>
            <input type="number" step="0.01" name="residual_value" required placeholder="5000" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Vida Útil (Años)</label>
            <input type="number" name="useful_life_years" required placeholder="10" min="1" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Mant. Mes ($)</label>
            <input type="number" step="0.01" name="monthly_maintenance" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Seguro Anual ($)</label>
            <input type="number" step="0.01" name="annual_insurance" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
          <button type="button" onclick="document.getElementById('createAssetModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">Cancelar</button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-extrabold shadow-md">Guardar Activo</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
