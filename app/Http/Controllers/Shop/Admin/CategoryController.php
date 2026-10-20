<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = ShopCategory::with(['parent'])
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('shop.admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = ShopCategory::whereNull('parent_id')->get();
        return view('shop.admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'parent_id' => 'nullable|exists:shop_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:60',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Evitar slugs duplicados
        $count = ShopCategory::where('slug', 'like', $validated['slug'] . '%')->count();
        if ($count > 0) {
            $validated['slug'] .= '-' . ($count + 1);
        }

        $validated['is_active'] = $request->has('is_active');

        ShopCategory::create($validated);

        return redirect()->route('shop.admin.categories.index')
            ->with('success', 'Categoría comercial creada correctamente.');
    }

    public function edit(ShopCategory $category): View
    {
        $parentCategories = ShopCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('shop.admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, ShopCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'parent_id' => 'nullable|exists:shop_categories,id|not_in:' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:60',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('shop.admin.categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function toggleStatus(ShopCategory $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', "Estado de la categoría '{$category->name}' actualizado.");
    }
}
