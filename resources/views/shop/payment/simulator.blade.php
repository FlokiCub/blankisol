@extends('shop.layouts.store')

@section('title', 'Pasarela de Pago Sandbox (USD) — BLANKISOL SHOP')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-12 space-y-6">

  <!-- Sandbox Warning Banner -->
  <div class="p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-3">
    <i class="fa-solid fa-flask text-lg text-amber-400 shrink-0"></i>
    <div>
      <strong class="block text-white">Entorno de Pruebas Sandbox Oficial</strong>
      <span>Esta pantalla simula el procesamiento con pasarela bancaria externa en USD sin cobro real a tarjetas de crédito.</span>
    </div>
  </div>

  <!-- Gateway Simulation Card -->
  <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-800 space-y-6 shadow-2xl">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-base">
          <i class="fa-solid fa-credit-card"></i>
        </div>
        <div>
          <span class="text-xs font-bold text-white uppercase tracking-wider block">BLANKISOL SECURE PAY</span>
          <span class="text-[10px] text-slate-400 font-mono">Ref: {{ $gatewayRef ?? 'TRX-SBX-01' }}</span>
        </div>
      </div>
      <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
        SANDBOX USD
      </span>
    </div>

    <!-- Amount Display -->
    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 text-center space-y-1">
      <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Importe a Liquidar</span>
      <span class="text-3xl sm:text-4xl font-black text-white font-mono block">
        ${{ number_format((float)$order->total, 2) }} <span class="text-xs text-emerald-400 font-sans font-bold">USD</span>
      </span>
      <span class="text-[11px] text-slate-400 block font-mono">Pedido: #{{ $order->order_number }}</span>
    </div>

    <!-- Customer Details -->
    <div class="space-y-2 text-xs text-slate-300 p-4 rounded-2xl bg-slate-900/50 border border-slate-800/80">
      <div class="flex justify-between">
        <span class="text-slate-400">Titular / Empresa:</span>
        <strong class="text-white">{{ $order->customer_data['business_name'] ?? (($order->customer_data['first_name'] ?? 'Cliente') . ' ' . ($order->customer_data['last_name'] ?? '')) }}</strong>
      </div>
      <div class="flex justify-between">
        <span class="text-slate-400">Correo Electrónico:</span>
        <span class="font-mono text-white">{{ $order->customer_data['email'] ?? 'N/D' }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-slate-400">Modalidad:</span>
        <span class="font-bold text-emerald-400 uppercase">{{ $order->customer_type === 'business' ? 'B2B Corporativo' : 'B2C Particular' }}</span>
      </div>
    </div>

    <!-- Actions Form -->
    <form method="POST" action="{{ route('shop.payment.sandbox.process') }}" class="space-y-3 pt-2">
      @csrf
      <input type="hidden" name="order_number" value="{{ $order->order_number }}">
      <input type="hidden" name="gateway_reference" value="{{ $gatewayRef }}">

      <button type="submit" name="action" value="approve" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm transition-all shadow-xl shadow-emerald-950/40 flex items-center justify-center gap-2 hover:scale-[1.01]">
        <i class="fa-solid fa-circle-check"></i>
        <span>Simular Pago Exitoso (${{ number_format((float)$order->total, 2) }} USD)</span>
      </button>

      <button type="submit" name="action" value="decline" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-800 hover:border-rose-500/40 font-semibold text-xs transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>Simular Cancelación / Rechazo</span>
      </button>
    </form>

  </div>

</div>
@endsection
