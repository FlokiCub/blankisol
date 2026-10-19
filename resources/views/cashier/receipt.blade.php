@extends('layouts.app', ['title' => 'Comprobante de Pago — ' . $payment->receipt_number])

@section('page_title', 'Recibo Oficial de Pago & Comprobante')

@section('content')
<style>
  @media print {
    @page { margin: 10mm; size: auto; }
    body { background: #ffffff !important; color: #000000 !important; font-size: 11pt; }
    .no-print, header, aside, #sidebar-backdrop { display: none !important; }
    .printable-invoice {
      box-shadow: none !important;
      border: 1px solid #111 !important;
      padding: 15px !important;
      margin: 0 !important;
      max-width: 100% !important;
      background: white !important;
      color: black !important;
    }
    .print-text-dark { color: #000 !important; }
    .print-bg-gray { background-color: #f3f4f6 !important; }
  }
</style>

<div class="max-w-3xl mx-auto space-y-6">

  <!-- Botones de Acción y Opciones de Impresión -->
  <div class="flex items-center justify-between no-print flex-wrap gap-3">
    <a href="{{ route('cashier.index') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-slate-300 rounded-2xl text-xs font-bold inline-flex items-center gap-2 border border-slate-800 transition-colors">
      <i class="fa-solid fa-arrow-left"></i>
      <span>Volver a Ventanilla de Caja</span>
    </a>

    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl text-xs font-extrabold inline-flex items-center gap-2 shadow-lg shadow-emerald-950/40 active:scale-98 transition-all">
        <i class="fa-solid fa-print text-sm"></i>
        <span>Imprimir Comprobante Oficial</span>
      </button>
    </div>
  </div>

  <!-- Comprobante Imprimible de Alta Fidelidad -->
  <div class="printable-invoice bg-white text-slate-900 rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-200 font-sans space-y-6">
    
    <!-- Encabezado Oficial -->
    <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 gap-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black text-sm">
            B
          </div>
          <h2 class="text-2xl font-black tracking-tight text-slate-950">BLANKISOL S.R.L.</h2>
        </div>
        <p class="text-xs text-slate-600 font-semibold">Sistema de Control y Gestión de Importaciones (SCGI)</p>
        <p class="text-[11px] text-slate-500">Recibo Oficial de Liquidación Arancelaria, Servicios y Flete</p>
        <p class="text-[10px] text-slate-400 font-mono">Terminal Puerto Mariel &bull; República de Cuba</p>
      </div>

      <div class="text-right space-y-1">
        <span class="inline-block px-3.5 py-1.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-black font-mono shadow-xs">
          {{ $payment->receipt_number }}
        </span>
        <p class="text-[11px] text-slate-600 font-mono font-medium mt-1">
          Emisión: {{ $payment->paid_at->format('d/m/Y H:i:s') }}
        </p>
        <p class="text-[10px] text-slate-400 font-mono">
          Turno #{{ $payment->cash_shift_id }}
        </p>
      </div>
    </div>

    <!-- Sucursal y Operador Responsable -->
    <div class="grid grid-cols-2 gap-4 text-xs p-4 bg-slate-50 rounded-2xl border border-slate-200">
      <div>
        <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Punto de Cobro / HUB:</span>
        <strong class="text-slate-900 text-sm block">{{ $payment->cashShift->cashRegister->regionalWarehouse->name ?? 'Mariel Central' }}</strong>
        <span class="block text-[11px] text-slate-600">{{ $payment->cashShift->cashRegister->regionalWarehouse->province ?? 'La Habana' }}</span>
      </div>
      <div>
        <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Cajero Operador:</span>
        <strong class="text-slate-900 text-sm block">{{ $payment->cashier->name }}</strong>
        <span class="block text-[11px] text-slate-600 font-mono">Terminal: {{ $payment->cashShift->cashRegister->code }}</span>
      </div>
    </div>

    <!-- Datos del Cliente y Ficha del Equipo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs pt-2">
      <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-1.5">
        <span class="text-slate-400 text-[10px] uppercase font-black tracking-wider block">Titular del Envío (Destinatario)</span>
        <p class="text-sm font-bold text-slate-950">{{ $payment->client_name }}</p>
        @if($payment->client_id_card)
          <p class="text-slate-600 font-mono text-[11px]">DNI / Carnet Identidad: <strong class="text-slate-800">{{ $payment->client_id_card }}</strong></p>
        @endif
        @if($payment->client_phone)
          <p class="text-slate-600 font-mono text-[11px]">Teléfono Móvil: <strong class="text-slate-800">{{ $payment->client_phone }}</strong></p>
        @endif
      </div>

      <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-1.5">
        <span class="text-slate-400 text-[10px] uppercase font-black tracking-wider block">Mercancía Identificada</span>
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-bold text-slate-950">{{ $payment->equipment->brand }} {{ $payment->equipment->model }}</p>
            <p class="text-slate-600 font-mono text-[11px]">VIN / Serial: {{ $payment->equipment->vin_serial }}</p>
          </div>
          <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-900 font-mono font-bold text-xs border border-slate-300">
            {{ $payment->equipment->tracking_pin }}
          </span>
        </div>
      </div>
    </div>

    <!-- Desglose de Conceptos Liquidados -->
    <div>
      <table class="w-full text-xs">
        <thead>
          <tr class="border-b-2 border-slate-300 text-slate-500 uppercase text-[10px] font-bold">
            <th class="py-2.5 text-left">Concepto Arancelario / Logístico</th>
            <th class="py-2.5 text-center">Moneda</th>
            <th class="py-2.5 text-right">Monto Liquidado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr>
            <td class="py-3 font-semibold text-slate-800">
              Arancel Aduanal (10% B2C o Tarifa Base)
              <span class="block text-[10px] text-slate-500 font-normal">Aforo regulatorio aduana puerto Mariel</span>
            </td>
            <td class="py-3 text-center font-mono text-slate-600">USD</td>
            <td class="py-3 text-right font-mono font-bold text-slate-950">${{ number_format((float) $payment->duty_amount_usd, 2) }}</td>
          </tr>
          <tr>
            <td class="py-3 font-semibold text-slate-800">
              Servicio de Flete y Distribución Provincial
              <span class="block text-[10px] text-slate-500 font-normal">Traslado a domicilio o almacén de entrega</span>
            </td>
            <td class="py-3 text-center font-mono text-slate-600">USD</td>
            <td class="py-3 text-right font-mono font-bold text-slate-950">${{ number_format((float) $payment->shipping_fee_usd, 2) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-slate-900 font-black text-sm">
            <td class="py-3.5 text-slate-950">TOTAL LIQUIDADO EN USD</td>
            <td colspan="2" class="py-3.5 text-right font-mono text-slate-950 text-base">
              ${{ number_format((float) $payment->amount_due_usd, 2) }} USD
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Desglose del Pago Recibido (Multimoneda) -->
    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-xs">
      <span class="text-slate-900 text-[10px] uppercase font-bold tracking-wider block mb-3">Cobro Efectuado en Ventanilla</span>
      <div class="grid grid-cols-2 gap-4 text-slate-800">
        <div class="p-3 bg-white rounded-xl border border-slate-200">
          <span class="text-slate-500 block text-[10px] font-bold uppercase">Efectivo USD Percibido:</span>
          <span class="font-mono font-black text-emerald-700 text-base">${{ number_format((float) $payment->amount_paid_usd, 2) }} USD</span>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200">
          <span class="text-slate-500 block text-[10px] font-bold uppercase">Efectivo CUP Percibido:</span>
          <span class="font-mono font-black text-emerald-700 text-base">${{ number_format((float) $payment->amount_paid_cup, 2) }} CUP</span>
        </div>
        <div class="col-span-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-600 font-medium">
          <span>Tasa Oficial Aplicada al Momento del Cobro:</span>
          <span class="font-mono font-bold text-slate-900">1 USD = {{ number_format((float) $payment->exchange_rate_applied, 2) }} CUP</span>
        </div>
      </div>
    </div>

    <!-- Firmas de Conformidad -->
    <div class="grid grid-cols-2 gap-12 pt-8 border-t border-slate-300 text-center text-xs text-slate-600">
      <div>
        <div class="border-b border-slate-400 pb-8 mb-2"></div>
        <p class="font-bold text-slate-900">{{ $payment->cashier->name }}</p>
        <p class="text-[10px] text-slate-500">Cajero Autorizado &bull; Firma y Cuño</p>
      </div>
      <div>
        <div class="border-b border-slate-400 pb-8 mb-2"></div>
        <p class="font-bold text-slate-900">{{ $payment->client_name }}</p>
        <p class="text-[10px] text-slate-500">Destinatario &bull; Conforme con Entrega</p>
      </div>
    </div>

    <!-- Pie de página de Auditoría Inmutable -->
    <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400 font-mono">
      <span>BLANKISOL SCGI &bull; Sistema Oficial de Importaciones</span>
      <span>HASH: {{ strtoupper(substr(hash('sha256', $payment->id . $payment->receipt_number . $payment->amount_due_usd), 0, 16)) }}</span>
    </div>

  </div>

</div>
@endsection
