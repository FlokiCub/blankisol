@extends('layouts.app', ['title' => 'Parámetros Económicos y Tarifas'])

@section('page_title', 'Torre Financiera — Parámetros Económicos & Tarifas Históricas')

@section('content')
<div class="space-y-6">

  <!-- Header Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl glass-card border border-slate-800 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div class="space-y-2">
      <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-500/10 text-cyan-400 border-cyan-500/30">
          <i class="fa-solid fa-sliders mr-1.5"></i>Hoja PARÁMETROS & TARIFAS
        </span>
        <span class="text-xs font-mono text-slate-400">Vigencia Temporal & Trazabilidad</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
        <span>Parámetros del Sistema y Tarifas Históricas</span>
      </h3>
      <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
        Gestión de parámetros normativos (Ley 113, normas de consumo diésel, peajes) y publicación de nuevas versiones de tarifas fletes y combustible sin alterar cálculos históricos cerrados.
      </p>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-wrap items-center gap-3">
      <button type="button" onclick="document.getElementById('newRateModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
        <i class="fa-solid fa-plus"></i>
        <span>Publicar Nueva Versión Tarifas</span>
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

  <!-- Versión de Tarifas Activa -->
  @if($activeRate)
  <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-cyan-950/20 to-slate-900 border border-cyan-800/40 shadow-xl space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
          Vigente Actualmente
        </span>
        <h4 class="text-base font-black text-white">Tarifario Activo — Desde {{ $activeRate->effective_from->format('d/m/Y') }}</h4>
      </div>
      <span class="text-xs font-mono text-slate-400">{{ $activeRate->support_document_note ?: 'Resolución de Tarifas y Combustible' }}</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-2">
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Diésel CUPET</span>
        <span class="text-lg font-black font-mono text-cyan-400">${{ number_format($activeRate->diesel_price_usd_l, 2) }} <span class="text-[10px] font-sans text-slate-500">/L</span></span>
      </div>
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Tarifa Occidente</span>
        <span class="text-lg font-black font-mono text-white">${{ number_format($activeRate->tariff_occidente_usd_kg, 2) }} <span class="text-[10px] font-sans text-slate-500">/kg</span></span>
      </div>
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Tarifa Centro</span>
        <span class="text-lg font-black font-mono text-blue-400">${{ number_format($activeRate->tariff_centro_usd_kg, 2) }} <span class="text-[10px] font-sans text-slate-500">/kg</span></span>
      </div>
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Tarifa Oriente</span>
        <span class="text-lg font-black font-mono text-purple-400">${{ number_format($activeRate->tariff_oriente_usd_kg, 2) }} <span class="text-[10px] font-sans text-slate-500">/kg</span></span>
      </div>
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Cita Puerto</span>
        <span class="text-lg font-black font-mono text-amber-400">${{ number_format($activeRate->port_appointment_usd, 2) }}</span>
      </div>
      <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="text-[10px] text-slate-400 block font-semibold">Desgaste Vehículo</span>
        <span class="text-lg font-black font-mono text-emerald-400">${{ number_format($activeRate->vehicle_wear_usd_km, 2) }} <span class="text-[10px] font-sans text-slate-500">/km</span></span>
      </div>
    </div>
  </div>
  @endif

  <!-- Formulario de Parámetros Económicos -->
  <form action="{{ route('profitability.management.parameters.update') }}" method="POST">
    @csrf
    <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
      <div class="p-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-gear text-cyan-400"></i>
            <span>Parámetros Operativos y Normativos del Sistema</span>
          </h4>
          <p class="text-xs text-slate-400 mt-0.5">Tasas impositivas (Ley 113), normas técnicas de combustible por ruta y coeficientes de cálculo.</p>
        </div>

        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg active:scale-95 transition-all flex items-center gap-2">
          <i class="fa-solid fa-floppy-disk"></i>
          <span>Guardar Cambios de Parámetros</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-4">Código</th>
              <th class="py-3 px-4">Descripción del Parámetro</th>
              <th class="py-3 px-4 text-center">Categoría</th>
              <th class="py-3 px-4 text-center">Unidad</th>
              <th class="py-3 px-4 text-right w-44">Valor Vigente</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
            @foreach($parameters as $param)
              <tr class="hover:bg-slate-800/30 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-white">{{ $param->code }}</td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-200">{{ $param->description }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                    {{ $param->category }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center font-mono text-slate-400">
                  {{ $param->unit }}
                </td>
                <td class="py-3 px-4 text-right">
                  <input 
                    type="text" 
                    name="parameters[{{ $param->code }}]" 
                    value="{{ $param->value }}" 
                    class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-cyan-400 font-mono text-right text-xs focus:ring-1 focus:ring-cyan-500"
                  >
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </form>

  <!-- Historial de Versiones de Tarifas -->
  <div class="rounded-3xl glass-card border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800/80">
      <h4 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-clock-rotate-left text-blue-400"></i>
        <span>Historial de Versiones Tarifarias</span>
      </h4>
      <p class="text-xs text-slate-400 mt-0.5">Cada análisis económico congelado conserva la tarifa exacta de su fecha de arribo sin ser modificado por nuevas versiones.</p>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-900/60 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-4">Vigente Desde</th>
            <th class="py-3 px-4 text-right">Diésel ($/L)</th>
            <th class="py-3 px-4 text-right">Occidente ($/kg)</th>
            <th class="py-3 px-4 text-right">Centro ($/kg)</th>
            <th class="py-3 px-4 text-right">Oriente ($/kg)</th>
            <th class="py-3 px-4 text-right">Cita Puerto ($)</th>
            <th class="py-3 px-4 text-right">Desgaste ($/km)</th>
            <th class="py-3 px-4">Documento / Nota</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-medium text-slate-300">
          @foreach($rates as $r)
            <tr class="hover:bg-slate-800/30 transition-colors">
              <td class="py-3 px-4 font-mono font-bold text-white">{{ $r->effective_from->format('d/m/Y') }}</td>
              <td class="py-3 px-4 text-right font-mono text-cyan-400">${{ number_format($r->diesel_price_usd_l, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-300">${{ number_format($r->tariff_occidente_usd_kg, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-300">${{ number_format($r->tariff_centro_usd_kg, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-300">${{ number_format($r->tariff_oriente_usd_kg, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-slate-400">${{ number_format($r->port_appointment_usd, 2) }}</td>
              <td class="py-3 px-4 text-right font-mono text-emerald-400">${{ number_format($r->vehicle_wear_usd_km, 2) }}</td>
              <td class="py-3 px-4 text-slate-400">{{ $r->support_document_note ?: 'Resolución de Dirección' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Nueva Versión Tarifas -->
  <div id="newRateModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-xl w-full shadow-2xl space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-800">
        <h3 class="text-base font-black text-white flex items-center gap-2">
          <i class="fa-solid fa-plus text-cyan-400"></i> Publicar Nueva Versión Tarifaria
        </h3>
        <button type="button" onclick="document.getElementById('newRateModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </div>

      <form action="{{ route('profitability.management.rates.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-bold text-slate-300 mb-1">Fecha de Entrada en Vigor</label>
          <input type="date" name="effective_from" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Tarifa Occ ($/kg)</label>
            <input type="number" step="0.01" name="tariff_occidente_usd_kg" value="0.80" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Tarifa Cen ($/kg)</label>
            <input type="number" step="0.01" name="tariff_centro_usd_kg" value="0.80" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Tarifa Ori ($/kg)</label>
            <input type="number" step="0.01" name="tariff_oriente_usd_kg" value="0.80" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Diésel ($/L)</label>
            <input type="number" step="0.01" name="diesel_price_usd_l" value="1.10" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Cita Puerto ($)</label>
            <input type="number" step="0.01" name="port_appointment_usd" value="120.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Dieta Chofer ($)</label>
            <input type="number" step="0.01" name="driver_diet_usd" value="50.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Turno Transitaria ($)</label>
            <input type="number" step="0.01" name="labor_shift_transitaria_usd" value="120.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Turno C. Transitorio ($)</label>
            <input type="number" step="0.01" name="labor_shift_transitory_center_usd" value="100.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Servicio ENCI ($)</label>
            <input type="number" step="0.01" name="transitaria_service_enci_usd" value="450.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Servicio Aerovaradero ($)</label>
            <input type="number" step="0.01" name="transitaria_service_av_usd" value="500.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-bold text-slate-300 mb-1">Comisión Gestor (%)</label>
            <input type="number" step="0.01" name="commercial_commission_pct" value="5.00" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Comisión Gestor ($/kg)</label>
            <input type="number" step="0.01" name="commercial_commission_usd_kg" value="0.04" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
          <div>
            <label class="block font-bold text-slate-300 mb-1">Desgaste Vehículo ($/km)</label>
            <input type="number" step="0.01" name="vehicle_wear_usd_km" value="0.12" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-300 mb-1">Documento Respaldo / Resolución</label>
          <input type="text" name="support_document_note" placeholder="Ej: Resolución Finanzas No. 04/2026" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
          <button type="button" onclick="document.getElementById('newRateModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">Cancelar</button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-extrabold shadow-md">Publicar Nueva Versión</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
