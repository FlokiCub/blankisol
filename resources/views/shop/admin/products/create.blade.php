@extends('shop.layouts.admin')

@section('page_title', 'Publicar Producto Comercial')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-emerald-400"></i>
        <span>Nuevo Producto en Catálogo</span>
      </h1>
      <p class="text-xs text-slate-400 mt-0.5">Ingresa los datos comerciales, fijación de precios en USD y modalidad de inventario.</p>
    </div>
    <a href="{{ route('shop.admin.products.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-semibold transition-all">
      &larr; Volver al catálogo
    </a>
  </div>

  <!-- Form -->
  <form method="POST" action="{{ route('shop.admin.products.store') }}" class="space-y-6">
    @csrf

    <!-- Bloque 1: Datos Básicos del Producto -->
    <div class="glass-card rounded-3xl p-6 sm:p-7 space-y-5">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-emerald-400"></i>
        <span>Información General</span>
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label for="sku" class="block text-xs font-semibold text-slate-300 mb-1.5">Código SKU Oficial *</label>
          <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="MOTO-PRO-01" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs uppercase font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div class="sm:col-span-2">
          <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre Comercial del Producto *</label>
          <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Moto Eléctrica BLANKISOL Pro 72V" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="category_id" class="block text-xs font-semibold text-slate-300 mb-1.5">Categoría Comercial</label>
          <select name="category_id" id="category_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
            <option value="">-- Sin categoría asignada --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="short_description" class="block text-xs font-semibold text-slate-300 mb-1.5">Resumen Comercial Corto</label>
          <input type="text" name="short_description" id="short_description" value="{{ old('short_description') }}" placeholder="Vehículo eléctrico de alta autonomía y batería de litio..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>
      </div>

      <div>
        <label for="description" class="block text-xs font-semibold text-slate-300 mb-1.5">Ficha Técnica & Descripción Detallada</label>
        <textarea name="description" id="description" rows="4" placeholder="Especificaciones completas del producto, características mecánicas o eléctricas..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('description') }}</textarea>
      </div>
    </div>

    <!-- Bloque 2: Precios y Moneda USD -->
    <div class="glass-card rounded-3xl p-6 sm:p-7 space-y-5">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-dollar-sign text-emerald-400"></i>
        <span>Estructura de Precios (Moneda Base: USD)</span>
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label for="price" class="block text-xs font-semibold text-slate-300 mb-1.5">Precio Regular (USD) *</label>
          <div class="relative">
            <span class="absolute left-3.5 top-2.5 text-slate-400 font-mono text-xs">$</span>
            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" required placeholder="1250.00" class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
        </div>

        <div>
          <label for="sale_price" class="block text-xs font-semibold text-slate-300 mb-1.5">Precio de Oferta (USD, Opcional)</label>
          <div class="relative">
            <span class="absolute left-3.5 top-2.5 text-slate-400 font-mono text-xs">$</span>
            <input type="number" step="0.01" min="0" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" placeholder="1100.00" class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">Debe ser menor al precio regular</span>
        </div>

        <div>
          <label for="cost_price" class="block text-xs font-semibold text-slate-300 mb-1.5">Costo Unitario (USD, Confidencial)</label>
          <div class="relative">
            <span class="absolute left-3.5 top-2.5 text-slate-400 font-mono text-xs">$</span>
            <input type="number" step="0.01" min="0" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" placeholder="800.00" class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">Visible sólo en administración para cálculo de margen</span>
        </div>
      </div>
    </div>

    <!-- Bloque 3: Modalidad de Inventario Mixto -->
    <div class="glass-card rounded-3xl p-6 sm:p-7 space-y-5">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-warehouse text-emerald-400"></i>
        <span>Modalidad de Existencias e Inventario</span>
      </h2>

      <div>
        <label class="block text-xs font-semibold text-slate-300 mb-2">Selecciona la Modalidad de Comercialización *</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          
          <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-3 bg-slate-900/60 border-slate-700 hover:border-emerald-500/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
            <input type="radio" name="inventory_type" value="controlled_stock" id="inv_controlled" {{ old('inventory_type', 'controlled_stock') === 'controlled_stock' ? 'checked' : '' }} onchange="toggleInventoryInputs()" class="mt-1 text-emerald-500 focus:ring-emerald-500">
            <div>
              <span class="font-bold text-white text-xs block">Stock Físico Controlado</span>
              <p class="text-[11px] text-slate-400 mt-0.5">El producto cuenta con unidades físicas reales. Bloquea compras cuando el stock llega a 0.</p>
            </div>
          </label>

          <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-3 bg-slate-900/60 border-slate-700 hover:border-purple-500/50 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
            <input type="radio" name="inventory_type" value="on_demand" id="inv_on_demand" {{ old('inventory_type') === 'on_demand' ? 'checked' : '' }} onchange="toggleInventoryInputs()" class="mt-1 text-purple-500 focus:ring-purple-500">
            <div>
              <span class="font-bold text-white text-xs block">Bajo Pedido (On Demand)</span>
              <p class="text-[11px] text-slate-400 mt-0.5">No requiere stock físico previo. Permite compras continuas indicando tiempo estimado de entrega.</p>
            </div>
          </label>

        </div>
      </div>

      <!-- Campos para Stock Controlado -->
      <div id="controlled_stock_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
          <label for="stock_quantity" class="block text-xs font-semibold text-slate-300 mb-1.5">Cantidad Inicial en Almacén *</label>
          <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 10) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="min_stock_alert" class="block text-xs font-semibold text-slate-300 mb-1.5">Umbral de Alerta de Stock Bajo</label>
          <input type="number" min="0" name="min_stock_alert" id="min_stock_alert" value="{{ old('min_stock_alert', 5) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>
      </div>

      <!-- Campos para On Demand / Logística -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
        <div>
          <label for="estimated_delivery_days" class="block text-xs font-semibold text-slate-300 mb-1.5">Plazo Estimado de Entrega</label>
          <input type="text" name="estimated_delivery_days" id="estimated_delivery_days" value="{{ old('estimated_delivery_days', '15 a 20 días hábiles') }}" placeholder="15 a 20 días hábiles" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="weight_kg" class="block text-xs font-semibold text-slate-300 mb-1.5">Peso Estimado (kg)</label>
          <input type="number" step="0.01" min="0" name="weight_kg" id="weight_kg" value="{{ old('weight_kg') }}" placeholder="45.5" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="dimensions" class="block text-xs font-semibold text-slate-300 mb-1.5">Dimensiones (cm)</label>
          <input type="text" name="dimensions" id="dimensions" value="{{ old('dimensions') }}" placeholder="180 x 70 x 110" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>
      </div>
    </div>

    <!-- Bloque 4: Visibilidad y SEO -->
    <div class="glass-card rounded-3xl p-6 sm:p-7 space-y-5">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-globe text-emerald-400"></i>
        <span>Publicación & SEO</span>
      </h2>

      <div class="flex flex-wrap items-center gap-6">
        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-500">
          <span class="text-xs font-semibold text-slate-200">Publicar Inmediatamente en Catálogo</span>
        </label>

        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500">
          <span class="text-xs font-semibold text-slate-200">Destacar en la Portada de la Tienda</span>
        </label>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
          <label for="seo_title" class="block text-xs font-semibold text-slate-300 mb-1.5">Título SEO (Meta Title)</label>
          <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title') }}" placeholder="Comprar Moto Eléctrica en Cuba | BLANKISOL" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>

        <div>
          <label for="seo_description" class="block text-xs font-semibold text-slate-300 mb-1.5">Descripción SEO (Meta Description)</label>
          <input type="text" name="seo_description" id="seo_description" value="{{ old('seo_description') }}" placeholder="Adquiere vehículos eléctricos con garantía oficial y entrega segura en Cuba..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('shop.admin.products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
        Cancelar
      </a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40 hover:scale-[1.01]">
        Guardar y Publicar Producto
      </button>
    </div>

  </form>

</div>

<script>
  function toggleInventoryInputs() {
    const isControlled = document.getElementById('inv_controlled').checked;
    const stockField = document.getElementById('controlled_stock_fields');
    if (isControlled) {
      stockField.classList.remove('hidden');
    } else {
      stockField.classList.add('hidden');
    }
  }
  // Run on load
  document.addEventListener('DOMContentLoaded', toggleInventoryInputs);
</script>
@endsection
