@extends('layouts.public')

@section('title', "Expediente {$equipment->tracking_pin} | BLANKISOL")

@section('head_extra')
    <!-- Protección de Privacidad: Evitar indexación de expedientes de carga individuales -->
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
<!-- Header de Consulta -->
<header class="sticky top-0 z-50 bg-[#051f42]/95 backdrop-blur-xl border-b border-[#cbcfd4]/15 shadow-lg shadow-[#020d1c]/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-[#f5782b] rounded-xl p-1">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-[#f5782b]/30 p-1 flex items-center justify-center shadow-md group-hover:scale-105 group-hover:border-[#f5782b] transition-all overflow-hidden">
                    <img src="{{ asset('favicon-dark/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white block leading-tight font-sans">
                        BLANKISOL
                    </span>
                    <span class="text-[10px] font-bold tracking-widest text-[#cbcfd4] uppercase block">
                        Torre de Control SCGI
                    </span>
                </div>
            </a>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0a254e] hover:bg-[#0a3166] border border-[#cbcfd4]/20 text-xs font-semibold text-slate-200 transition-all">
                    <i class="fa-solid fa-print text-[#f5782b]"></i>
                    <span>Imprimir Expediente</span>
                </button>
                <a href="{{ route('tracking.index') }}" class="btn-corporate-primary text-xs !py-2.5 !px-5">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Consultar Otro Envío</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Main Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#cbcfd4]" aria-label="Ruta de navegación">
        <a href="{{ route('home') }}" class="hover:text-[#f5782b] transition-colors">Inicio</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i>
        <a href="{{ route('tracking.index') }}" class="hover:text-[#f5782b] transition-colors">Seguimiento</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i>
        <span class="text-white font-mono font-bold">{{ $equipment->tracking_pin }}</span>
    </nav>

    <!-- Top Card: Resumen General del Equipo & Estatus -->
    <div class="card-corporate p-6 sm:p-10 border border-[#cbcfd4]/20 shadow-2xl relative overflow-hidden">
        <!-- Glow ambiental superior -->
        <div class="absolute -top-12 -right-12 w-80 h-80 bg-[#f5782b]/10 blur-3xl pointer-events-none -z-10"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-[#cbcfd4]/15">
            <div>
                <!-- PIN & Copy Action -->
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#f5782b]/15 border border-[#f5782b]/30 text-[#ff934f] text-xs font-mono font-bold tracking-wider">
                        <i class="fa-solid fa-barcode text-xs"></i>
                        <span>PIN: {{ $equipment->tracking_pin }}</span>
                    </div>

                    <button 
                        type="button" 
                        onclick="copyPin('{{ $equipment->tracking_pin }}')" 
                        id="copy-pin-btn"
                        class="px-2.5 py-1 rounded-lg bg-[#051f42] hover:bg-[#0a254e] border border-[#cbcfd4]/30 text-[11px] text-[#cbcfd4] hover:text-white transition-all flex items-center gap-1.5"
                        title="Copiar código PIN"
                    >
                        <i class="fa-regular fa-copy text-xs"></i>
                        <span id="copy-text">Copiar</span>
                    </button>

                    <span class="text-xs text-slate-400">
                        &bull; Expediente Oficial SCGI
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    {{ $equipment->brand }} {{ $equipment->model }}
                </h1>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300 mt-2 font-mono">
                    <span>
                        VIN / N° Chasis: <strong class="text-white">{{ $equipment->vin_serial }}</strong>
                    </span>
                    @if($equipment->solve_cargo_tracking_id)
                        <span class="text-[#cbcfd4]">
                            &bull; Manifiesto SolveCargo: <strong class="text-[#ff934f]">{{ $equipment->solve_cargo_tracking_id }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Badge de Estado en Tiempo Real -->
            <div class="flex flex-col items-start lg:items-end gap-2.5">
                <div class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#0a254e] border-2 border-[#f5782b]/40 shadow-xl shadow-[#020d1c]/60">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#f5782b] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#f5782b]"></span>
                    </span>
                    <span class="text-sm sm:text-base font-extrabold text-white uppercase tracking-wider">
                        {{ is_object($equipment->current_status) && method_exists($equipment->current_status, 'label') ? $equipment->current_status->label() : ucfirst(str_replace('_', ' ', $equipment->current_status)) }}
                    </span>
                </div>

                @if($equipment->current_location_note)
                    <div class="text-xs text-[#cbcfd4] flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-location-dot text-[#f5782b]"></i>
                        <span>{{ $equipment->current_location_note }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stepper / Barra de Progreso del Ciclo Operativo -->
        <div class="pt-8">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#cbcfd4] flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-[#f5782b]"></i>
                    <span>Fase del Ciclo Operativo</span>
                </h3>
                <span class="text-[11px] text-slate-400">7 Hitos de Control</span>
            </div>

            @php
                $stages = [
                    ['id' => 'supplier_dispatched', 'name' => 'Proveedor', 'icon' => 'fa-industry'],
                    ['id' => 'in_transit_solve_cargo', 'name' => 'Tránsito Marítimo', 'icon' => 'fa-ship'],
                    ['id' => 'port_arrival', 'name' => 'Arribo a Puerto', 'icon' => 'fa-anchor'],
                    ['id' => 'customs_inspection', 'name' => 'Aduana Mariel', 'icon' => 'fa-shield-halved'],
                    ['id' => 'ready_for_dispatch', 'name' => 'Cotejo Despacho', 'icon' => 'fa-barcode'],
                    ['id' => 'regional_transit', 'name' => 'Ruta Provincial', 'icon' => 'fa-truck-fast'],
                    ['id' => 'delivered', 'name' => 'Entregado', 'icon' => 'fa-circle-check'],
                ];

                $stageOrder = array_column($stages, 'id');
                $currentStatusCode = is_object($equipment->current_status) ? $equipment->current_status->value : $equipment->current_status;
                $currentIndex = array_search($currentStatusCode, $stageOrder);
                if ($currentIndex === false) $currentIndex = 3;
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($stages as $index => $stage)
                    @php
                        $isCompleted = $index < $currentIndex;
                        $isCurrent = $index === $currentIndex;
                    @endphp
                    <div class="p-3.5 rounded-2xl border text-center transition-all duration-300 {{ $isCurrent ? 'bg-gradient-to-b from-[#0a254e] to-[#051f42] border-[#f5782b] shadow-xl shadow-[#f5782b]/15 scale-[1.02]' : ($isCompleted ? 'bg-[#020d1c]/80 border-emerald-500/40 text-slate-300' : 'bg-[#020d1c]/40 border-[#cbcfd4]/10 opacity-50') }}">
                        <div class="w-9 h-9 rounded-xl mx-auto flex items-center justify-center text-sm mb-2 {{ $isCurrent ? 'bg-[#f5782b] text-white shadow-md shadow-[#f5782b]/40 animate-pulse' : ($isCompleted ? 'bg-emerald-500/20 text-emerald-400' : 'bg-[#051f42] text-slate-500') }}">
                            <i class="fa-solid {{ $stage['icon'] }}"></i>
                        </div>
                        <p class="text-[11px] font-bold text-white line-clamp-1 leading-tight">{{ $stage['name'] }}</p>
                        <span class="text-[9px] font-bold uppercase tracking-wider mt-1 block {{ $isCurrent ? 'text-[#ff934f]' : ($isCompleted ? 'text-emerald-400' : 'text-slate-500') }}">
                            {{ $isCurrent ? 'En Proceso' : ($isCompleted ? 'Completado' : 'Pendiente') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2 Column Details: Timeline + Technical & Financial Info -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Columna Izquierda: Bitácora Cronológica de Trazabilidad (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/15 shadow-xl">
                <div class="flex items-center justify-between pb-5 border-b border-[#cbcfd4]/15 mb-6">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white flex items-center gap-2.5">
                            <i class="fa-solid fa-clock-rotate-left text-[#f5782b]"></i>
                            <span>Bitácora Oficial de Trazabilidad</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Registro secuencial e inmutable de movimientos, inspecciones y custodias
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-xl bg-[#0a254e] border border-[#cbcfd4]/20 text-[#cbcfd4] font-mono text-xs font-semibold">
                        {{ $equipment->statusHistories->count() }} eventos
                    </span>
                </div>

                <!-- Lista de Hitos de la Bitácora -->
                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#cbcfd4]/20">
                    @forelse($equipment->statusHistories as $history)
                        <div class="relative group">
                            <!-- Bullet en la línea de tiempo -->
                            <div class="absolute -left-6 top-1.5 w-4 h-4 rounded-full bg-[#051f42] border-2 border-[#f5782b] group-hover:scale-125 transition-transform shadow-md shadow-[#f5782b]/30"></div>

                            <div class="p-4 sm:p-5 rounded-2xl bg-[#020d1c]/80 border border-[#cbcfd4]/15 hover:border-[#f5782b]/40 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 mb-2">
                                    <h3 class="text-sm sm:text-base font-bold text-white">
                                        {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}
                                    </h3>
                                    <span class="text-xs font-mono text-[#cbcfd4] bg-[#051f42] px-2.5 py-0.5 rounded-lg border border-[#cbcfd4]/10 self-start sm:self-auto">
                                        {{ $history->recorded_at ? $history->recorded_at->format('d/m/Y H:i') : '' }}
                                    </span>
                                </div>

                                @if($history->location)
                                    <p class="text-xs text-[#ff934f] font-semibold flex items-center gap-1.5 mb-1.5">
                                        <i class="fa-solid fa-map-pin text-[10px]"></i>
                                        <span>{{ $history->location }}</span>
                                    </p>
                                @endif

                                @if($history->notes)
                                    <p class="text-xs text-slate-300 leading-relaxed bg-[#051f42]/70 p-3 rounded-xl border border-[#cbcfd4]/10 mt-2">
                                        {{ $history->notes }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400 bg-[#020d1c]/40 rounded-2xl border border-dashed border-[#cbcfd4]/20">
                            <i class="fa-solid fa-hourglass-start text-xl text-[#f5782b] mb-2 block"></i>
                            El expediente está activo y a la espera de próximos escaneos de control.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Botón Consultar Otro Envío en la parte inferior -->
            <div class="p-6 rounded-2xl bg-[#020d1c]/60 border border-[#cbcfd4]/15 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <span class="text-xs font-bold text-white block">¿Deseas rastrear otro contenedor o equipo?</span>
                    <span class="text-[11px] text-slate-400">Puedes consultar por PIN, VIN o identificador SolveCargo.</span>
                </div>
                <a href="{{ route('tracking.index') }}" class="btn-corporate-secondary text-xs !py-3 !px-6 whitespace-nowrap">
                    <i class="fa-solid fa-magnifying-glass mr-1 text-[#f5782b]"></i>
                    <span>Nueva Búsqueda</span>
                </a>
            </div>
        </div>

        <!-- Columna Derecha: Pase Óptico (QR), Aduana / Arancel y Asistencia (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Pase Óptico de Retiro (Código QR) -->
            <div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/15 shadow-xl text-center space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#cbcfd4]/15">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Pase Óptico de Retiro</span>
                    <span class="badge-corporate-orange text-[10px]">Ventanilla</span>
                </div>

                <div class="p-4 bg-white rounded-2xl inline-block mx-auto shadow-2xl">
                    <img 
                        src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('tracking.show', $equipment->tracking_pin)) }}" 
                        alt="Código QR de Retiro para PIN {{ $equipment->tracking_pin }}"
                        class="w-36 h-36 mx-auto"
                        loading="lazy"
                    />
                </div>

                <div>
                    <p class="text-sm font-extrabold text-white font-mono tracking-wider">{{ $equipment->tracking_pin }}</p>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                        Muestra este código digital o impreso en el mostrador del almacén HUB para validación y entrega expedita.
                    </p>
                </div>
            </div>

            <!-- Resumen Financiero & Arancel Aduanal -->
            <div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/15 shadow-xl space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2 pb-3 border-b border-[#cbcfd4]/15">
                    <i class="fa-solid fa-file-invoice-dollar text-[#f5782b]"></i>
                    <span>Estado Financiero & Arancel</span>
                </h2>

                <div class="space-y-3 text-xs">
                    <div class="p-4 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/15 space-y-1.5">
                        <span class="text-[#cbcfd4] block text-[11px] uppercase tracking-wider font-semibold">Régimen Arancelario:</span>
                        @if($equipment->duty_paid_abroad)
                            <div class="flex items-center gap-2 text-emerald-400 font-bold text-sm">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>100% Prepagado en Origen ($0.00 USD)</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Equipo exento de arancel aduanal pendiente de cobro.</p>
                        @else
                            <div class="flex items-center gap-2 text-amber-400 font-bold text-sm">
                                <i class="fa-solid fa-clock"></i>
                                <span>Pendiente de Cobro en Cuba</span>
                            </div>
                            <div class="text-xs text-slate-300 font-mono mt-1">
                                Arancel Estimado: <strong class="text-white">${{ number_format($equipment->duty_amount_usd, 2) }} USD</strong>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/15">
                        <span class="text-[#cbcfd4]">Modalidad de Entrega:</span>
                        @if($equipment->home_delivery_requested)
                            <span class="font-bold text-[#ff934f] flex items-center gap-1">
                                <i class="fa-solid fa-truck"></i> Entrega a Domicilio
                            </span>
                        @else
                            <span class="font-bold text-slate-200 flex items-center gap-1">
                                <i class="fa-solid fa-warehouse"></i> Retiro en Mostrador
                            </span>
                        @endif
                    </div>

                    @if($equipment->currentWarehouse)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/15">
                            <span class="text-[#cbcfd4]">Almacén HUB:</span>
                            <strong class="text-white font-medium">{{ $equipment->currentWarehouse->name }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- WhatsApp Direct Contact -->
            @php
                $waText = urlencode("Hola equipo BLANKISOL, deseo consultar el estado de mi equipo PIN: {$equipment->tracking_pin} ({$equipment->brand} {$equipment->model}) VIN: {$equipment->vin_serial}.");
            @endphp
            <div class="card-corporate p-6 sm:p-8 border border-emerald-500/30 shadow-xl text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 mx-auto flex items-center justify-center text-xl">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">¿Tienes alguna duda sobre tu carga?</h3>
                    <p class="text-xs text-slate-300 mt-1">Atención directa con el equipo de operaciones de BLANKISOL.</p>
                </div>
                <a 
                    href="https://wa.me/?text={{ $waText }}" 
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950/50 active:scale-98 transition-all"
                >
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Contactar por WhatsApp</span>
                </a>
            </div>

            <!-- Nota de Privacidad de Consulta -->
            <div class="p-4 rounded-xl bg-[#020d1c]/60 border border-[#cbcfd4]/10 text-center">
                <span class="text-[11px] text-slate-400 leading-tight block">
                    <i class="fa-solid fa-user-shield text-[#f5782b] mr-1"></i>
                    Este informe omite intencionalmente datos de identidad personal o contacto del consignatario para garantizar los protocolos de protección de datos.
                </span>
            </div>

        </div>

    </div>

</div>

<!-- Footer Simple de Consulta -->
<footer class="bg-[#020d1c] border-t border-[#cbcfd4]/15 text-slate-400 text-xs py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span>&copy; {{ date('Y') }} BLANKISOL SCGI &bull; Sistema de Trazabilidad e Importaciones.</span>
        <div class="flex items-center gap-4 text-[11px]">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Portal Corporativo</a>
            <a href="{{ route('tracking.index') }}" class="hover:text-white transition-colors">Seguimiento</a>
            <a href="{{ route('login') }}" class="text-[#f5782b] hover:underline">Acceso Operadores</a>
        </div>
    </div>
</footer>
@endsection

@section('scripts')
<script>
    function copyPin(pin) {
        navigator.clipboard.writeText(pin).then(function() {
            const btn = document.getElementById('copy-pin-btn');
            const text = document.getElementById('copy-text');
            if (text) {
                text.innerText = '¡Copiado!';
                btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-500');
                setTimeout(function() {
                    text.innerText = 'Copiar';
                    btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-500');
                }, 2000);
            }
        });
    }
</script>
@endsection