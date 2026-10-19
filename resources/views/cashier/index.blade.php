@extends('layouts.app', ['title' => 'Caja Regional — Cobros y Aranceles'])

@section('page_title', 'Módulo de Cajas Regionales & Liquidación Arancelaria')

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-slate-900 border border-emerald-500/20 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold mb-2 border border-emerald-500/30">
        <i class="fa-solid fa-cash-register text-emerald-400"></i>
        <span>Ventanilla de Recaudación Oficial USD / CUP</span>
      </div>
      <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Caja Regional & Liquidación</h3>
      <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
        Cobro de aranceles y fletes según régimen tributario. Tasa oficial activa: 
        <span class="font-bold text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">1 USD = {{ number_format($activeRate, 2) }} CUP</span>
      </p>
    </div>

    @if($activeShift)
      <div class="flex items-center gap-3">
        <div class="px-4 py-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-950/40">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
          <span>Turno Activo: {{ $activeShift->cashRegister->code }}</span>
        </div>
      </div>
    @endif
  </div>

  @if(!$activeShift)
    <!-- Banner de Apertura de Caja Requerida -->
    <div class="glass-card border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl">
      <div class="max-w-2xl">
        <div class="flex items-center gap-3 text-amber-400 mb-2">
          <i class="fa-solid fa-triangle-exclamation text-xl"></i>
          <h4 class="font-bold text-base text-white">Apertura de Turno de Caja Obligatoria</h4>
        </div>
        <p class="text-xs text-slate-300 mb-6 leading-relaxed">
          Para realizar cobros, registrar recaudación o entregar equipos físicamente, debes declarar el fondo inicial en gaveta y habilitar tu sesión de caja en el almacén provincial.
        </p>

        <form method="POST" action="{{ route('cashier.shift.open') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          @csrf
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Seleccionar Caja *</label>
            <select name="cash_register_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-2xl text-xs text-slate-100 focus:ring-2 focus:ring-emerald-500 font-medium">
              @foreach($cashRegisters as $reg)
                <option value="{{ $reg->id }}" {{ $reg->isOpen() ? 'disabled' : '' }}>
                  {{ $reg->code }} — {{ $reg->name }} {{ $reg->isOpen() ? '(Ocupada)' : '(Disponible)' }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Fondo Inicial USD</label>
            <input type="number" step="0.01" min="0" name="initial_usd" value="0.00" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-2xl text-xs text-slate-100 focus:ring-2 focus:ring-emerald-500 font-mono" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Fondo Inicial CUP</label>
            <input type="number" step="0.01" min="0" name="initial_cup" value="0.00" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-2xl text-xs text-slate-100 focus:ring-2 focus:ring-emerald-500 font-mono" />
          </div>

          <div class="sm:col-span-3 flex justify-end mt-2">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl text-xs font-extrabold shadow-xl shadow-emerald-950/40 flex items-center gap-2">
              <i class="fa-solid fa-key"></i>
              <span>Abrir Turno de Caja</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  @else
    <!-- Panel Operativo: Balance en Vivo y Buscador de Equipos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Columna 1: Estado del Turno y Arqueo -->
      <div class="space-y-4">
        <div class="glass-card rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Gaveta en Vivo</span>
            <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/30">Arqueo Dinámico</span>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Recaudado USD</span>
              <span class="text-xl font-black text-emerald-400 font-mono">${{ number_format((float) $activeShift->total_collected_usd, 2) }}</span>
            </div>
            <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
              <span class="text-[10px] text-slate-400 font-bold uppercase block">Recaudado CUP</span>
              <span class="text-xl font-black text-teal-400 font-mono">${{ number_format((float) $activeShift->total_collected_cup, 2) }}</span>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-800 text-[11px] text-slate-400 space-y-1.5">
            <div class="flex justify-between">
              <span>Cajero Responsable:</span>
              <strong class="text-slate-200">{{ $activeShift->cashier->name }}</strong>
            </div>
            <div class="flex justify-between">
              <span>Hora Apertura:</span>
              <span class="font-mono text-slate-300">{{ $activeShift->opened_at->format('H:i d/m/Y') }}</span>
            </div>
          </div>

          <!-- Botón Cerrar Turno -->
          <button 
            type="button" 
            onclick="document.getElementById('modalCloseShift').classList.remove('hidden')" 
            class="w-full py-3 bg-slate-900 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-2 border border-slate-800 hover:border-rose-500/40"
          >
            <i class="fa-solid fa-lock"></i>
            <span>Cerrar Turno & Arqueo</span>
          </button>
        </div>
      </div>

      <!-- Columna 2 y 3: Buscador de Equipos y Terminal de Cobro -->
      <div class="lg:col-span-2 space-y-4">
        
        <!-- Buscador por PIN o VIN -->
        <div class="glass-card rounded-3xl p-6 shadow-xl space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
              <i class="fa-solid fa-barcode text-brand-400"></i>
              <span>Consultar Bulto para Liquidación o Entrega</span>
            </h4>
            <span class="text-[11px] text-slate-500 font-mono">PIN / VIN / Serial</span>
          </div>

          <div class="flex gap-2">
            <div class="relative flex-1">
              <input 
                type="text" 
                id="searchCodeInput" 
                placeholder="Escanea o escribe PIN (ej: BK-78492) o VIN..." 
                class="w-full px-4 py-3.5 bg-slate-950 border border-slate-700 rounded-2xl text-sm font-mono text-white placeholder-slate-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 focus:border-brand-500 uppercase font-bold transition-all shadow-inner"
                onkeypress="if(event.key === 'Enter') lookupEquipment()"
              />
            </div>
            <button 
              type="button" 
              onclick="lookupEquipment()" 
              class="px-6 py-3.5 bg-brand-600 hover:bg-brand-500 text-white rounded-2xl text-xs font-extrabold shadow-lg shadow-brand-950/40 flex items-center gap-2 transition-all"
            >
              <i class="fa-solid fa-magnifying-glass"></i>
              <span>Consultar</span>
            </button>
          </div>

          <!-- Spinner de Carga -->
          <div id="lookupSpinner" class="hidden text-center py-6 text-slate-400 text-xs">
            <i class="fa-solid fa-circle-notch fa-spin text-xl text-brand-400 mb-2"></i>
            <p>Consultando base de datos central y validando aranceles...</p>
          </div>

          <!-- Resultado del Equipo -->
          <div id="equipmentResultCard" class="hidden p-5 bg-slate-950/90 rounded-2xl border border-slate-800 space-y-4 animate-fade-in shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-800">
              <div>
                <h5 id="eqTitle" class="text-base font-bold text-white">Yadea G5 Pro</h5>
                <p id="eqSubtitle" class="text-xs text-slate-400 font-mono">PIN: BK-78492 | VIN: 123456</p>
              </div>
              <div id="eqStatusBadge"></div>
            </div>

            <!-- Banner Financiero Dinámico -->
            <div id="financialBanner"></div>

            <!-- Formulario de Cobro -->
            <div id="paymentFormSection" class="hidden pt-3 border-t border-slate-800 space-y-4">
              <h6 class="text-xs font-bold uppercase tracking-wider text-slate-300">Registrar Cobro en Ventanilla</h6>
              
              <form method="POST" action="{{ route('cashier.payment.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="equipment_id" id="formEquipmentId" />
                <input type="hidden" name="cash_shift_id" value="{{ $activeShift->id }}" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Monto Abonado en USD</label>
                    <input type="number" step="0.01" min="0" name="amount_usd" id="formAmountUsd" value="0.00" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Monto Abonado en CUP</label>
                    <input type="number" step="0.01" min="0" name="amount_cup" id="formAmountCup" value="0.00" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono" />
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Método de Pago *</label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-medium">
                      <option value="cash_usd">Efectivo USD</option>
                      <option value="cash_cup">Efectivo CUP</option>
                      <option value="cash_mixed" selected>Mixto (Efectivo USD + CUP)</option>
                      <option value="transfer_cup">Transferencia CUP (EnZona / Transfermóvil)</option>
                      <option value="transfer_mlc">Transferencia MLC</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">DNI / Carnet del Cliente</label>
                    <input type="text" name="client_id_card" placeholder="No. Identidad" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono" />
                  </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl text-xs font-black shadow-lg shadow-emerald-950/40 flex items-center justify-center gap-2 transition-all">
                  <i class="fa-solid fa-receipt"></i>
                  <span>Liquidar Cobro & Emitir Comprobante</span>
                </button>
              </form>
            </div>

            <!-- Botón Entrega Directa (Prepagado o Liquidado) -->
            <div id="deliverSection" class="hidden pt-3 border-t border-slate-800">
              <form method="POST" id="deliverForm" action="">
                @csrf
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl mb-3 text-xs text-emerald-300 flex items-center gap-2">
                  <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                  <span>El equipo no presenta deuda pendiente. Puedes proceder a entregarlo físicamente al cliente.</span>
                </div>
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white rounded-2xl text-xs font-black shadow-lg shadow-brand-950/40 flex items-center justify-center gap-2 transition-all">
                  <i class="fa-solid fa-handshake"></i>
                  <span>Confirmar Entrega Física al Cliente</span>
                </button>
              </form>
            </div>

          </div>
        </div>

      </div>

    </div>

    <!-- Modal Cierre de Turno -->
    <div id="modalCloseShift" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
      <div class="glass-card border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <h4 class="text-base font-bold text-white flex items-center gap-2">
          <i class="fa-solid fa-lock text-rose-400"></i>
          <span>Cierre de Turno & Arqueo Físico</span>
        </h4>
        <p class="text-xs text-slate-400">
          Cuenta el efectivo físico en gaveta e ingresa los montos exactos para detectar faltantes o sobrantes.
        </p>

        <form method="POST" action="{{ route('cashier.shift.close', $activeShift) }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Efectivo Físico USD *</label>
            <input type="number" step="0.01" min="0" name="closing_usd" required value="{{ $activeShift->expected_balance_usd }}" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white font-mono" />
            <span class="text-[10px] text-slate-500">Esperado por sistema: ${{ number_format((float) $activeShift->expected_balance_usd, 2) }} USD</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Efectivo Físico CUP *</label>
            <input type="number" step="0.01" min="0" name="closing_cup" required value="{{ $activeShift->expected_balance_cup }}" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white font-mono" />
            <span class="text-[10px] text-slate-500">Esperado por sistema: ${{ number_format((float) $activeShift->expected_balance_cup, 2) }} CUP</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Observaciones del Cierre</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white" placeholder="Arqueo conforme..."></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" onclick="document.getElementById('modalCloseShift').classList.add('hidden')" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-semibold">Cancelar</button>
            <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold">Confirmar Cierre</button>
          </div>
        </form>
      </div>
    </div>
  @endif

  <!-- Tabla de Recibos Emitidos en el Turno -->
  @if($activeShift && $recentPayments->count() > 0)
    <div class="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-2xl">
      <div class="p-5 border-b border-slate-800 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
          <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
          <span>Cobros Registrados en este Turno</span>
        </h4>
        <span class="text-xs text-slate-400 font-mono">{{ $recentPayments->count() }} recibo(s)</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold">
            <tr>
              <th class="px-4 py-3.5">No. Recibo</th>
              <th class="px-4 py-3.5">Equipo / PIN</th>
              <th class="px-4 py-3.5">Cliente</th>
              <th class="px-4 py-3.5">Abono USD</th>
              <th class="px-4 py-3.5">Abono CUP</th>
              <th class="px-4 py-3.5">Fecha y Hora</th>
              <th class="px-4 py-3.5 text-right">Comprobante</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            @foreach($recentPayments as $pay)
              <tr class="hover:bg-slate-800/40 transition-colors">
                <td class="px-4 py-3.5 font-mono font-bold text-emerald-400">{{ $pay->receipt_number }}</td>
                <td class="px-4 py-3.5 font-mono text-white">{{ $pay->equipment->tracking_pin }}</td>
                <td class="px-4 py-3.5">{{ $pay->client_name }}</td>
                <td class="px-4 py-3.5 font-mono font-bold text-slate-200">${{ number_format((float) $pay->amount_paid_usd, 2) }}</td>
                <td class="px-4 py-3.5 font-mono font-bold text-slate-200">${{ number_format((float) $pay->amount_paid_cup, 2) }}</td>
                <td class="px-4 py-3.5 text-slate-400 font-mono text-[11px]">{{ $pay->paid_at->format('H:i d/m') }}</td>
                <td class="px-4 py-3.5 text-right">
                  <a href="{{ route('cashier.receipt', $pay) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-receipt text-[10px]"></i>
                    <span>Ver Recibo ↗</span>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

</div>

<script>
function lookupEquipment() {
  const code = document.getElementById('searchCodeInput').value.trim();
  if (!code) return;

  const spinner = document.getElementById('lookupSpinner');
  const card = document.getElementById('equipmentResultCard');
  
  spinner.classList.remove('hidden');
  card.classList.add('hidden');

  fetch(`{{ route('cashier.lookup') }}?code=${encodeURIComponent(code)}`)
    .then(r => r.json())
    .then(data => {
      spinner.classList.add('hidden');
      if (!data.success) {
        alert(data.message || 'Equipo no encontrado');
        return;
      }

      const eq = data.equipment;
      card.classList.remove('hidden');

      document.getElementById('eqTitle').innerText = `${eq.brand} ${eq.model}`;
      document.getElementById('eqSubtitle').innerText = `PIN: ${eq.tracking_pin} | VIN: ${eq.vin_serial} | Almacén: ${eq.warehouse || 'En Tránsito'}`;

      document.getElementById('formEquipmentId').value = eq.id;
      document.getElementById('deliverForm').action = `/cashier/equipment/${eq.id}/deliver`;

      const banner = document.getElementById('financialBanner');
      const payForm = document.getElementById('paymentFormSection');
      const deliverSec = document.getElementById('deliverSection');

      if (eq.is_totally_prepaid || eq.is_fully_paid) {
        banner.innerHTML = `
          <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl">
            <div class="flex items-center gap-2 text-emerald-400 font-bold text-sm">
              <i class="fa-solid fa-circle-check text-base"></i>
              <span>${eq.is_totally_prepaid ? 'TOTALMENTE PREPAGADO EN EL EXTERIOR ($0.00)' : 'LIQUIDADO EN CUBA'}</span>
            </div>
            <p class="text-xs text-slate-300 mt-1">Este producto no tiene cargos pendientes en Cuba. Arancel y flete exentos de cobro.</p>
          </div>
        `;
        payForm.classList.add('hidden');
        deliverSec.classList.remove('hidden');
      } else {
        banner.innerHTML = `
          <div class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-amber-400 font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>SALDO PENDIENTE DE COBRO</span>
              </span>
              <span class="text-lg font-black font-mono text-amber-300">$${eq.outstanding_usd.toFixed(2)} USD</span>
            </div>
            <div class="text-xs text-slate-300 flex justify-between border-t border-amber-500/20 pt-2">
              <span>Arancel: $${eq.duty_amount_usd.toFixed(2)} USD | Envío: $${eq.shipping_fee_usd.toFixed(2)} USD</span>
              <span class="font-mono font-bold text-teal-300">Equivalente CUP: $${eq.outstanding_cup.toFixed(2)} CUP</span>
            </div>
          </div>
        `;
        document.getElementById('formAmountUsd').value = eq.outstanding_usd.toFixed(2);
        document.getElementById('formAmountCup').value = "0.00";
        payForm.classList.remove('hidden');
        deliverSec.classList.add('hidden');
      }
    })
    .catch(err => {
      spinner.classList.add('hidden');
      alert('Error al consultar el equipo.');
    });
}
</script>
@endsection
