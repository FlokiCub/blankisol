<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCoupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = ShopCoupon::latest('id')->paginate(15);
        return view('shop.admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:40|unique:shop_coupons,code',
            'name' => 'required|string|max:120',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        ShopCoupon::create($validated);

        return redirect()->route('shop.admin.coupons.index')
            ->with('success', "Cupón comercial [{$validated['code']}] creado con éxito.");
    }

    public function toggleStatus(ShopCoupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', "Estado del cupón [{$coupon->code}] actualizado.");
    }

    public function destroy(ShopCoupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('shop.admin.coupons.index')
            ->with('success', "Cupón [{$coupon->code}] eliminado.");
    }
}
