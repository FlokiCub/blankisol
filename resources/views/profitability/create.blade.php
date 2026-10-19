@extends('layouts.app', ['title' => 'Evaluar Rentabilidad de Contenedor'])

@section('page_title', 'Evaluación Económica de Contenedor — Modelo 4 Etapas')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('profitability.index') }}" class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
      <div>
        <h3 class="text-2xl font-black text-white">Evaluación de Factibilidad de Contenedor</h3>
        <p class="text-xs text-slate-400">Introduce la distribución de carga y parámetros operativos para calcular el margen de contribución y viabilidad.</p>
      </div>
    </div>
  </div>

  @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-950/50 border border-rose-500/40 text-rose-200 text-xs space-y-1">
      <strong class="block font-bold">Por favor corrige los siguientes errores:</strong>
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('profitability.store') }}" class="space-y-6">
    @csrf

    <!-- Bloque 1: Identificación y Carga Regional -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-box text-cyan-400"></i>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">1. Identificación y Distribución de Carga por Zona</h4>
        </div>
        <span class="text-[11px] font-mono text-cyan-400 font-bold bg-cyan-500/10 px-2.5 py-0.5 rounded-full border border-cyan-500/30">
          Tarifa Base: ${{ number_format($tariff, 2) }} USD/kg
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Container ID -->
        <div>
          <label for="container_id" class="block text-xs font-semibold text-slate-300 mb-1">
            Identificador del Contenedor <span class="text-rose-400">*</span>
          </label>
          <input 
            type="text" 
            id="container_id" 
            name="container_id" 
            required 
            value="{{ old('container_id', 'CONT-2026-' . strtoupper(Str::random(4))) }}"
            placeholder="Ej: CONT-01 o MSCU1234567" 
            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white uppercase font-mono font-bold focus:ring-2 focus:ring-cyan-500"
          />
        </div>

        <!-- Arrival Date -->
        <div>
          <label for="arrival_date" class="block text-xs font-semibold text-slate-300 mb-1">
            Fecha de Arribo al Puerto <span class="text-rose-400">*</span>
          </label>
          <input 
            type="date" 
            id="arrival_date" 
            name="arrival_date" 
            required 
            value="{{ old('arrival_date', now()->toDateString()) }}"
            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono focus:ring-2 focus:ring-cyan-500"
          />
        </div>

        <!-- Transitaria -->
        <div>
          <label for="transitaria" class="block text-xs font-semibold text-slate-300 mb-1">
            Transitaria Responsable <span class="text-rose-400">*</span>
          </label>
          <select 
            id="transitaria" 
            name="transitaria" 
            required 
            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-semibold focus:ring-2 focus:ring-cyan-500"
          >
            <option value="ENCI" {{ old('transitaria') === 'ENCI' ? 'selected' : '' }}>ENCI</option>
            <option value="A.V" {{ old('transitaria') === 'A.V' ? 'selected' : '' }}>Aerovaradero (A.V)</option>
            <option value="OTRA" {{ old('transitaria') === 'OTRA' ? 'selected' : '' }}>Otra Transitaria</option>
          </select>
        </div>
      </div>

      <!-- Pesos por Región -->
      <div class="pt-4 border-t border-slate-800/80">
        <label class="block text-xs font-bold text-slate-200 mb-3">
          Kilogramos de Carga por Región de Destino:
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Occidente -->
          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
              <span class="font-bold text-slate-300">Occidente</span>
              <span class="text-[10px] text-cyan-400">Habana / Artemisa / Mayabeque</span>
            </div>
            <div class="relative">
              <input 
                type="number" 
                step="0.01" 
                min="0" 
                id="kg_occidente" 
                name="kg_occidente" 
                required 
                value="{{ old('kg_occidente', '5200') }}"
                oninput="updateTotalKg()"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white font-mono font-bold focus:ring-2 focus:ring-cyan-500"
              />
              <span class="absolute right-3.5 top-3 text-xs text-slate-500">kg</span>
            </div>
          </div>

          <!-- Centro -->
          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
              <span class="font-bold text-slate-300">Centro</span>
              <span class="text-[10px] text-blue-400">Matanzas / VC / SS / Cienfuegos</span>
            </div>
            <div class="relative">
              <input 
                type="number" 
                step="0.01" 
                min="0" 
                id="kg_centro" 
                name="kg_centro" 
                required 
                value="{{ old('kg_centro', '2600') }}"
                oninput="updateTotalKg()"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white font-mono font-bold focus:ring-2 focus:ring-blue-500"
              />
              <span class="absolute right-3.5 top-3 text-xs text-slate-500">kg</span>
            </div>
          </div>

          <!-- Oriente -->
          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
              <span class="font-bold text-slate-300">Oriente</span>
              <span class="text-[10px] text-purple-400">Camagüey / Holguín / Santiago</span>
            </div>
            <div class="relative">
              <input 
                type="number" 
                step="0.01" 
                min="0" 
                id="kg_oriente" 
                name="kg_oriente" 
                required 
                value="{{ old('kg_oriente', '2300') }}"
                oninput="updateTotalKg()"
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white font-mono font-bold focus:ring-2 focus:ring-purple-500"
              />
              <span class="absolute right-3.5 top-3 text-xs text-slate-500">kg</span>
            </div>
          </div>
        </div>

        <!-- Total Kg Indicator Bar -->
        <div class="mt-4 p-3.5 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
          <span class="text-slate-400 font-semibold">Carga Total Declarada:</span>
          <span class="font-mono text-base font-black text-white" id="total_kg_display">10,100.00 kg</span>
        </div>
      </div>
    </div>

    <!-- Bloque 2: Parámetros Operativos Específicos (Opcional / Ajustes Finos) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-sliders text-amber-400"></i>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">2. Ajustes Operativos y Combustible Real (Opcional)</h4>
        </div>
        <span class="text-[11px] text-slate-500">Si se dejan vacíos, se aplicarán las normas técnicas de consumo</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <!-- Diésel Price Override -->
        <div>
          <label for="custom_diesel_price" class="block font-semibold text-slate-300 mb-1">
            Precio Diésel (USD/L)
          </label>
          <input 
            type="number" 
            step="0.01" 
            min="0.5" 
            id="custom_diesel_price" 
            name="custom_diesel_price" 
            value="{{ old('custom_diesel_price', $dieselPrice) }}"
            placeholder="2.00" 
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
          <p class="text-[10px] text-slate-500 mt-1">Oficial CUPET 2026: $2.00 USD/L</p>
        </div>

        <!-- Viajes al Puerto -->
        <div>
          <label for="trips_port" class="block font-semibold text-slate-300 mb-1">
            Viajes al Puerto (Mariel)
          </label>
          <input 
            type="number" 
            step="0.1" 
            min="0" 
            id="trips_port" 
            name="trips_port" 
            value="{{ old('trips_port', '1.0') }}"
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
          <p class="text-[10px] text-slate-500 mt-1">Norma estándar: 1 viaje por contenedor</p>
        </div>

        <!-- Litros Diésel Puerto -->
        <div>
          <label for="liters_port" class="block font-semibold text-slate-300 mb-1">
            Litros Diésel Puerto Aplicados
          </label>
          <input 
            type="number" 
            step="0.1" 
            min="0" 
            id="liters_port" 
            name="liters_port" 
            value="{{ old('liters_port', '60.0') }}"
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
          <p class="text-[10px] text-slate-500 mt-1">Norma: 60 L (180 km Mariel)</p>
        </div>

        <!-- Jornales de Desagrupe Transitaria -->
        <div>
          <label for="labor_transitaria" class="block font-semibold text-slate-300 mb-1">
            Jornales de Desagrupe Transitaria
          </label>
          <input 
            type="number" 
            step="1" 
            min="0" 
            id="labor_transitaria" 
            name="labor_transitaria" 
            value="{{ old('labor_transitaria') }}"
            placeholder="Automático (1000 kg/jornal)" 
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
        </div>

        <!-- Estadía / Almacenaje Transitaria -->
        <div>
          <label for="storage_transitaria" class="block font-semibold text-slate-300 mb-1">
            Estadía / Almacenaje Extra (USD)
          </label>
          <input 
            type="number" 
            step="0.01" 
            min="0" 
            id="storage_transitaria" 
            name="storage_transitaria" 
            value="{{ old('storage_transitaria', '0') }}"
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
        </div>

        <!-- Otros Gastos Directos -->
        <div>
          <label for="other_direct_costs" class="block font-semibold text-slate-300 mb-1">
            Otros Gastos Directos (USD)
          </label>
          <input 
            type="number" 
            step="0.01" 
            min="0" 
            id="other_direct_costs" 
            name="other_direct_costs" 
            value="{{ old('other_direct_costs', '0') }}"
            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono"
          />
        </div>
      </div>
    </div>

    <!-- Botón de Envío -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('profitability.index') }}" class="px-5 py-3 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold transition-all">
        Cancelar
      </a>

      <button type="submit" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-2xl shadow-cyan-950/60 active:scale-98 transition-all flex items-center gap-2.5">
        <i class="fa-solid fa-chart-pie text-sm"></i>
        <span>Calcular Rentabilidad & Emitir Veredicto</span>
      </button>
    </div>
  </form>

</div>

<script>
  function updateTotalKg() {
    const occ = parseFloat(document.getElementById('kg_occidente').value) || 0;
    const cen = parseFloat(document.getElementById('kg_centro').value) || 0;
    const ori = parseFloat(document.getElementById('kg_oriente').value) || 0;
    const total = occ + cen + ori;
    document.getElementById('total_kg_display').textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg';
  }
  document.addEventListener('DOMContentLoaded', updateTotalKg);
</script>
@endsection
