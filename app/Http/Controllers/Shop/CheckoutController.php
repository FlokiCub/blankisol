<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopCustomerAddress;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopShippingZone;
use App\Services\Shop\CartService;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\ShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected ShippingService $shippingService,
        protected CheckoutService $checkoutService
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
     * Muestra el formulario de checkout dual B2C & B2B con soporte para direcciones favoritas.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $sessionId = $this->resolveSessionId($request);
        $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Tu carrito está vacío. Agrega productos antes de proceder al checkout.');
        }

        $savedAddresses = collect();
        $defaultAddress = null;
        if (auth()->check()) {
            $customer = ShopCustomer::where('user_id', auth()->id())->first();
            if ($customer) {
                $savedAddresses = $customer->addresses()->orderByDesc('is_default')->latest()->get();
                $defaultAddress = $savedAddresses->firstWhere('is_default', true) ?: $savedAddresses->first();
            }
        }

        $province = $request->input('province', $defaultAddress?->province ?? 'La Habana');
        $shippingRateId = $request->filled('shipping_rate_id')
            ? (int) $request->input('shipping_rate_id')
            : null;

        $summary = $this->cartService->getCartSummary($cart, $shippingRateId, $province);
        $shippingZones = ShopShippingZone::with(['rates' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        $idempotencyKey = (string) Str::uuid();

        return view('shop.checkout.index', compact(
            'cart',
            'summary',
            'province',
            'shippingZones',
            'idempotencyKey',
            'savedAddresses',
            'defaultAddress'
        ));
    }

    /**
     * Procesa la compra de manera atómica con snapshot inmutable en USD.
     */
    public function process(Request $request): RedirectResponse
    {
        $customerType = $request->input('customer_type', 'individual');

        $rules = [
            'customer_type' => 'required|in:individual,business',
            'email' => 'required|email|max:120',
            'phone' => 'required|string|max:40',
            'address' => 'required|string|max:200',
            'province' => 'required|string|max:80',
            'municipality' => 'required|string|max:80',
            'postal_code' => 'nullable|string|max:20',
            'recipient_name' => 'nullable|string|max:120',
            'recipient_phone' => 'nullable|string|max:40',
            'shipping_rate_id' => 'required|exists:shop_shipping_rates,id',
            'delivery_notes' => 'nullable|string|max:500',
            'order_notes' => 'nullable|string|max:500',
            'idempotency_key' => 'nullable|string|max:64',
            'cart_session' => 'nullable|string|max:100',
        ];

        if ($customerType === 'business') {
            $rules['business_name'] = 'required|string|max:150';
            $rules['tax_id'] = 'required|string|max:60';
            $rules['first_name'] = 'required|string|max:80'; // Nombre de contacto
            $rules['last_name'] = 'nullable|string|max:80';
        } else {
            $rules['first_name'] = 'required|string|max:80';
            $rules['last_name'] = 'required|string|max:80';
            $rules['id_number'] = 'nullable|string|max:40'; // DNI / Pasaporte
        }

        $validated = $request->validate($rules);

        try {
            $sessionId = $this->resolveSessionId($request);
            $cart = $this->cartService->getOrCreateCart($sessionId, auth()->id());

            $result = $this->checkoutService->processCheckout(
                $cart,
                $validated,
                $validated['idempotency_key'] ?? null
            );

            // Guardar dirección en la libreta de favoritas del cliente autenticado
            if (auth()->check()) {
                $customer = ShopCustomer::firstOrCreate(
                    ['user_id' => auth()->id()],
                    [
                        'customer_type' => $customerType,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'] ?? null,
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'company_name' => $validated['business_name'] ?? null,
                        'business_name' => $validated['business_name'] ?? null,
                        'tax_id' => $validated['tax_id'] ?? null,
                    ]
                );

                if ($request->boolean('save_address', true)) {
                    $recipient = !empty($validated['recipient_name'])
                        ? trim($validated['recipient_name'])
                        : trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));
                    $recPhone = !empty($validated['recipient_phone'])
                        ? trim($validated['recipient_phone'])
                        : trim($validated['phone']);

                    ShopCustomerAddress::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'province' => $validated['province'],
                            'municipality' => $validated['municipality'],
                            'address_line1' => trim($validated['address']),
                        ],
                        [
                            'address_type' => 'shipping',
                            'recipient_name' => $recipient,
                            'phone' => $recPhone,
                            'postal_code' => $validated['postal_code'] ?? null,
                            'is_default' => $request->boolean('is_default_address', false) || $customer->addresses()->count() === 0,
                        ]
                    );

                    $customer->update([
                        'default_province' => $validated['province'],
                        'default_municipality' => $validated['municipality'],
                    ]);
                }
            }

            if (!empty($result['checkout_url'])) {
                return redirect($result['checkout_url']);
            }

            return redirect()->route('shop.checkout.success', $result['order']->order_number)
                ->with('success', "Pedido #{$result['order']->order_number} registrado correctamente.");
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Pantalla de confirmación de pedido con snapshot inmutable.
     */
    public function success(string $orderNumber): View
    {
        $order = ShopOrder::with(['items.product', 'shippingRate', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.checkout.success', compact('order'));
    }
}
