<?php

declare(strict_types=1);

namespace App\Services\Shop\Payment;

use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class SandboxPaymentGateway implements PaymentGatewayInterface
{
    protected string $webhookSecret;

    public function __construct(?string $webhookSecret = null)
    {
        $this->webhookSecret = $webhookSecret ?? config('app.key', 'blankisol_sandbox_secret_key_2026');
    }

    public function getIdentifier(): string
    {
        return 'sandbox';
    }

    public function initializePayment(ShopOrder $order): array
    {
        $gatewayRef = 'SBX-TRX-' . strtoupper(Str::random(12));

        return [
            'success' => true,
            'gateway' => $this->getIdentifier(),
            'gateway_reference' => $gatewayRef,
            'amount' => (float) $order->total,
            'currency' => 'USD',
            'checkout_url' => route('shop.payment.sandbox.simulator', [
                'order' => $order->order_number,
                'ref' => $gatewayRef,
            ]),
        ];
    }

    public function handleWebhook(Request $request): array
    {
        $signature = $request->header('X-BLANKISOL-SHOP-SIGNATURE');
        $payload = $request->getContent();

        if (empty($signature)) {
            return [
                'valid' => false,
                'message' => 'Falta encabezado criptográfico de firma (X-BLANKISOL-SHOP-SIGNATURE).',
            ];
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            return [
                'valid' => false,
                'message' => 'Firma criptográfica inválida. La solicitud no proviene de la pasarela autorizada.',
            ];
        }

        $data = json_decode($payload, true);

        return [
            'valid' => true,
            'event' => $data['event'] ?? 'payment.succeeded',
            'order_number' => $data['order_number'] ?? null,
            'gateway_reference' => $data['gateway_reference'] ?? null,
            'amount' => (float) ($data['amount'] ?? 0.0),
            'currency' => $data['currency'] ?? 'USD',
            'status' => $data['status'] ?? 'paid',
            'payload' => $data,
        ];
    }

    public function verifyPaymentStatus(string $gatewayReference): array
    {
        return [
            'gateway_reference' => $gatewayReference,
            'status' => 'paid',
            'currency' => 'USD',
        ];
    }

    public function refund(ShopPayment $payment, ?float $amount = null, string $reason = ''): array
    {
        $refundAmount = $amount ?? (float) $payment->amount;

        return [
            'success' => true,
            'refund_id' => 'REF-' . strtoupper(Str::random(10)),
            'refund_amount' => $refundAmount,
            'currency' => 'USD',
            'reason' => $reason,
        ];
    }
}
