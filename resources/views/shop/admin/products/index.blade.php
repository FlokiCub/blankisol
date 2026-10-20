@extends('shop.layouts.admin')

@section('page_title', 'Catálogo de Productos')

@section('content')
<div class="space-y-6">

  <!-- Header / Action bar -->
  <div class="glass-card rounded-3xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
        <i class="fa-solid fa-box-open text-emerald-400"></i>
        <span>Catálogo de Productos Comerciales</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        Administración de precios en USD, inventario físico controlado vs. productos bajo pedido (On Demand).
      </p>
    </div>

    <a href="{{ route('shop.admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-lg shadow-emerald-950/40 hover:scale-[1.02]">
      <i class="fa-solid fa-plus"></i>
      <span>Nuevo Producto</span>
    </a>
  </div>

  <!-- Filters & Search Bar -->
  <div class="glass-card rounded-3xl p-5">
    <form method="GET" action="{{ route('shop.admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      
      <!-- Búsqueda por SKU o Nombre -->
      <div class="lg:col-span-2">
        <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Buscar Producto</label>
        <div class="relative">
          <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nombre o SKU (ej. MOTO-001)..." class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs absolute left-3 top-3"></i>
        </div>
      </div>

      <!-- Filtro Categoría -->
      <div>
        <label for="category_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Categoría</label>
        <select name="category_id" id="category_id" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <option value="">Todas las categorías</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Filtro Modalidad Inventario -->
      <div>
        <label for="inventory_type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Modalidad Stock</label>
        <select name="inventory_type" id="inventory_type" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
          <option value="">Todas las modalidades</option>
          <option value="controlled_stock" {{ request('inventory_type') === 'controlled_stock' ? 'selected' : '' }}>Stock Controlado (Físico)</option>
          <option value="on_demand" {{ request('inventory_type') === 'on_demand' ? 'selected' : '' }}>Bajo Pedido (On Demand)</option>
        </select>
      </div>

      <!-- Botón Filtrar -->
      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition-all">
          <i class="fa-solid fa-filter mr-1"></i> Filtrar
        </button>
        @if(request()->hasAny(['search', 'category_id', 'inventory_type', 'status']))
          <a href="{{ route('shop.admin.products.index') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs" title="Limpiar filtros">
            <i class="fa-solid fa-rotate-left"></i>
          </a>
        @endif
      </div>

    </form>
  </div>

  <!-- Products Table -->
  <div class="glass-card rounded-3xl overflow-hidden p-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-slate-400 border-b border-slate-800">
            <th class="pb-3 font-semibold">SKU / Producto</th>
            <th class="pb-3 font-semibold">Categoría</th>
            <th class="pb-3 font-semibold text-right">Precio USD</th>
            <th class="pb-3 font-semibold text-center">Modalidad & Stock</th>
            <th class="pb-3 font-semibold text-center">Estado</th>
            <th class="pb-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
          @forelse($products as $product)
            <tr class="hover:bg-slate-900/40 transition-colors">
              <td class="py-3.5">
                <div>
                  <span class="font-mono text-[10px] text-emerald-400 font-bold uppercase tracking-wider block">
                    {{ $product->sku }}
                  </span>
                  <span class="font-bold text-white text-sm block">
                    {{ $product->name }}
                  </span>
                  @if($product->is_featured)
                    <span class="inline-flex items-center gap-1 text-[9px] px-1.5 py-0.2 rounded bg-amber-500/15 text-amber-300 border border-amber-500/25 font-bold uppercase mt-0.5">
                      <i class="fa-solid fa-star text-[8px]"></i> Destacado
                    </span>
                  @endif
                </div>
              </td>
              <td class="py-3.5 text-slate-300">
                {{ $product->category?->name ?? 'Sin categoría' }}
              </td>
              <td class="py-3.5 text-right font-mono">
                @if($product->sale_price && $product->sale_price < $product->price)
                  <span class="text-emerald-400 font-bold block">${{ number_format((float)$product->sale_price, 2) }}</span>
                  <span class="text-slate-500 line-through text-[10px] block">${{ number_format((float)$product->price, 2) }}</span>
                @else
                  <span class="text-white font-bold">${{ number_format((float)$product->price, 2) }}</span>
                @endif
              </td>
              <td class="py-3.5 text-center">
                @if($product->isOnDemand())
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30">
                    Bajo Pedido ({{ $product->estimated_delivery_days ?: 'Plazo variable' }})
                  </span>
                @else
                  @if($product->stock_quantity == 0)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-rose-500/20 text-rose-300 border border-rose-500/30">
                      Agotado (0 unids)
                    </span>
                  @elseif($product->stock_quantity <= 5)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30">
                      Stock Crítico ({{ $product->stock_quantity }} unids)
                    </span>
                  @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                      {{ $product->stock_quantity }} en stock
                    </span>
                  @endif
                @endif
              </td>
              <td class="py-3.5 text-center">
                <form method="POST" action="{{ route('shop.admin.products.toggle-status', $product) }}">
                  @csrf
                  <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-semibold transition-all {{ $product->is_active ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}">
                    {{ $product->is_active ? 'Publicado' : 'Borrador' }}
                  </button>
                </form>
              </td>
              <td class="py-3.5 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <a href="{{ route('shop.admin.products.edit', $product) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors" title="Editar producto">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                  </a>
                  <form method="POST" action="{{ route('shop.admin.products.destroy', $product) }}" onsubmit="return confirm('¿Confirmas eliminar este producto del catálogo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 transition-colors" title="Eliminar producto">
                      <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-12 text-center text-slate-500">
                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600 block"></i>
                No se encontraron productos registrados con los filtros seleccionados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
      <div class="pt-4 border-t border-slate-800">
        {{ $products->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
