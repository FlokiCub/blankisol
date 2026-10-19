@extends('layouts.app', ['title' => 'Inspección Aduanal y Semáforos Portuarios'])

@section('page_title', 'Inspección Aduanal — Semáforos de Permanencia Mariel')

@section('content')
<div class="space-y-6">
    <!-- Header Banner Ejecutivo -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-amber-950/80 via-slate-900 to-slate-900 border border-amber-500/20 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold mb-2 border border-amber-500/30">
                <i class="fa-solid fa-anchor text-amber-400"></i>
                <span>Recinto Portuario Mariel &bull; Control Fiscal & Aforo</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Inspección Aduanal & Semáforos 48h / 72h</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
                Monitoreo estricto de permanencia portuaria, libramientos y mitigación de cuellos de botella con alertas preventivas y críticas.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Puesto: {{ auth()->user()->name }} ({{ auth()->user()->role->label() }})</span>
            </span>
        </div>
    </div>

    <!-- KPI Semáforos Portuarios con Micro-Interacciones -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total en Puerto -->
        <a href="{{ route('customs.index') }}" class="p-5 rounded-3xl glass-card {{ empty($semaphoreFilter) ? 'border-brand-500 ring-2 ring-brand-500/30 bg-slate-900/90' : 'border-slate-800' }} hover:border-slate-700 transition-all group shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total en Recinto</span>
                <span class="p-2.5 rounded-2xl bg-slate-800 text-brand-400 group-hover:scale-110 transition-transform"><i class="fa-solid fa-boxes-stacked"></i></span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black text-white font-mono">{{ $metrics['total_in_customs'] }}</span>
                <span class="text-xs text-slate-400">bultos</span>
            </div>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">Equipos en proceso de aforo</p>
        </a>

        <!-- Normal (<48h) -->
        <a href="{{ route('customs.index', ['semaphore' => 'green']) }}" class="p-5 rounded-3xl glass-card {{ $semaphoreFilter === 'green' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-950/20' : 'border-slate-800' }} hover:border-emerald-500/40 transition-all group shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Flujo Normal (&lt;48h)</span>
                </span>
                <span class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform"><i class="fa-solid fa-circle-check"></i></span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-400 font-mono">{{ $metrics['green_count'] }}</span>
                <span class="text-xs text-slate-400">en tiempo</span>
            </div>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">Permanencia estándar</p>
        </a>

        <!-- Preventivo (48h-72h) -->
        <a href="{{ route('customs.index', ['semaphore' => 'yellow']) }}" class="p-5 rounded-3xl glass-card {{ $semaphoreFilter === 'yellow' ? 'border-amber-500 ring-2 ring-amber-500/30 bg-amber-950/20' : 'border-slate-800' }} hover:border-amber-500/40 transition-all group shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Alerta (48h - 72h)</span>
                </span>
                <span class="p-2.5 rounded-2xl bg-amber-500/10 text-amber-400 group-hover:scale-110 transition-transform"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-400 font-mono">{{ $metrics['yellow_count'] }}</span>
                <span class="text-xs text-slate-400">alerta</span>
            </div>
            <p class="mt-1 text-[11px] text-slate-500 font-medium">Riesgo de congestión</p>
        </a>

        <!-- Crítico (>72h) -->
        <a href="{{ route('customs.index', ['semaphore' => 'red']) }}" class="p-5 rounded-3xl glass-card {{ $semaphoreFilter === 'red' ? 'border-rose-500 ring-2 ring-rose-500/30 bg-rose-950/20' : 'border-slate-800' }} hover:border-rose-500/40 transition-all group shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    <span>Crítico (&gt;72h)</span>
                </span>
                <span class="p-2.5 rounded-2xl bg-rose-500/10 text-rose-400 group-hover:scale-110 transition-transform"><i class="fa-solid fa-radiation"></i></span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-black text-rose-400 font-mono">{{ $metrics['red_count'] }}</span>
                <span class="text-xs text-rose-400/80">retención</span>
            </div>
            <p class="mt-1 text-[11px] text-rose-400/70 font-medium">Prioridad Director / Auditor</p>
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="glass-card p-4 rounded-3xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('customs.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2 flex-1 max-w-md">
            <input type="hidden" name="semaphore" value="{{ $semaphoreFilter }}">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por PIN, VIN, marca o modelo..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all uppercase font-mono">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold rounded-2xl shadow-md shadow-brand-900/40 transition-all">
                Filtrar
            </button>
            @if($search || $semaphoreFilter)
                <a href="{{ route('customs.index') }}" class="p-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-2xl transition-all" title="Limpiar filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-400">
            Total en cola: <strong class="text-white font-mono">{{ $equipments->total() }}</strong> equipos
        </div>
    </div>

    <!-- Tabla de Equipos con Semáforos -->
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/90 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Semáforo / Tiempo en Muelle</th>
                        <th class="px-5 py-4">Equipo / PIN / VIN</th>
                        <th class="px-5 py-4">Régimen Fiscal</th>
                        <th class="px-5 py-4">Estado Aduanal</th>
                        <th class="px-5 py-4">Valor Declarado</th>
                        <th class="px-5 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($equipments as $eq)
                        @php
                            $sem = $eq->getCustomsSemaphore();
                            $hours = $eq->getCustomsStayHours();
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- Semáforo -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($sem === 'red')
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-extrabold bg-rose-500/15 text-rose-300 border border-rose-500/40">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                        <span class="font-mono">{{ $hours }}h</span>
                                        <span>(Retención Crítica)</span>
                                    </div>
                                @elseif($sem === 'yellow')
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/40">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        <span class="font-mono">{{ $hours }}h</span>
                                        <span>(Alerta 48-72h)</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/40">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span class="font-mono">{{ $hours }}h</span>
                                        <span>(En Tiempo &lt;48h)</span>
                                    </div>
                                @endif
                                <div class="text-[11px] text-slate-500 mt-1 font-mono">
                                    Arribo: {{ $eq->customs_entry_at ? $eq->customs_entry_at->format('d/m/Y H:i') : $eq->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <!-- Equipo / PIN -->
                            <td class="px-5 py-4">
                                <div class="font-bold text-white text-sm">{{ $eq->brand }} {{ $eq->model }}</div>
                                <div class="text-xs font-mono text-brand-400 font-bold">
                                    <a href="{{ route('tracking.show', $eq->tracking_pin) }}" target="_blank" class="hover:underline">
                                        {{ $eq->tracking_pin }}
                                    </a>
                                </div>
                                <div class="text-[11px] font-mono text-slate-500">{{ $eq->vin_serial }}</div>
                            </td>

                            <!-- Tipo / Negocio -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $eq->business_type->value === 'B2B' ? 'bg-purple-500/15 text-purple-300 border border-purple-500/30' : 'bg-blue-500/15 text-blue-300 border border-blue-500/30' }}">
                                    {{ $eq->business_type->value }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1">{{ $eq->equipment_type->label() }}</div>
                            </td>

                            <!-- Estado Aduana -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $eq->current_status->badgeClass() }}">
                                    {{ $eq->current_status->label() }}
                                </span>
                                @if($eq->customs_status === 'held_in_customs')
                                    <div class="mt-1 text-[11px] font-bold text-rose-400 flex items-center gap-1">
                                        <i class="fa-solid fa-hand"></i> 
                                        <span>Retenido en Aduana</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Valor -->
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-xs">
                                <div class="font-bold text-white">${{ number_format($eq->declared_value_usd, 2) }} USD</div>
                                @if($eq->duty_paid_abroad)
                                    <span class="text-[10px] text-emerald-400 font-semibold block">✓ Prepagado $0.00</span>
                                @else
                                    <span class="text-[10px] text-amber-400 block font-semibold">Arancel: ${{ number_format($eq->duty_amount_usd, 2) }}</span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('customs.show', $eq) }}" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-all" title="Ver Expediente">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if($eq->current_status === \App\Enums\EquipmentStatus::PORT_ARRIVAL)
                                        <form action="{{ route('customs.start-inspection', $eq) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-md shadow-amber-900/40" title="Iniciar Inspección Física">
                                                <i class="fa-solid fa-magnifying-glass-arrow-right mr-1"></i> Aforar
                                            </button>
                                        </form>
                                    @endif

                                    @if($eq->current_status === \App\Enums\EquipmentStatus::CUSTOMS_INSPECTION)
                                        <form action="{{ route('customs.clear', $eq) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-900/40" title="Aprobar Libramiento Aduanal">
                                                <i class="fa-solid fa-check-double mr-1"></i> Liberar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-circle-check text-4xl text-slate-700 mb-3 block"></i>
                                No hay bultos pendientes con los criterios seleccionados en el recinto aduanal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($equipments->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/50">
                {{ $equipments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
