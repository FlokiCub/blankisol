<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopPayment;
use App\Services\Shop\Payment\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentManager $paymentManager
    ) {}

    /**
     * Webhook asíncrono con verificación de firma criptográfica HMAC-SHA256.
     */
    public function webhook(Request $request): JsonResponse
    {
        $gateway = $this->paymentManager->getGateway('sandbox');
        $result = $gateway->handleWebhook($request);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        $orderNumber = $result['order_number'];
        $order = ShopOrder::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => "Orden #{$orderNumber} no encontrada.",
            ], 404);
        }

        // Marcar la orden como pagada de forma inmutable
        $order->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // Actualizar o crear registro de pago
        ShopPayment::updateOrCreate(
            ['order_id' => $order->id, 'transaction_id' => $result['gateway_reference']],
            [
                'gateway' => 'sandbox',
                'amount' => $result['amount'],
                'currency' => $result['currency'],
                'status' => 'paid',
                'payload' => $result['payload'],
                'verified_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Pago liquidado exitosamente en USD para orden #{$order->order_number}.",
            'order_status' => $order->fresh()->status,
        ]);
    }

    /**
     * Simulador de Pasarela Sandbox para Pruebas Comerciales en USD.
     */
    public function sandboxSimulator(Request $request): View|RedirectResponse
    {
        $orderNumber = $request->query('order');
        $gatewayRef = $request->query('ref');

        if (!$orderNumber) {
            return redirect()->route('shop.index');
        }

        $order = ShopOrder::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('shop.payment.simulator', compact('order', 'gatewayRef'));
    }

    /**
     * Procesa la simulación en Sandbox (Aprobar o Rechazar).
     */
    public function sandboxProcess(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => 'required|exists:shop_orders,order_number',
            'action' => 'required|in:approve,decline',
            'gateway_reference' => 'nullable|string',
        ]);

        $order = ShopOrder::where('order_number', $validated['order_number'])->firstOrFail();

        if ($validated['action'] === 'approve') {
            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            ShopPayment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'gateway' => 'sandbox',
                    'transaction_id' => $validated['gateway_reference'] ?? ('SBX-' . uniqid()),
                    'amount' => $order->total,
                    'currency' => 'USD',
                    'status' => 'paid',
                    'payload' => ['simulator_result' => 'approved_by_user'],
                    'verified_at' => now(),
                ]
            );

            return redirect()->route('shop.checkout.success', $order->order_number)
                ->with('success', '¡Pago simulado en Sandbox completado con éxito!');
        }

        return redirect()->route('shop.cart.index')
            ->with('error', 'El pago fue cancelado o rechazado en la simulación.');
    }
}
