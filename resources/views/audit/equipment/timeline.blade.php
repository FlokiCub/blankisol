@extends('layouts.app')

@section('page_title', 'Trazabilidad Inmutable de Equipos — Auditoría')

@section('content')
<div class="space-y-6 animate-fade-in">

  <!-- Header & Search Bar -->
  <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 text-xs font-bold uppercase tracking-wider mb-2">
          <i class="fa-solid fa-timeline"></i> Trazabilidad e Historial Inmutable
        </div>
        <h2 class="text-2xl font-black text-white tracking-tight">Auditoría Completa de Bulto / Equipo</h2>
        <p class="text-sm text-slate-400">Inspecciona la cadena inmutable de custodia, inspecciones técnicas, movimientos de ruta y pagos.</p>
      </div>

      <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700">
        <i class="fa-solid fa-arrow-left"></i> Volver a Torre de Control
      </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('audit.equipment.timeline') }}" class="flex items-center gap-3 max-w-2xl">
      <div class="relative flex-1">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="q" value="{{ $search }}" placeholder="Ingresa PIN (ej. BK-78492), VIN o ID SolveCargo..." class="w-full bg-slate-800 border border-slate-700 rounded-2xl pl-11 pr-4 py-3 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-purple-500 outline-none shadow-inner uppercase">
      </div>
      <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl text-xs font-bold transition-all shadow-lg shadow-purple-600/30">
        Auditar Equipo
      </button>
    </form>
  </div>

  @if($equipment)
    <!-- Equipment Snapshot Card -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
          <div class="flex items-center gap-3">
            <h3 class="text-xl font-black text-white tracking-tight">{{ $equipment->brand }} {{ $equipment->model }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $equipment->current_status->badgeClass() }}">
              {{ $equipment->current_status->label() }}
            </span>
          </div>
          <p class="text-xs text-slate-400 mt-1">
            PIN: <strong class="text-indigo-400 font-mono text-sm">{{ $equipment->tracking_pin }}</strong> • 
            VIN: <span class="text-slate-300 font-mono">{{ $equipment->vin_serial }}</span> • 
            SolveCargo: <span class="text-slate-300 font-mono">{{ $equipment->solve_cargo_tracking_id ?? 'N/A' }}</span>
          </p>
        </div>

        <div class="text-right">
          <div class="text-xs text-slate-400">Ubicación Actual:</div>
          <div class="text-sm font-bold text-white">{{ $equipment->current_location_note ?? 'Puerto / Almacén' }}</div>
          <div class="text-xs text-slate-400 mt-0.5">{{ $equipment->currentWarehouse?->name ?? '' }}</div>
        </div>
      </div>

      <!-- Financial & Client Info Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 text-xs">
        <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
          <span class="text-slate-400 block mb-1">Cliente / Consignatario:</span>
          <strong class="text-white text-sm block">{{ $equipment->client_data['name'] ?? 'N/A' }}</strong>
          <span class="text-slate-400">{{ $equipment->client_data['phone'] ?? '' }}</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
          <span class="text-slate-400 block mb-1">Tipo de Negocio & Valor:</span>
          <strong class="text-indigo-400 text-sm block">{{ $equipment->business_type?->value === 'b2b' ? 'B2B (Empresa)' : 'B2C (Persona Natural)' }}</strong>
          <span class="text-slate-300">Valor: ${{ number_format((float) $equipment->declared_value_usd, 2) }} USD</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
          <span class="text-slate-400 block mb-1">Estado Fiscal / Aranceles:</span>
          @if($equipment->duty_paid_abroad)
            <strong class="text-emerald-400 text-sm block">✓ Prepagado en Origen ($0.00)</strong>
          @else
            <strong class="text-amber-400 text-sm block">Arancel en Cuba: ${{ number_format((float) $equipment->duty_amount_usd, 2) }}</strong>
          @endif
          <span class="text-slate-400">Flete: ${{ number_format((float) $equipment->shipping_fee_usd, 2) }}</span>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
          <span class="text-slate-400 block mb-1">Liquidación / Caja:</span>
          @if($equipment->isFullySettled())
            <strong class="text-emerald-400 text-sm block">✓ Totalmente Liquidado</strong>
            <span class="text-emerald-300">Apto para Entrega</span>
          @else
            <strong class="text-rose-400 text-sm block">Pendiente de Pago</strong>
            <span class="text-rose-300">Deuda: ${{ number_format($equipment->getOutstandingBalanceUsd(), 2) }} USD</span>
          @endif
        </div>
      </div>
    </div>

    <!-- Timeline of Status History Card -->
    <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
      <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
        <i class="fa-solid fa-list-check text-purple-400"></i> Bitácora Cronológica de Eventos e Hitos
      </h3>

      <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
        @forelse($equipment->statusHistories as $history)
          <div class="relative group">
            <div class="absolute -left-6 top-1.5 w-4 h-4 rounded-full bg-purple-600 border-2 border-slate-900 shadow-md group-hover:scale-125 transition-all"></div>
            
            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 transition-all">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                <span class="text-sm font-bold text-white">
                  Estado: <span class="text-purple-400">{{ strtoupper(str_replace('_', ' ', $history->to_status)) }}</span>
                </span>
                <span class="text-xs font-mono text-slate-400">
                  {{ $history->recorded_at ? $history->recorded_at->format('d/m/Y H:i:s') : $history->created_at->format('d/m/Y H:i:s') }}
                </span>
              </div>

              @if($history->location)
                <p class="text-xs text-slate-300 font-semibold mb-1">
                  📍 Ubicación: <span class="text-white">{{ $history->location }}</span>
                </p>
              @endif

              @if($history->notes)
                <p class="text-xs text-slate-400">{{ $history->notes }}</p>
              @endif

              <div class="mt-2 pt-2 border-t border-slate-700/50 text-[11px] text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-user-check text-purple-400"></i>
                <span>Registrado por: <strong>{{ $history->changedBy?->name ?? 'Sistema Automático' }}</strong></span>
              </div>
            </div>
          </div>
        @empty
          <p class="text-slate-500 text-sm">No hay historial de auditoría registrado para este equipo.</p>
        @endforelse
      </div>
    </div>

    <!-- WhatsApp Notifications Audit Card -->
    @if($equipment->whatsAppNotifications->isNotEmpty())
      <div class="bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
        <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
          <i class="fa-brands fa-whatsapp text-emerald-400"></i> Notificaciones de WhatsApp Despachadas ({{ $equipment->whatsAppNotifications->count() }})
        </h3>

        <div class="space-y-3 text-xs">
          @foreach($equipment->whatsAppNotifications as $wa)
            <div class="p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <span class="font-bold text-white">Trigger: {{ strtoupper($wa->event_trigger) }}</span> • 
                <span class="text-slate-400">Destinatario: {{ $wa->recipient_phone }} ({{ $wa->recipient_name }})</span>
                <p class="text-[11px] text-slate-400 mt-1 font-mono">Gateway ID: {{ $wa->gateway_message_id ?? 'N/A' }} • Provider: {{ $wa->provider ?? 'meta' }}</p>
              </div>

              <div class="text-right">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $wa->status === 'read' ? 'bg-blue-500/20 text-blue-400' : ($wa->status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-300') }}">
                  {{ strtoupper($wa->status) }}
                </span>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $wa->sent_at?->format('d/m/Y H:i') }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

  @elseif(!empty($search))
    <div class="p-12 text-center bg-slate-900/40 rounded-3xl border border-slate-800">
      <i class="fa-solid fa-triangle-exclamation text-4xl text-amber-400 mb-3"></i>
      <h3 class="text-lg font-bold text-white">No se encontró ningún bulto o equipo</h3>
      <p class="text-xs text-slate-400 mt-1">Verifica el código ingresado ("{{ $search }}") o intenta con el PIN de 8 caracteres.</p>
    </div>
  @else
    <div class="p-12 text-center bg-slate-900/40 rounded-3xl border border-slate-800">
      <i class="fa-solid fa-magnifying-glass text-4xl text-slate-600 mb-3"></i>
      <h3 class="text-base font-bold text-white">Ingresa un código para consultar la auditoría</h3>
      <p class="text-xs text-slate-400 mt-1">Puedes buscar por PIN (ej. BK-78492), VIN del vehículo o Tracking ID de SolveCargo.</p>
    </div>
  @endif

</div>
@endsection
