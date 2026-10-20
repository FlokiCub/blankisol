<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = ShopProduct::with('category')->latest('id');

        // Filtro por término de búsqueda (nombre o SKU)
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filtro por modalidad de inventario
        if ($request->filled('inventory_type')) {
            $query->where('inventory_type', $request->input('inventory_type'));
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = ShopCategory::orderBy('name')->get();

        return view('shop.admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();
        return view('shop.admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:shop_categories,id',
            'sku' => 'required|string|max:60|unique:shop_products,sku',
            'name' => 'required|string|max:200',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'inventory_type' => 'required|in:controlled_stock,on_demand',
            'stock_quantity' => 'required_if:inventory_type,controlled_stock|integer|min:0',
            'min_stock_alert' => 'integer|min:0',
            'estimated_delivery_days' => 'nullable|string|max:60',
            'weight_kg' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:80',
            'seo_title' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $count = ShopProduct::where('slug', 'like', $validated['slug'] . '%')->count();
        if ($count > 0) {
            $validated['slug'] .= '-' . ($count + 1);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['stock_quantity'] = $validated['inventory_type'] === 'on_demand' 
            ? 0 
            : (int) ($validated['stock_quantity'] ?? 0);

        ShopProduct::create($validated);

        return redirect()->route('shop.admin.products.index')
            ->with('success', 'Producto comercial publicado exitosamente en el catálogo.');
    }

    public function edit(ShopProduct $product): View
    {
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();
        return view('shop.admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, ShopProduct $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:shop_categories,id',
            'sku' => 'required|string|max:60|unique:shop_products,sku,' . $product->id,
            'name' => 'required|string|max:200',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'inventory_type' => 'required|in:controlled_stock,on_demand',
            'stock_quantity' => 'required_if:inventory_type,controlled_stock|integer|min:0',
            'min_stock_alert' => 'integer|min:0',
            'estimated_delivery_days' => 'nullable|string|max:60',
            'weight_kg' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:80',
            'seo_title' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        if ($validated['inventory_type'] === 'on_demand') {
            $validated['stock_quantity'] = 0;
        }

        $product->update($validated);

        return redirect()->route('shop.admin.products.index')
            ->with('success', 'Producto comercial actualizado correctamente.');
    }

    public function toggleStatus(ShopProduct $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', "Estado del producto [{$product->sku}] modificado con éxito.");
    }

    public function destroy(ShopProduct $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('shop.admin.products.index')
            ->with('success', "Producto [{$product->sku}] eliminado del catálogo.");
    }
}
