<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingController extends Controller
{
    public function index(): View
    {
        $zones = ShopShippingZone::with('rates')->orderBy('name')->get();
        return view('shop.admin.shipping.index', compact('zones'));
    }

    public function storeZone(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'provinces' => 'nullable|string', // Provincias separadas por coma
        ]);

        $provincesArray = !empty($validated['provinces']) 
            ? array_map('trim', explode(',', $validated['provinces'])) 
            : [];

        ShopShippingZone::create([
            'name' => $validated['name'],
            'provinces' => $provincesArray,
            'is_active' => true,
        ]);

        return redirect()->route('shop.admin.shipping.index')
            ->with('success', 'Zona de entrega creada exitosamente.');
    }

    public function storeRate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:shop_shipping_zones,id',
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:60',
        ]);

        ShopShippingRate::create([
            'zone_id' => $validated['zone_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'estimated_days' => $validated['estimated_days'],
            'is_active' => true,
        ]);

        return redirect()->route('shop.admin.shipping.index')
            ->with('success', 'Tarifa de entrega en USD agregada a la zona.');
    }

    public function destroyRate(ShopShippingRate $rate): RedirectResponse
    {
        $rate->delete();

        return redirect()->route('shop.admin.shipping.index')
            ->with('success', 'Tarifa de entrega eliminada.');
    }
}
