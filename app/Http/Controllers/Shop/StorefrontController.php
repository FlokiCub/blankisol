<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * Portada Principal de la Tienda Virtual (Home /shop).
     */
    public function index(): View
    {
        $categories = ShopCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->take(6)
            ->get();

        $featuredProducts = ShopProduct::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest('id')
            ->take(8)
            ->get();

        $onDemandProducts = ShopProduct::with('category')
            ->where('is_active', true)
            ->where('inventory_type', 'on_demand')
            ->latest('id')
            ->take(4)
            ->get();

        $latestProducts = ShopProduct::with('category')
            ->where('is_active', true)
            ->latest('id')
            ->take(8)
            ->get();

        return view('shop.storefront.index', compact(
            'categories',
            'featuredProducts',
            'onDemandProducts',
            'latestProducts'
        ));
    }

    /**
     * Catálogo General de Productos y Filtros (/shop/products).
     */
    public function products(Request $request): View
    {
        $query = ShopProduct::with('category')->where('is_active', true);

        // 1. Filtro por búsqueda
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('short_description', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        // 2. Filtro por categoría (id o slug)
        if ($request->filled('category')) {
            $categoryParam = $request->input('category');
            $query->whereHas('category', function ($q) use ($categoryParam) {
                if (is_numeric($categoryParam)) {
                    $q->where('id', $categoryParam);
                } else {
                    $q->where('slug', $categoryParam);
                }
            });
        }

        // 3. Filtro por modalidad de inventario (físico vs bajo pedido)
        if ($request->filled('inventory_type')) {
            $query->where('inventory_type', $request->input('inventory_type'));
        }

        // 4. Filtro por rango de precio USD
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // 5. Ordenamiento
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'featured' => $query->orderBy('is_featured', 'desc')->latest('id'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();

        return view('shop.storefront.products', compact('products', 'categories'));
    }

    /**
     * Productos por Categoría (/shop/categories/{slug}).
     */
    public function category(string $slug): View
    {
        $category = ShopCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = ShopProduct::with('category')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->latest('id')
            ->paginate(12);

        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();

        return view('shop.storefront.category', compact('category', 'products', 'categories'));
    }

    /**
     * Ficha de Detalle de Producto (/shop/products/{slug}).
     */
    public function show(string $slug): View
    {
        $product = ShopProduct::with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = ShopProduct::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->latest('id')
            ->take(4)
            ->get();

        return view('shop.storefront.show', compact('product', 'relatedProducts'));
    }
}
