@extends('shop.layouts.store')

@section('title', 'Finalizar Compra Segura — BLANKISOL SHOP (USD)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

  <!-- Header & Progress -->
  <div>
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
      <a href="{{ route('shop.index') }}" class="hover:text-emerald-700 transition">Tienda</a>
      <span>/</span>
      <a href="{{ route('shop.cart.index') }}" class="hover:text-emerald-700 transition">Carrito</a>
      <span>/</span>
      <span class="text-emerald-700 font-bold">Checkout Seguro</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
      <i class="fa-solid fa-lock text-emerald-600"></i>
      <span>Checkout Comercial B2C / B2B</span>
    </h1>
  </div>

  <form method="POST" action="{{ route('shop.checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    @csrf
    <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

    <!-- Left Column: Customer & Shipping Details (Span 2) -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- Bloque 1: Tipo de Cliente (B2C vs B2B) -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-user-tag text-emerald-600"></i>
          <span>1. Modalidad de Facturación & Perfil</span>
        </h2>

        <div>
          <label class="block text-xs font-bold text-slate-800 mb-2">Selecciona tu perfil de compra *</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-3 bg-slate-50 border-slate-200 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 shadow-2xs">
              <input type="radio" name="customer_type" value="individual" id="type_individual" {{ old('customer_type', 'individual') === 'individual' ? 'checked' : '' }} onchange="toggleCustomerType()" class="mt-1 text-emerald-600 focus:ring-emerald-500">
              <div>
                <span class="font-bold text-slate-900 text-xs block">Consumidor Particular (B2C)</span>
                <p class="text-[11px] text-slate-500 mt-0.5">Compra a título personal con entrega directa a domicilio o familiar en Cuba.</p>
              </div>
            </label>

            <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-3 bg-slate-50 border-slate-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 shadow-2xs">
              <input type="radio" name="customer_type" value="business" id="type_business" {{ old('customer_type') === 'business' ? 'checked' : '' }} onchange="toggleCustomerType()" class="mt-1 text-blue-600 focus:ring-blue-500">
              <div>
                <span class="font-bold text-slate-900 text-xs block">Empresa / MIPYME (B2B)</span>
                <p class="text-[11px] text-slate-500 mt-0.5">Facturación comercial, datos fiscales, contrato corporativo y despacho institucional.</p>
              </div>
            </label>

          </div>
        </div>

        <!-- Campos B2C (Particulares) -->
        <div id="fields_individual" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <div>
            <label for="first_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nombres *</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', auth()->user()?->name) }}" placeholder="Juan Carlos" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>

          <div>
            <label for="last_name" class="block text-xs font-bold text-slate-700 mb-1.5">Apellidos *</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="Pérez González" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>

          <div>
            <label for="id_number" class="block text-xs font-bold text-slate-700 mb-1.5">Carnet de Identidad / Pasaporte</label>
            <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" placeholder="Ej. 850312..." class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>

          <div>
            <label for="phone_ind" class="block text-xs font-bold text-slate-700 mb-1.5">Teléfono Celular / WhatsApp *</label>
            <input type="tel" name="phone" id="phone_ind" value="{{ old('phone') }}" placeholder="+53 5123 4567" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>
        </div>

        <!-- Campos B2B (Empresas) -->
        <div id="fields_business" class="hidden space-y-4 pt-2">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="business_name" class="block text-xs font-bold text-slate-700 mb-1.5">Razón Social / Nombre Comercial *</label>
              <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" placeholder="Constructora Caribe S.U.R.L." class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
            </div>

            <div>
              <label for="tax_id" class="block text-xs font-bold text-slate-700 mb-1.5">ID Fiscal / CIF / Código Registral *</label>
              <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" placeholder="CIF-B89201923" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
            </div>
          </div>
        </div>

        <!-- Campo Email común -->
        <div>
          <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Correo Electrónico de Notificaciones *</label>
          <input type="email" name="email" id="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="correo@ejemplo.com" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white" required>
        </div>

      </div>

      <!-- Bloque 2: Dirección de Entrega en Cuba -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-location-dot text-emerald-600"></i>
            <span>2. Dirección de Destino en Cuba</span>
          </h2>
          @auth
            <span class="text-[11px] text-slate-500 font-medium hidden sm:inline flex items-center gap-1">
              <i class="fa-solid fa-bookmark text-emerald-600"></i> Direcciones guardadas del cliente
            </span>
          @endauth
        </div>

        <!-- Selector de Direcciones Favoritas Guardadas (Para Clientes Autenticados) -->
        @if(isset($savedAddresses) && $savedAddresses->isNotEmpty())
          <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2.5">
            <div class="flex items-center justify-between">
              <label for="saved_address_selector" class="text-xs font-bold text-emerald-950 flex items-center gap-1.5">
                <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                <span>Tus Direcciones de Entrega Guardadas</span>
              </label>
              <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-300">
                {{ $savedAddresses->count() }} disponible{{ $savedAddresses->count() > 1 ? 's' : '' }}
              </span>
            </div>

            <select 
              id="saved_address_selector" 
              onchange="onSavedAddressSelected(this.value)"
              class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-emerald-300 text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs"
            >
              @foreach($savedAddresses as $addr)
                <option 
                  value="{{ $addr->id }}"
                  data-province="{{ $addr->province }}"
                  data-municipality="{{ $addr->municipality }}"
                  data-address="{{ $addr->address_line1 }}"
                  data-recipient-name="{{ $addr->recipient_name }}"
                  data-recipient-phone="{{ $addr->phone }}"
                  data-postal-code="{{ $addr->postal_code }}"
                  {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'selected' : '' }}
                >
                  {{ $addr->is_default ? '⭐ [Predeterminada] ' : '' }}{{ $addr->recipient_name }} — {{ $addr->province }}, {{ $addr->municipality }} ({{ Str::limit($addr->address_line1, 35) }})
                </option>
              @endforeach
              <option value="new">+ Escribir una nueva dirección de entrega...</option>
            </select>
            <p class="text-[11px] text-emerald-800/80">
              Selecciona una dirección de tu libreta para autocompletar automáticamente todos los datos de entrega.
            </p>
          </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="province" class="block text-xs font-bold text-slate-700 mb-1.5">Provincia de Destino *</label>
            <select 
              name="province" 
              id="province" 
              onchange="onCheckoutProvinceChanged()" 
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
              required
            >
              @php
                $provinciasCuba = [
                  'La Habana', 'Artemisa', 'Mayabeque', 'Matanzas', 
                  'Villa Clara', 'Cienfuegos', 'Sancti Spíritus', 'Ciego de Ávila', 
                  'Camagüey', 'Las Tunas', 'Holguín', 'Granma', 
                  'Santiago de Cuba', 'Guantánamo', 'Pinar del Río', 'Isla de la Juventud'
                ];
              @endphp
              @foreach($provinciasCuba as $p)
                <option value="{{ $p }}" {{ old('province', $province) === $p ? 'selected' : '' }}>{{ $p }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="municipality" class="block text-xs font-bold text-slate-700 mb-1.5">Municipio *</label>
            <select 
              name="municipality" 
              id="municipality" 
              onchange="onCheckoutMunicipalityChanged()" 
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
              required
            >
              <option value="Playa">Playa</option>
            </select>
          </div>
        </div>

        <!-- Indicador Dinámico de Factibilidad de Entrega -->
        <div id="checkout_delivery_status_box" class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i id="checkout_delivery_icon" class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span id="checkout_delivery_msg" class="font-semibold text-emerald-900">
              ✓ Entrega directa a domicilio disponible para este municipio.
            </span>
          </div>
          <span id="checkout_delivery_tag" class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-bold text-[10px]">
            Cobertura Activa
          </span>
        </div>

        <div>
          <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">Dirección Exacta (Calle, Número, e/ Calles) *</label>
          <input type="text" name="address" id="address" value="{{ old('address', $defaultAddress?->address_line1) }}" placeholder="Calle 42 No. 512 e/ 5ta y 7ma" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white" required>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="recipient_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nombre de Quien Recibe en Cuba</label>
            <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', $defaultAddress?->recipient_name) }}" placeholder="Persona en destino (si es diferente)" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>

          <div>
            <label for="recipient_phone" class="block text-xs font-bold text-slate-700 mb-1.5">Teléfono de Contacto Receptor</label>
            <input type="tel" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone', $defaultAddress?->phone) }}" placeholder="+53 5..." class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
          </div>
        </div>

        <div>
          <label for="delivery_notes" class="block text-xs font-bold text-slate-700 mb-1.5">Punto de Referencia / Notas de Entrega</label>
          <textarea name="delivery_notes" id="delivery_notes" rows="2" placeholder="Cerca del parque central, casa de rejas verdes..." class="w-full px-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">{{ old('delivery_notes') }}</textarea>
        </div>

        <!-- Opción para guardar la dirección en favoritos (Para Clientes Autenticados) -->
        @auth
          <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/70 p-3.5 rounded-2xl border border-slate-200/80">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
              <input 
                type="checkbox" 
                name="save_address" 
                value="1" 
                id="save_address" 
                checked 
                class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500"
              >
              <span>Guardar esta dirección en mi libreta de favoritas</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
              <input 
                type="checkbox" 
                name="is_default_address" 
                value="1" 
                id="is_default_address" 
                class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500"
              >
              <span>Establecer como principal</span>
            </label>
          </div>
        @endauth

      </div>

      <!-- Bloque 3: Modalidad y Tarifa de Entrega -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-5">
        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-truck-fast text-emerald-600"></i>
          <span>3. Modalidad de Entrega / Despacho en Cuba</span>
        </h2>

        <div class="space-y-3">
          @forelse($shippingZones as $zone)
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $zone->name }}</div>
            <div class="space-y-2">
              @foreach($zone->rates as $rate)
                <label class="p-4 rounded-2xl border cursor-pointer transition flex items-center justify-between bg-slate-50 border-slate-200 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 shadow-2xs">
                  <div class="flex items-center gap-3">
                    <input type="radio" name="shipping_rate_id" value="{{ $rate->id }}" {{ (old('shipping_rate_id') == $rate->id || $loop->first) ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                    <div>
                      <span class="font-bold text-slate-900 text-xs block">{{ $rate->name }}</span>
                      <span class="text-[11px] text-slate-500 block">
                        {{ $rate->estimated_days ? 'Tiempo estimado: ' . $rate->estimated_days : 'Entrega programada' }}
                      </span>
                    </div>
                  </div>
                  <span class="font-mono font-bold text-xs text-slate-900">
                    {{ $rate->price > 0 ? '$' . number_format((float)$rate->price, 2) . ' USD' : 'GRATIS' }}
                  </span>
                </label>
              @endforeach
            </div>
          @empty
            <div class="p-4 rounded-2xl bg-amber-50 text-amber-800 text-xs">
              No hay tarifas configuradas para esta zona. Se aplicará despacho estándar.
            </div>
          @endforelse
        </div>
      </div>

    </div>

    <!-- Right Column: Order Summary & Pay Action (Span 1) -->
    <div class="space-y-6">
      
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6 sticky top-28">
        
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-receipt text-emerald-600"></i>
          <span>Resumen de Liquidación</span>
        </h3>

        <!-- Ítems Breve -->
        <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1 text-xs divide-y divide-slate-100">
          @foreach($cart->items as $item)
            @if($item->product)
              <div class="pt-2 first:pt-0 flex justify-between items-center text-slate-700">
                <div class="min-w-0 pr-2">
                  <span class="font-semibold block truncate">{{ $item->product->name }}</span>
                  <span class="text-[10px] text-slate-400 font-mono">{{ $item->quantity }}x ${{ number_format((float)$item->unit_price, 2) }}</span>
                </div>
                <span class="font-mono font-bold text-slate-900 shrink-0">
                  ${{ number_format((float)($item->quantity * $item->unit_price), 2) }}
                </span>
              </div>
            @endif
          @endforeach
        </div>

        <!-- Desglose de Precios en USD -->
        <div class="space-y-3 pt-4 border-t border-slate-100 text-xs">
          <div class="flex justify-between text-slate-600">
            <span>Subtotal:</span>
            <span class="font-mono font-bold text-slate-900">${{ number_format($summary['subtotal'], 2) }} USD</span>
          </div>

          @if($summary['discount_amount'] > 0)
            <div class="flex justify-between text-emerald-700 font-semibold">
              <span>Cupón ({{ $summary['coupon_code'] }}):</span>
              <span class="font-mono">-${{ number_format($summary['discount_amount'], 2) }} USD</span>
            </div>
          @endif

          <div class="flex justify-between text-slate-600">
            <span>Tarifa de Envío:</span>
            <span class="font-mono font-bold text-slate-900">${{ number_format($summary['shipping_fee'], 2) }} USD</span>
          </div>

          <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
            <div>
              <strong class="text-slate-900 text-sm font-extrabold block">Total a Pagar:</strong>
              <span class="text-[10px] text-slate-400 font-mono">Dólares Estadounidenses</span>
            </div>
            <span class="text-2xl font-black text-slate-900 font-mono">
              ${{ number_format($summary['total'], 2) }} <span class="text-xs text-emerald-700 font-sans font-bold">USD</span>
            </span>
          </div>
        </div>

        <!-- Botón de Pago -->
        <div class="pt-2">
          <button type="submit" class="w-full py-4 px-4 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs sm:text-sm transition shadow-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock"></i>
            <span>Confirmar y Pagar en USD</span>
          </button>
        </div>

        <div class="text-[11px] text-slate-500 text-center space-y-1">
          <p><i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> Garantía de entrega directa y nacionalización en Cuba</p>
          <p class="text-[10px]">Al confirmar aceptas los términos comerciales de BLANKISOL S.A.</p>
        </div>

      </div>

    </div>

  </form>

</div>

@push('scripts')
<script>
  function toggleCustomerType() {
    const isBusiness = document.getElementById('type_business').checked;
    const fieldsInd = document.getElementById('fields_individual');
    const fieldsBus = document.getElementById('fields_business');

    if (isBusiness) {
      fieldsInd.classList.add('hidden');
      fieldsBus.classList.remove('hidden');
    } else {
      fieldsInd.classList.remove('hidden');
      fieldsBus.classList.add('hidden');
    }
  }

  function populateCheckoutMunicipalities(selectedProv, targetMun) {
    const munSelect = document.getElementById('municipality');
    if (!munSelect) return;
    
    const locations = window.CUBA_LOCATIONS || {
      "La Habana": ["Playa", "Plaza de la Revolución", "Centro Habana", "Habana Vieja", "Regla", "Habana del Este", "Guanabacoa", "San Miguel del Padrón", "Diez de Octubre", "Cerro", "Marianao", "La Lisa", "Boyeros", "Arroyo Naranjo", "Cotorro"]
    };

    const munList = locations[selectedProv] || ['Cabecera'];
    munSelect.innerHTML = '';
    
    munList.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m;
      opt.textContent = m;
      if (m === targetMun) opt.selected = true;
      munSelect.appendChild(opt);
    });

    if (!munSelect.value && munList.length > 0) {
      munSelect.value = munList[0];
    }

    updateCheckoutDeliveryFeedback(selectedProv, munSelect.value);
  }

  function updateCheckoutDeliveryFeedback(prov, mun) {
    const box = document.getElementById('checkout_delivery_status_box');
    const msg = document.getElementById('checkout_delivery_msg');
    const tag = document.getElementById('checkout_delivery_tag');
    const icon = document.getElementById('checkout_delivery_icon');

    if (box && typeof checkDeliveryAvailability === 'function') {
      const info = checkDeliveryAvailability(prov, mun);
      if (info.type === 'hub_only') {
        box.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between';
        msg.className = 'font-semibold text-amber-900';
        msg.textContent = `⚠ Entrega puerta a puerta no disponible para ${mun}. Retiro personal en almacén central de ${prov} o HUB Mariel.`;
        tag.className = 'px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-[10px]';
        tag.textContent = 'Retiro en Hub';
        icon.className = 'fa-solid fa-triangle-exclamation text-amber-600 text-sm';
      } else {
        box.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between';
        msg.className = 'font-semibold text-emerald-900';
        msg.textContent = `✓ Entrega directa a domicilio garantizada para ${mun}, ${prov}.`;
        tag.className = 'px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-bold text-[10px]';
        tag.textContent = 'Cobertura Activa';
        icon.className = 'fa-solid fa-circle-check text-emerald-600 text-sm';
      }
    }
  }

  function onSavedAddressSelected(val) {
    const sel = document.getElementById('saved_address_selector');
    const saveAddrCheckbox = document.getElementById('save_address');
    if (!sel) return;

    if (val === 'new') {
      document.getElementById('address').value = '';
      document.getElementById('recipient_name').value = '';
      document.getElementById('recipient_phone').value = '';
      if (saveAddrCheckbox) saveAddrCheckbox.checked = true;
      return;
    }

    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    const prov = opt.dataset.province;
    const mun = opt.dataset.municipality;
    const addr = opt.dataset.address;
    const rName = opt.dataset.recipientName;
    const rPhone = opt.dataset.recipientPhone;

    if (addr) document.getElementById('address').value = addr;
    if (rName) document.getElementById('recipient_name').value = rName;
    if (rPhone) document.getElementById('recipient_phone').value = rPhone;
    if (saveAddrCheckbox) saveAddrCheckbox.checked = false;

    if (prov) {
      const provSelect = document.getElementById('province');
      const currentProv = provSelect ? provSelect.value : null;
      if (currentProv && currentProv !== prov) {
        provSelect.value = prov;
        localStorage.setItem('blankisol_delivery_province', prov);
        localStorage.setItem('blankisol_delivery_municipality', mun || '');
        const url = new URL(window.location.href);
        url.searchParams.set('province', prov);
        window.location.href = url.toString();
        return;
      }
    }

    if (mun) {
      populateCheckoutMunicipalities(document.getElementById('province').value, mun);
      localStorage.setItem('blankisol_delivery_municipality', mun);
    }
  }

  function onCheckoutProvinceChanged() {
    const provSelect = document.getElementById('province');
    const prov = provSelect.value;
    localStorage.setItem('blankisol_delivery_province', prov);
    populateCheckoutMunicipalities(prov, null);
    
    // Recargar con la nueva provincia para actualizar las tarifas de envío
    const url = new URL(window.location.href);
    url.searchParams.set('province', prov);
    window.location.href = url.toString();
  }

  function onCheckoutMunicipalityChanged() {
    const mun = document.getElementById('municipality').value;
    const prov = document.getElementById('province').value;
    localStorage.setItem('blankisol_delivery_municipality', mun);
    updateCheckoutDeliveryFeedback(prov, mun);
  }

  document.addEventListener('DOMContentLoaded', () => {
    toggleCustomerType();

    const savedSelector = document.getElementById('saved_address_selector');
    const hasOldAddress = {{ old('address') ? 'true' : 'false' }};

    if (savedSelector && !hasOldAddress && savedSelector.value !== 'new') {
      const selectedOpt = savedSelector.options[savedSelector.selectedIndex];
      if (selectedOpt && selectedOpt.value !== 'new') {
        const prov = selectedOpt.dataset.province;
        const mun = selectedOpt.dataset.municipality;
        const provSelect = document.getElementById('province');
        if (provSelect && prov && provSelect.value === prov) {
          populateCheckoutMunicipalities(prov, mun);
          return;
        }
      }
    }

    const savedProv = localStorage.getItem('blankisol_delivery_province') || '{{ old("province", $province) }}';
    const savedMun = localStorage.getItem('blankisol_delivery_municipality') || '{{ old("municipality", "Playa") }}';
    
    const provSelect = document.getElementById('province');
    if (provSelect) {
      provSelect.value = savedProv;
    }
    populateCheckoutMunicipalities(savedProv, savedMun);
  });
</script>
@endpush
@endsection
