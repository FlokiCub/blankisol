@extends('shop.layouts.admin')

@section('page_title', 'Cupones & Promociones Comerciales')

@section('content')
<div class="space-y-6">

  <!-- Header -->
  <div class="glass-card rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-ticket text-emerald-400"></i>
        <span>Promociones & Códigos de Descuento (USD)</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        Crea cupones de rebaja porcentual o fija en USD, con límites de canje y fechas de vigencia.
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Coupons List (Span 2) -->
    <div class="lg:col-span-2 glass-card rounded-3xl p-6">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-tags text-emerald-400"></i>
        <span>Cupones Registrados</span>
      </h2>

      <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-slate-400 border-b border-slate-800">
              <th class="pb-3 font-semibold">Código / Campaña</th>
              <th class="pb-3 font-semibold text-center">Tipo Descuento</th>
              <th class="pb-3 font-semibold text-center">Valor</th>
              <th class="pb-3 font-semibold text-center">Compra Mínima</th>
              <th class="pb-3 font-semibold text-center">Vigencia</th>
              <th class="pb-3 font-semibold text-center">Usos / Límite</th>
              <th class="pb-3 font-semibold text-center">Estado</th>
              <th class="pb-3 font-semibold text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            @forelse($coupons as $coupon)
              <tr class="hover:bg-slate-900/40 transition-colors">
                <td class="py-3.5">
                  <span class="font-mono text-xs font-black text-emerald-400 tracking-wider block">
                    {{ $coupon->code }}
                  </span>
                  <span class="text-white font-medium text-xs block">
                    {{ $coupon->name }}
                  </span>
                </td>
                <td class="py-3.5 text-center">
                  @if($coupon->isPercentage())
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">Porcentual</span>
                  @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">Monto Fijo</span>
                  @endif
                </td>
                <td class="py-3.5 text-center font-mono font-bold text-white">
                  @if($coupon->isPercentage())
                    {{ number_format((float)$coupon->discount_value, 0) }}%
                    @if($coupon->max_discount_amount)
                      <span class="text-[9px] text-slate-400 block font-normal">(Max: ${{ number_format((float)$coupon->max_discount_amount, 2) }})</span>
                    @endif
                  @else
                    ${{ number_format((float)$coupon->discount_value, 2) }} USD
                  @endif
                </td>
                <td class="py-3.5 text-center font-mono text-slate-300">
                  @if($coupon->min_order_amount)
                    ${{ number_format((float)$coupon->min_order_amount, 2) }}
                  @else
                    <span class="text-slate-500">Sin mínimo</span>
                  @endif
                </td>
                <td class="py-3.5 text-center text-[10px] text-slate-400 font-mono">
                  @if($coupon->expires_at)
                    {{ $coupon->expires_at->format('d/m/Y') }}
                  @else
                    <span class="text-emerald-400 font-bold">Permanente</span>
                  @endif
                </td>
                <td class="py-3.5 text-center font-mono text-slate-300">
                  {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                </td>
                <td class="py-3.5 text-center">
                  <form method="POST" action="{{ route('shop.admin.coupons.toggle-status', $coupon) }}">
                    @csrf
                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-semibold transition-all {{ $coupon->is_active ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}">
                      {{ $coupon->is_active ? 'Activo' : 'Pausado' }}
                    </button>
                  </form>
                </td>
                <td class="py-3.5 text-right">
                  <form method="POST" action="{{ route('shop.admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('¿Deseas eliminar este cupón?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 transition-colors" title="Eliminar cupón">
                      <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="py-12 text-center text-slate-500">
                  <i class="fa-solid fa-ticket text-3xl mb-2 text-slate-600 block"></i>
                  No hay cupones de descuento configurados aún.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($coupons->hasPages())
        <div class="pt-4 border-t border-slate-800">
          {{ $coupons->links() }}
        </div>
      @endif
    </div>

    <!-- Right Column: Create Coupon Form (Span 1) -->
    <div class="glass-card rounded-3xl p-6">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-emerald-400"></i>
        <span>Crear Nuevo Cupón</span>
      </h2>

      <form method="POST" action="{{ route('shop.admin.coupons.store') }}" class="mt-4 space-y-4">
        @csrf

        <div>
          <label for="code" class="block text-xs font-semibold text-slate-300 mb-1.5">Código del Cupón *</label>
          <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="VERANO10" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs uppercase font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre / Motivo de la Campaña *</label>
          <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Descuento Apertura Temporada" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="discount_type" class="block text-xs font-semibold text-slate-300 mb-1.5">Tipo *</label>
            <select name="discount_type" id="discount_type" required class="w-full px-2.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
              <option value="percentage">Porcentual (%)</option>
              <option value="fixed">Fijo ($ USD)</option>
            </select>
          </div>

          <div>
            <label for="discount_value" class="block text-xs font-semibold text-slate-300 mb-1.5">Valor *</label>
            <input type="number" step="0.01" min="0.01" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" required placeholder="10.00" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="min_order_amount" class="block text-xs font-semibold text-slate-300 mb-1.5">Mínimo Compra ($)</label>
            <input type="number" step="0.01" min="0" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount') }}" placeholder="100.00" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>

          <div>
            <label for="max_discount_amount" class="block text-xs font-semibold text-slate-300 mb-1.5">Tope Descuento ($)</label>
            <input type="number" step="0.01" min="0" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount') }}" placeholder="50.00" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="starts_at" class="block text-xs font-semibold text-slate-300 mb-1.5">Inicio Vigencia</label>
            <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>

          <div>
            <label for="expires_at" class="block text-xs font-semibold text-slate-300 mb-1.5">Fin Vigencia</label>
            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
        </div>

        <div>
          <label for="usage_limit" class="block text-xs font-semibold text-slate-300 mb-1.5">Límite de Canjes (Opcional)</label>
          <input type="number" min="1" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" placeholder="Ej. 100 usos en total" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div class="pt-2">
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-500">
            <span class="text-xs font-semibold text-slate-200">Cupón Activo Inmediatamente</span>
          </label>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40">
          Guardar y Habilitar Cupón
        </button>
      </form>
    </div>

  </div>

</div>
@endsection
