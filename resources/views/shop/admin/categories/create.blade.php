@extends('shop.layouts.admin')

@section('page_title', 'Crear Categoría Comercial')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-emerald-400"></i>
        <span>Nueva Categoría</span>
      </h1>
      <p class="text-xs text-slate-400 mt-0.5">Define una línea de productos o subcategoría comercial.</p>
    </div>
    <a href="{{ route('shop.admin.categories.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-semibold transition-all">
      &larr; Volver
    </a>
  </div>

  <!-- Form -->
  <div class="glass-card rounded-3xl p-6 sm:p-8">
    <form method="POST" action="{{ route('shop.admin.categories.store') }}" class="space-y-5">
      @csrf

      <div>
        <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre de la Categoría *</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ej. Movilidad Eléctrica" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
      </div>

      <div>
        <label for="parent_id" class="block text-xs font-semibold text-slate-300 mb-1.5">Categoría Padre (Opcional)</label>
        <select name="parent_id" id="parent_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <option value="">-- Ninguna (Categoría Principal) --</option>
          @foreach($parentCategories as $parent)
            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
              {{ $parent->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="icon" class="block text-xs font-semibold text-slate-300 mb-1.5">Icono FontAwesome (Clase CSS)</label>
        <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-solid fa-layer-group') }}" placeholder="fa-solid fa-motorcycle" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
      </div>

      <div>
        <label for="description" class="block text-xs font-semibold text-slate-300 mb-1.5">Descripción Comercial</label>
        <textarea name="description" id="description" rows="3" placeholder="Breve descripción que describe los productos contenidos en esta categoría..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('description') }}</textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="sort_order" class="block text-xs font-semibold text-slate-300 mb-1.5">Orden de Visualización</label>
          <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-mono">
        </div>

        <div class="flex items-center pt-6">
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-500">
            <span class="text-xs font-semibold text-slate-200">Categoría Activa en Tienda</span>
          </label>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
        <a href="{{ route('shop.admin.categories.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
          Cancelar
        </a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40">
          Guardar Categoría
        </button>
      </div>

    </form>
  </div>

</div>
@endsection
