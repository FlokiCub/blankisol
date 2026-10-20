@extends('shop.layouts.admin')

@section('page_title', 'Zonas & Tarifas de Entrega')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="glass-card rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-truck-ramp-box text-emerald-400"></i>
        <span>Zonas de Entrega & Tarifas Oficiales (USD)</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        Define áreas de cobertura geográfica por provincias y modalidades de despacho (Estándar, Express, Retiro en HUB).
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Zones and Rates List (Span 2) -->
    <div class="lg:col-span-2 space-y-6">
      @forelse($zones as $zone)
        <div class="glass-card rounded-3xl p-6 space-y-4">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 border-b border-slate-800">
            <div>
              <span class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
                <span>{{ $zone->name }}</span>
              </span>
              <div class="mt-1 flex flex-wrap gap-1">
                @if(!empty($zone->provinces) && is_array($zone->provinces))
                  @foreach($zone->provinces as $prov)
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                      {{ $prov }}
                    </span>
                  @endforeach
                @else
                  <span class="text-[11px] text-slate-500 italic">Cobertura global / sin provincias restringidas</span>
                @endif
              </div>
            </div>

            <!-- Botón para desplegar formulario de agregar tarifa -->
            <button onclick="document.getElementById('rate_form_{{ $zone->id }}').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors flex items-center gap-1.5 shrink-0">
              <i class="fa-solid fa-plus text-[10px]"></i>
              <span>Agregar Tarifa</span>
            </button>
          </div>

          <!-- Formulario inline para agregar tarifa a la zona (oculto por defecto) -->
          <div id="rate_form_{{ $zone->id }}" class="hidden p-4 rounded-2xl bg-slate-900/80 border border-slate-800 animate-fade-in">
            <h4 class="text-xs font-bold text-white mb-3">Nueva Modalidad de Tarifa para {{ $zone->name }}</h4>
            <form method="POST" action="{{ route('shop.admin.shipping.rates.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
              @csrf
              <input type="hidden" name="zone_id" value="{{ $zone->id }}">

              <div class="sm:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nombre Tarifa *</label>
                <input type="text" name="name" required placeholder="Ej. Entrega Estándar HUB Mariel" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
              </div>

              <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Precio (USD) *</label>
                <input type="number" step="0.01" min="0" name="price" required placeholder="15.00" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
              </div>

              <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Plazo Estimado</label>
                <input type="text" name="estimated_days" placeholder="2 a 4 días" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
              </div>

              <div class="sm:col-span-4 flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('rate_form_{{ $zone->id }}').classList.add('hidden')" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">
                  Cancelar
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold">
                  Guardar Tarifa
                </button>
              </div>
            </form>
          </div>

          <!-- Rates Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="text-slate-400 border-b border-slate-800/80">
                  <th class="pb-2.5 font-semibold">Modalidad de Envío</th>
                  <th class="pb-2.5 font-semibold text-center">Plazo Estimado</th>
                  <th class="pb-2.5 font-semibold text-right">Precio en USD</th>
                  <th class="pb-2.5 font-semibold text-right">Acción</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60">
                @forelse($zone->rates as $rate)
                  <tr class="hover:bg-slate-900/40 transition-colors">
                    <td class="py-2.5 font-semibold text-white">
                      {{ $rate->name }}
                    </td>
                    <td class="py-2.5 text-center text-slate-400 font-mono text-[11px]">
                      {{ $rate->estimated_days ?: 'No especificado' }}
                    </td>
                    <td class="py-2.5 text-right font-mono font-bold text-emerald-400">
                      ${{ number_format((float)$rate->price, 2) }} USD
                    </td>
                    <td class="py-2.5 text-right">
                      <form method="POST" action="{{ route('shop.admin.shipping.rates.destroy', $rate) }}" onsubmit="return confirm('¿Eliminar esta tarifa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 transition-colors" title="Eliminar tarifa">
                          <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="py-6 text-center text-slate-500 text-xs">
                      No hay tarifas configuradas para esta zona geográfica todavía. Haz clic en "Agregar Tarifa".
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @empty
        <div class="glass-card rounded-3xl p-12 text-center text-slate-500">
          <i class="fa-solid fa-map-location-dot text-3xl mb-2 text-slate-600 block"></i>
          <p class="font-medium">No hay zonas geográficas de entrega creadas todavía.</p>
          <p class="text-[11px] text-slate-500 mt-1">Crea tu primera zona de entrega con el formulario de la derecha.</p>
        </div>
      @endforelse
    </div>

    <!-- Right Column: Create Zone Form (Span 1) -->
    <div class="glass-card rounded-3xl p-6">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-emerald-400"></i>
        <span>Nueva Zona Geográfica</span>
      </h2>

      <form method="POST" action="{{ route('shop.admin.shipping.zones.store') }}" class="mt-4 space-y-4">
        @csrf

        <div>
          <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre de la Zona *</label>
          <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ej. Occidente (La Habana y Artemisa)" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="provinces" class="block text-xs font-semibold text-slate-300 mb-1.5">Provincias Incluidas (Separadas por coma)</label>
          <textarea name="provinces" id="provinces" rows="4" placeholder="La Habana, Artemisa, Mayabeque, Matanzas" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('provinces') }}</textarea>
          <p class="text-[10px] text-slate-500 mt-1">Escribe las provincias que pertenecen a esta zona tarifaria separadas por comas.</p>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40">
          Guardar Zona Geográfica
        </button>
      </form>
    </div>

  </div>

</div>
@endsection
