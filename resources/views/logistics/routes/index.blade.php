@extends('layouts.app', ['title' => 'Hojas de Ruta y Logística Regional'])

@section('page_title', 'Hojas de Ruta — Distribución y Traslados Regionales')

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-950/80 via-slate-900 to-slate-900 border border-blue-500/20 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold mb-2 border border-blue-500/30">
        <i class="fa-solid fa-truck-fast text-blue-400"></i>
        <span>Logística de Distribución Nacional Mariel &bull; HUBs Provinciales</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Hojas de Ruta y Convoys de Carga</h3>
      <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
        Gestión integral de traslados desde el <strong>Puerto del Mariel</strong> hacia los almacenes provinciales. Asigna choferes, camiones y bultos verificados con trazabilidad satelital.
      </p>
    </div>

    <!-- Quick Action & Stats -->
    <div class="flex items-center gap-3 flex-wrap">
      <a 
        href="{{ route('logistics.routes.create') }}" 
        class="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-brand-600 via-brand-500 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-xl shadow-brand-950/40 active:scale-98 transition-all flex items-center gap-2"
      >
        <i class="fa-solid fa-plus text-sm"></i>
        <span>Nueva Hoja de Ruta</span>
      </a>
    </div>
  </div>

  <!-- Metric Counters Row -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="p-5 rounded-3xl glass-card shadow-lg">
      <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">En Tránsito (Convoys)</span>
      <span class="text-3xl font-black text-blue-400 mt-1 block font-mono">{{ $inTransitCount }}</span>
      <span class="text-[11px] text-slate-500">Convoys en ruta nacional</span>
    </div>

    <div class="p-5 rounded-3xl glass-card shadow-lg">
      <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Bultos en Carretera</span>
      <span class="text-3xl font-black text-cyan-400 mt-1 block font-mono">{{ $packagesInTransitCount }}</span>
      <span class="text-[11px] text-slate-500">Equipos rodando a destino</span>
    </div>

    <div class="p-5 rounded-3xl glass-card shadow-lg">
      <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Listos para Cargar</span>
      <span class="text-3xl font-black text-emerald-400 mt-1 block font-mono">{{ $readyForDispatchCount }}</span>
      <span class="text-[11px] text-slate-500">Aprobados por aforo Mariel</span>
    </div>

    <div class="p-5 rounded-3xl glass-card shadow-lg">
      <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Hojas en Borrador</span>
      <span class="text-3xl font-black text-amber-400 mt-1 block font-mono">{{ $draftCount }}</span>
      <span class="text-[11px] text-slate-500">En proceso de estiba</span>
    </div>
  </div>

  <!-- Filters & Search -->
  <div class="glass-card p-4 rounded-3xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="GET" action="{{ route('logistics.routes.index') }}" class="flex items-center gap-3 w-full sm:w-auto flex-wrap">
      <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-brand-500 font-medium">
        <option value="">Todos los Estados</option>
        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador / En Carga</option>
        <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>En Tránsito</option>
        <option value="arrived_destination" {{ request('status') === 'arrived_destination' ? 'selected' : '' }}>Completadas en Destino</option>
      </select>

      <select name="destination_id" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-brand-500 font-medium">
        <option value="">Todos los Destinos Provinciales</option>
        @foreach($warehouses as $wh)
          <option value="{{ $wh->id }}" {{ request('destination_id') === $wh->id ? 'selected' : '' }}>{{ $wh->province }} — {{ $wh->name }}</option>
        @endforeach
      </select>

      @if(request()->hasAny(['status', 'destination_id']))
        <a href="{{ route('logistics.routes.index') }}" class="text-xs text-brand-400 hover:text-white px-3 py-1.5 rounded-xl bg-slate-800 transition-colors">
          <i class="fa-solid fa-rotate-left mr-1"></i> Limpiar
        </a>
      @endif
    </form>
    
    <div class="text-xs text-slate-400">
      Total de convoyes: <strong class="text-white font-mono">{{ $routes->total() }}</strong>
    </div>
  </div>

  <!-- Routes Table -->
  <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
      <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Expedientes de Convoys y Hojas de Ruta</h4>
      <span class="text-xs text-slate-500 font-mono">{{ $routes->total() }} registradas</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-950/90 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-800">
          <tr>
            <th class="py-3.5 px-4">Código / Ruta</th>
            <th class="py-3.5 px-4">Destino Provincial</th>
            <th class="py-3.5 px-4">Chofer & Camión</th>
            <th class="py-3.5 px-4 text-center">Bultos</th>
            <th class="py-3.5 px-4">Estado</th>
            <th class="py-3.5 px-4">Fecha Salida</th>
            <th class="py-3.5 px-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @forelse($routes as $route)
            <tr class="hover:bg-slate-800/40 transition-colors">
              <td class="py-4 px-4 font-mono font-bold text-white">
                <a href="{{ route('logistics.routes.show', $route) }}" class="text-brand-400 hover:underline">
                  #{{ $route->route_code }}
                </a>
              </td>
              <td class="py-4 px-4">
                <span class="font-bold text-white block">{{ $route->destinationWarehouse->province ?? 'N/A' }}</span>
                <span class="text-[11px] text-slate-400">{{ $route->destinationWarehouse->name ?? 'N/A' }}</span>
              </td>
              <td class="py-4 px-4">
                <span class="text-slate-200 font-semibold block">{{ $route->driver_name }}</span>
                <span class="text-[11px] font-mono text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">Chapa: {{ $route->truck_license_plate }}</span>
              </td>
              <td class="py-4 px-4 text-center font-bold text-white font-mono">
                <span class="px-3 py-1 rounded-full bg-slate-950 border border-slate-800 text-brand-300">
                  {{ $route->equipments->count() }}
                </span>
              </td>
              <td class="py-4 px-4">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $route->statusBadgeClass() }}">
                  {{ $route->statusLabel() }}
                </span>
              </td>
              <td class="py-4 px-4 text-slate-400 text-[11px] font-mono">
                {{ $route->dispatched_at ? $route->dispatched_at->format('d/m/Y H:i') : 'Pendiente salida' }}
              </td>
              <td class="py-4 px-4 text-right">
                <a href="{{ route('logistics.routes.show', $route) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-brand-300 font-bold text-xs transition-colors shadow-sm">
                  Ver Manifiesto ↗
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-10 text-center text-slate-500">
                No hay hojas de ruta registradas con los filtros actuales.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($routes->hasPages())
      <div class="p-4 border-t border-slate-800 bg-slate-950/50">
        {{ $routes->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
