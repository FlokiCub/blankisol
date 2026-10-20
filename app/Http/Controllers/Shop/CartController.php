<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCartItem;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopProductVariant;
use App\Services\Shop\CartService;
use App\Services\Shop\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected ShippingService $shippingService
    ) {}

    /**
     * Resuelve el identificador de sesión para el carrito (soporta header, input o cookie de sesión).
     */
    protected function resolveSessionId(Request $request): string
    {
        return $request->header('X-Cart-Session')
            ?: ($request->input('cart_session') ?: $request->session()->getId());
    }

    /**
     * Muestra la vista del carrito de compras.
     */
    public function index(Request $request): View
    {
        $sessionId = $this->resolveSessionId($request);
        $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

        $province = $request->input('province', 'La Habana');
        $shippingRateId = $request->filled('shipping_rate_id')
            ? (int) $request->input('shipping_rate_id')
            : null;

        $summary = $this->cartService->getCartSummary($cart, $shippingRateId, $province);

        return view('shop.cart.index', compact('cart', 'summary', 'province'));
    }

    /**
     * Añade un ítem al carrito (vía AJAX o formulario).
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:shop_products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:shop_product_variants,id',
        ]);

        try {
            $sessionId = $this->resolveSessionId($request);
            $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

            $product = ShopProduct::findOrFail($validated['product_id']);
            $variant = !empty($validated['variant_id'])
                ? ShopProductVariant::find($validated['variant_id'])
                : null;

            $quantity = (int) ($validated['quantity'] ?? 1);

            $this->cartService->addItem($cart, $product, $quantity, $variant);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Se añadió [{$product->name}] al carrito.",
                    'cart_count' => $cart->fresh()->total_items,
                    'subtotal' => $cart->fresh()->subtotal,
                ]);
            }

            return back()->with('success', "Se añadió '{$product->name}' al carrito.");
        } catch (Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Actualiza la cantidad de un ítem en el carrito.
     */
    public function update(Request $request, ShopCartItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $this->cartService->updateQuantity($item, (int) $validated['quantity']);
            return back()->with('success', 'Cantidad actualizada en el carrito.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina un ítem del carrito.
     */
    public function remove(ShopCartItem $item): RedirectResponse
    {
        $this->cartService->removeItem($item);
        return back()->with('success', 'Producto removido del carrito.');
    }

    /**
     * Aplica cupón de descuento.
     */
    public function applyCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $sessionId = $this->resolveSessionId($request);
        $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

        $result = $this->cartService->applyCoupon($cart, $validated['coupon_code']);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Remueve cupón de descuento.
     */
    public function removeCoupon(Request $request): RedirectResponse
    {
        $sessionId = $this->resolveSessionId($request);
        $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

        $this->cartService->removeCoupon($cart);

        return back()->with('success', 'Cupón removido del carrito.');
    }
}
