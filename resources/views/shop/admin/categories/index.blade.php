@extends('shop.layouts.admin')

@section('page_title', 'Categorías Comerciales')

@section('content')
<div class="space-y-6">

  <!-- Header / Action bar -->
  <div class="glass-card rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-tags text-emerald-400"></i>
        <span>Estructura de Categorías</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        Organiza las líneas de productos y jerarquías visibles en la tienda online.
      </p>
    </div>

    <a href="{{ route('shop.admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40 hover:scale-[1.02]">
      <i class="fa-solid fa-plus"></i>
      <span>Nueva Categoría</span>
    </a>
  </div>

  <!-- Categories Table -->
  <div class="glass-card rounded-3xl overflow-hidden p-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 border-b border-slate-800">
            <th class="pb-3 font-semibold">Icono / Nombre</th>
            <th class="pb-3 font-semibold">Slug Comercial</th>
            <th class="pb-3 font-semibold">Categoría Padre</th>
            <th class="pb-3 font-semibold text-center">Orden</th>
            <th class="pb-3 font-semibold text-center">Productos</th>
            <th class="pb-3 font-semibold text-center">Estado</th>
            <th class="pb-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @forelse($categories as $category)
            <tr class="hover:bg-slate-900/40 transition-colors">
              <td class="py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0">
                    <i class="{{ $category->icon ?: 'fa-solid fa-folder' }}"></i>
                  </div>
                  <div>
                    <span class="font-bold text-white text-sm block">{{ $category->name }}</span>
                    @if($category->description)
                      <span class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $category->description }}</span>
                    @endif
                  </div>
                </div>
              </td>
              <td class="py-3.5 font-mono text-slate-300">
                {{ $category->slug }}
              </td>
              <td class="py-3.5 text-slate-300">
                @if($category->parent)
                  <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                    {{ $category->parent->name }}
                  </span>
                @else
                  <span class="text-slate-500 italic text-[11px]">Principal (Raíz)</span>
                @endif
              </td>
              <td class="py-3.5 text-center font-mono text-slate-300">
                {{ $category->sort_order }}
              </td>
              <td class="py-3.5 text-center">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                  {{ $category->products_count }}
                </span>
              </td>
              <td class="py-3.5 text-center">
                <form method="POST" action="{{ route('shop.admin.categories.toggle-status', $category) }}">
                  @csrf
                  <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-semibold transition-all {{ $category->is_active ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}">
                    {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                  </button>
                </form>
              </td>
              <td class="py-3.5 text-right">
                <div class="inline-flex items-center gap-2">
                  <a href="{{ route('shop.admin.categories.edit', $category) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors" title="Editar categoría">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-12 text-center text-slate-500">
                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-600 block"></i>
                No hay categorías creadas aún.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
