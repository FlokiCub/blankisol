@extends('layouts.app', ['title' => 'Área de Despacho — Cotejo con Manifiesto (Mariel)'])

@section('page_title', 'Área de Despacho Puerto Mariel — Cotejo con Manifiesto')

@section('content')
<style>
  @keyframes flashSuccess {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    50% { box-shadow: 0 0 60px 20px rgba(16, 185, 129, 0.4); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
  }
  @keyframes flashError {
    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    50% { box-shadow: 0 0 60px 20px rgba(239, 68, 68, 0.4); }
    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
  }
  .scanner-flash-success { animation: flashSuccess 0.8s ease-out; }
  .scanner-flash-error { animation: flashError 0.8s ease-out; }
</style>

<div class="space-y-6">

  <!-- Header Banner Ejecutivo -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-brand-950 via-slate-900 to-slate-900 border border-brand-500/20 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-semibold mb-2 border border-brand-500/30">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Terminal Mariel &bull; Muelle de Despacho y Aforo</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Escáner Industrial de Cotejo Inmediato</h3>
      <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
        Pase directo sin ensamblaje. La pistola óptica coteja el bulto físico contra el manifiesto de carga (SolveCargo / BL). Al confirmar coincidencia, transiciona a <strong>Listo para Despacho Provincial</strong> con emisión automática de WhatsApp.
      </p>
    </div>

    <!-- Quick Metrics Counters -->
    <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
      <div class="px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-800 text-right">
        <span class="text-[11px] text-slate-400 block font-medium">Listos para Despacho</span>
        <span class="text-2xl font-black text-emerald-400 font-mono">{{ $readyForDispatchCount }}</span>
      </div>
      <div class="px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-800 text-right">
        <span class="text-[11px] text-slate-400 block font-medium">En Arribo / Puerto</span>
        <span class="text-2xl font-black text-amber-400 font-mono">{{ $inPortArrivalCount }}</span>
      </div>
      <div class="px-4 py-3 rounded-2xl bg-slate-950/80 border border-slate-800 text-right">
        <span class="text-[11px] text-slate-400 block font-medium">En Tránsito / Proveedor</span>
        <span class="text-2xl font-black text-brand-400 font-mono">{{ $pendingTransitCount }}</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Scanner and Manifest Verification Form (7 cols) -->
    <div class="lg:col-span-7 space-y-6">
      <div id="scanner-container" class="glass-card rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 transition-all duration-300">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg shadow-lg shadow-brand-900/40">
              <i class="fa-solid fa-barcode"></i>
            </div>
            <div>
              <h4 class="text-sm font-bold text-white flex items-center gap-2">
                <span>Lector Láser de Bultos</span>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-normal">Autofocus Activo</span>
              </h4>
              <p class="text-[11px] text-slate-400">Pasa el lector de código de barras o ingresa PIN / VIN / BL</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" onclick="testAudio()" title="Probar audio de escaneo" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
              <i class="fa-solid fa-volume-high text-xs"></i>
            </button>
            <span class="text-[11px] text-brand-400 font-mono bg-brand-500/10 px-2.5 py-1 rounded-full border border-brand-500/20">
              2D / 1D Ready
            </span>
          </div>
        </div>

        <form method="POST" action="{{ route('dispatch.scan.process') }}" class="space-y-5" id="scanner-form">
          @csrf

          <!-- Barcode Input con Enfoque Continuo -->
          <div>
            <label for="barcode" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
              Código Escaneado del Bulto <span class="text-rose-400">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-qrcode text-xl text-brand-400"></i>
              </div>
              <input 
                type="text" 
                id="barcode" 
                name="barcode" 
                required 
                autofocus
                autocomplete="off"
                placeholder="Escanea el código de barras aquí..." 
                class="w-full pl-12 pr-28 py-4 rounded-2xl bg-slate-950 border-2 border-brand-500/40 text-white placeholder-slate-500 font-mono text-base font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-brand-500 transition-all uppercase tracking-wider shadow-inner"
              />
              <button 
                type="button" 
                id="btn-lookup"
                onclick="lookupManifestData()"
                class="absolute right-2 top-2 bottom-2 px-4 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-brand-900/50"
              >
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Cotejar</span>
              </button>
            </div>
            <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
              <i class="fa-solid fa-circle-info text-[10px]"></i>
              <span>El campo se re-enfoca automáticamente para permitir ráfagas continuas de escaneo físico en muelle.</span>
            </p>
          </div>

          <!-- Manifest Live Comparison Box (Auto-filled on lookup/scan) -->
          <div id="manifest-preview-box" class="hidden p-5 rounded-2xl bg-slate-950 border border-brand-500/30 space-y-4 animate-fade-in shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-400 animate-pulse"></span>
                <span class="text-xs font-bold text-white uppercase tracking-wider">Cotejo Instantáneo SolveCargo / Manifiesto</span>
              </div>
              <span id="preview-pin" class="text-xs font-mono font-bold text-brand-300 px-2 py-0.5 rounded bg-brand-900/40 border border-brand-500/30"></span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
              <div>
                <span class="text-slate-400 block text-[11px]">Mercancía / Equipo:</span>
                <strong id="preview-item" class="text-white"></strong>
              </div>
              <div>
                <span class="text-slate-400 block text-[11px]">VIN / Serial:</span>
                <strong id="preview-vin" class="text-slate-200 font-mono"></strong>
              </div>
              <div>
                <span class="text-slate-400 block text-[11px]">Destinatario Declarado:</span>
                <strong id="preview-recipient" class="text-slate-200"></strong>
              </div>
              <div>
                <span class="text-slate-400 block text-[11px]">Provincia Destino:</span>
                <strong id="preview-destination" class="text-emerald-300 font-bold"></strong>
              </div>
              <div>
                <span class="text-slate-400 block text-[11px]">Valor Declarado CIF:</span>
                <strong id="preview-value" class="text-slate-200 font-mono"></strong>
              </div>
              <div>
                <span class="text-slate-400 block text-[11px]">Liquidación en Origen:</span>
                <strong id="preview-finance" class="text-slate-200"></strong>
              </div>
            </div>
          </div>

          <!-- Declaraciones Arancelarias -->
          <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
            <p class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
              <i class="fa-solid fa-file-invoice-dollar text-brand-400"></i>
              <span>Declaración Arancelaria de Origen</span>
            </p>

            <div class="flex items-start gap-3">
              <input 
                type="checkbox" 
                id="duty_paid_abroad" 
                name="duty_paid_abroad" 
                value="1" 
                class="mt-1 w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500"
              />
              <label for="duty_paid_abroad" class="text-xs text-slate-200 cursor-pointer select-none">
                <strong class="text-emerald-400">Aranceles y tasas 100% liquidados en el exterior.</strong>
                <span class="text-[11px] text-slate-400 block mt-0.5">El sistema fijará el cobro aduanal en <strong>$0.00 USD</strong>.</span>
              </label>
            </div>

            <div class="flex items-start gap-3 pt-2 border-t border-slate-800/80">
              <input 
                type="checkbox" 
                id="delivery_paid_abroad" 
                name="delivery_paid_abroad" 
                value="1" 
                class="mt-1 w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500"
              />
              <label for="delivery_paid_abroad" class="text-xs text-slate-200 cursor-pointer select-none">
                <strong class="text-brand-300">Flete provincial a domicilio ($50 USD) prepagado en origen.</strong>
                <span class="text-[11px] text-slate-400 block mt-0.5">Exonera el cobro de transporte al destinatario final en Cuba.</span>
              </label>
            </div>
          </div>

          <!-- Location Note -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="location" class="block text-xs font-semibold text-slate-400 mb-1">
                Punto de Control en Mariel
              </label>
              <input 
                type="text" 
                id="location" 
                name="location" 
                value="Puerto del Mariel - Área de Despacho" 
                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"
              />
            </div>
            <div>
              <label for="notes" class="block text-xs font-semibold text-slate-400 mb-1">
                Observaciones del Operador
              </label>
              <input 
                type="text" 
                id="notes" 
                name="notes" 
                placeholder="Ej: Bulto íntegro con sello SolveCargo intacto" 
                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"
              />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button 
              type="submit" 
              id="btn-confirm-scan"
              class="flex-1 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-950/40 active:scale-98 transition-all flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-check-double text-base"></i>
              <span>Cotejo Exitoso &bull; Habilitar Despacho</span>
            </button>

            <button 
              type="button" 
              onclick="openDiscrepancyModal()"
              class="py-4 px-6 rounded-2xl bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-300 font-bold text-xs transition-all flex items-center justify-center gap-2"
            >
              <i class="fa-solid fa-triangle-exclamation"></i>
              <span>Reportar Discrepancia</span>
            </button>
          </div>
        </form>

        <!-- Botones de Rápida Selección para Demostraciones -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs flex-wrap gap-2">
          <span class="text-slate-400 font-medium">Bultos de prueba predeterminados:</span>
          <div class="flex gap-2 flex-wrap">
            <button type="button" onclick="quickFill('BK-78492', false)" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-brand-600 text-slate-300 font-mono text-[11px] transition-colors">
              BK-78492 (Cobro en Cuba)
            </button>
            <button type="button" onclick="quickFill('BK-SOLAR01', true)" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 font-mono text-[11px] transition-colors">
              BK-SOLAR01 (Prepagado $0)
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Right Column: Last Scan Result Card & Recent Verified List (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
      
      <!-- Last Scan Success Card -->
      @if(session('scan_result'))
        @php $res = session('scan_result'); $eq = $res['equipment']; $fin = $res['finance']; @endphp
        <div id="last-scan-card" class="p-6 rounded-3xl glass-card border-2 {{ $fin['is_totally_prepaid'] ? 'border-emerald-500/60 bg-emerald-950/20' : 'border-brand-500/60 bg-brand-950/20' }} shadow-2xl space-y-4 animate-fade-in">
          <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
              <span class="text-xl {{ $fin['is_totally_prepaid'] ? 'text-emerald-400' : 'text-brand-400' }}">
                <i class="fa-solid fa-circle-check"></i>
              </span>
              <h4 class="text-sm font-bold text-white">¡Cotejo con Manifiesto Confirmado!</h4>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold {{ $fin['is_totally_prepaid'] ? 'bg-emerald-500/20 text-emerald-300' : 'bg-brand-500/20 text-brand-300' }}">
              {{ $eq->tracking_pin }}
            </span>
          </div>

          <div>
            <h3 class="text-base font-black text-white">{{ $eq->brand }} {{ $eq->model }}</h3>
            <p class="text-xs text-slate-400 font-mono">VIN: {{ $eq->vin_serial }}</p>
            <p class="text-xs text-emerald-400 font-semibold mt-1">
              📍 Destino: {{ $res['destination_province'] ?? 'Destino Provincial' }} &bull; Listo para Ruta Regional
            </p>
          </div>

          <!-- Financial status box -->
          <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-400">Régimen de Liquidación:</span>
              <strong class="{{ $fin['is_totally_prepaid'] ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $fin['summary_label'] }}
              </strong>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-400">Total a cobrar en Cuba:</span>
              <span class="text-base font-black {{ $fin['is_totally_prepaid'] ? 'text-emerald-400' : 'text-white' }} font-mono">
                ${{ number_format($fin['total_to_collect_usd'], 2) }} USD
              </span>
            </div>

            @if(!$fin['is_totally_prepaid'])
              <div class="flex items-center justify-between pt-1 border-t border-slate-800 text-[11px] text-slate-400">
                <span>Equivalente en CUP (Tasa {{ $fin['exchange_rate'] }}):</span>
                <span class="font-bold text-amber-300 font-mono">{{ number_format($fin['total_to_collect_cup'], 2) }} CUP</span>
              </div>
            @endif
          </div>

          <a href="{{ route('tracking.show', $eq->tracking_pin) }}" target="_blank" class="block text-center py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-brand-300 transition-colors">
            Ver Ficha en Tracking Público ↗
          </a>
        </div>
      @endif

      <!-- Recent Verified for Dispatch List -->
      <div class="glass-card rounded-3xl p-6 shadow-sm space-y-4">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
          <span>Últimos Bultos Listos para Despacho</span>
          <span class="text-emerald-400 font-mono text-[11px]">Mariel → Provincias</span>
        </h4>

        <div class="divide-y divide-slate-800/60 text-xs">
          @forelse($recentVerified as $item)
            <div class="py-3 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <p class="font-bold text-white truncate">{{ $item->brand }} {{ $item->model }}</p>
                <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                  <span class="text-brand-400 font-bold">{{ $item->tracking_pin }}</span>
                  <span>•</span>
                  <span>{{ $item->client_data['province'] ?? 'La Habana' }}</span>
                </div>
              </div>

              <div>
                @if($item->duty_paid_abroad)
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">
                    ✓ Prepagado $0
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">
                    Cobro en Cuba
                  </span>
                @endif
              </div>
            </div>
          @empty
            <div class="py-6 text-center text-slate-500">
              No hay bultos verificados en esta sesión operativa.
            </div>
          @endforelse
        </div>
      </div>

    </div>

  </div>

</div>

<!-- Modal de Discrepancia -->
<div id="discrepancy-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="glass-card border border-slate-800 rounded-3xl p-6 max-w-lg w-full space-y-5 shadow-2xl">
    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
      <div class="flex items-center gap-2 text-rose-400">
        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        <h4 class="text-sm font-bold text-white">Reportar Discrepancia con Manifiesto</h4>
      </div>
      <button type="button" onclick="closeDiscrepancyModal()" class="text-slate-400 hover:text-white">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('dispatch.discrepancy') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-300 mb-1">Código del Bulto / PIN</label>
        <input type="text" id="modal-barcode" name="barcode" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono uppercase" />
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-300 mb-1">Motivo de la Discrepancia</label>
        <textarea name="reason" rows="3" required placeholder="Ej: Embalaje exterior con roturas, precinto no coincide o destinatario incorrecto..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeDiscrepancyModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold">Cancelar</button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold">Registrar Incidencia</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Web Audio API Synthesizer para Feedback Acústico en Muelle
  let audioCtx = null;

  function getAudioContext() {
    if (!audioCtx) {
      audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioCtx;
  }

  function playScanSuccess() {
    try {
      const ctx = getAudioContext();
      if (ctx.state === 'suspended') ctx.resume();

      const now = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();

      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, now); // D5
      osc.frequency.setValueAtTime(880.00, now + 0.1); // A5

      gain.gain.setValueAtTime(0.15, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);

      osc.connect(gain);
      gain.connect(ctx.destination);

      osc.start(now);
      osc.stop(now + 0.35);

      // Flash visual verde en el contenedor
      triggerFlash('success');
    } catch(e) {
      console.warn('Audio feedback failed', e);
    }
  }

  function playScanError() {
    try {
      const ctx = getAudioContext();
      if (ctx.state === 'suspended') ctx.resume();

      const now = ctx.currentTime;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(180, now);
      osc.frequency.linearRampToValueAtTime(120, now + 0.4);

      gain.gain.setValueAtTime(0.2, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);

      osc.connect(gain);
      gain.connect(ctx.destination);

      osc.start(now);
      osc.stop(now + 0.4);

      // Flash visual rojo en el contenedor
      triggerFlash('error');
    } catch(e) {
      console.warn('Audio feedback failed', e);
    }
  }

  function triggerFlash(type) {
    const el = document.getElementById('scanner-container');
    if (!el) return;
    el.classList.remove('scanner-flash-success', 'scanner-flash-error');
    void el.offsetWidth; // force reflow
    if (type === 'success') {
      el.classList.add('scanner-flash-success');
    } else {
      el.classList.add('scanner-flash-error');
    }
  }

  function testAudio() {
    playScanSuccess();
  }

  // Autofocus Perpetuo para Pistolas Ópticas de Almacén
  const barcodeInput = document.getElementById('barcode');
  let preventAutoFocus = false;

  function ensureBarcodeFocus() {
    if (!preventAutoFocus && barcodeInput && document.activeElement !== barcodeInput) {
      const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
      if (activeTag !== 'input' && activeTag !== 'textarea') {
        barcodeInput.focus();
      }
    }
  }

  // Si el usuario hace clic fuera, reenfocar en 800ms
  window.addEventListener('click', (e) => {
    const isModalOpen = !document.getElementById('discrepancy-modal').classList.contains('hidden');
    if (!isModalOpen) {
      setTimeout(ensureBarcodeFocus, 400);
    }
  });

  // Re-enfocar automáticamente en cualquier tecla presionada fuera de inputs
  window.addEventListener('keydown', (e) => {
    const isModalOpen = !document.getElementById('discrepancy-modal').classList.contains('hidden');
    if (!isModalOpen && document.activeElement !== barcodeInput) {
      const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
      if (activeTag !== 'input' && activeTag !== 'textarea' && e.key.length === 1) {
        barcodeInput.focus();
      }
    }
  });

  function quickFill(code, isPrepaid) {
    barcodeInput.value = code;
    document.getElementById('duty_paid_abroad').checked = isPrepaid;
    document.getElementById('delivery_paid_abroad').checked = isPrepaid;
    lookupManifestData();
  }

  async function lookupManifestData() {
    const code = barcodeInput.value.trim();
    if (!code) return;

    try {
      const response = await fetch("{{ route('dispatch.lookup') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ barcode: code })
      });

      const res = await response.json();
      if (res.success && res.data) {
        playScanSuccess();
        const mc = res.data.manifest_comparison;
        const fin = res.data.finance;

        document.getElementById('preview-pin').textContent = mc.tracking_pin;
        document.getElementById('preview-item').textContent = mc.brand_model + ' (' + mc.equipment_type + ')';
        document.getElementById('preview-vin').textContent = mc.vin_serial;
        document.getElementById('preview-recipient').textContent = mc.recipient_name + ' (' + mc.recipient_id + ')';
        document.getElementById('preview-destination').textContent = mc.destination_province;
        document.getElementById('preview-value').textContent = '$' + Number(mc.declared_value_usd).toFixed(2) + ' USD';
        
        document.getElementById('preview-finance').textContent = fin.is_totally_prepaid 
          ? '✓ Prepagado $0.00' 
          : '$' + Number(fin.total_to_collect_usd).toFixed(2) + ' USD (' + Number(fin.total_to_collect_cup).toFixed(2) + ' CUP)';

        document.getElementById('duty_paid_abroad').checked = mc.duty_paid_abroad;
        document.getElementById('delivery_paid_abroad').checked = mc.delivery_paid_abroad;

        document.getElementById('manifest-preview-box').classList.remove('hidden');
      } else {
        playScanError();
      }
    } catch (e) {
      playScanError();
      console.log('Error looking up manifest', e);
    }
  }

  function openDiscrepancyModal() {
    preventAutoFocus = true;
    const code = barcodeInput.value.trim();
    document.getElementById('modal-barcode').value = code;
    document.getElementById('discrepancy-modal').classList.remove('hidden');
  }

  function closeDiscrepancyModal() {
    preventAutoFocus = false;
    document.getElementById('discrepancy-modal').classList.add('hidden');
    setTimeout(() => barcodeInput.focus(), 100);
  }

  @if(session('scan_result'))
    // Reproducir tono de éxito al cargar tras confirmar
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(playScanSuccess, 300);
    });
  @endif

  @if(session('error') || session('warning'))
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(playScanError, 300);
    });
  @endif
</script>
@endsection
