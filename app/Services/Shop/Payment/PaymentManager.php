<?php

declare(strict_types=1);

namespace App\Services\Shop\Payment;

use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopPayment;
use InvalidArgumentException;

class PaymentManager
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways = [];

    protected string $defaultGateway = 'sandbox';

    public function __construct()
    {
        // Registrar Sandbox Driver por defecto
        $this->registerGateway(new SandboxPaymentGateway());
    }

    public function registerGateway(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->getIdentifier()] = $gateway;
    }

    public function getGateway(?string $name = null): PaymentGatewayInterface
    {
        $key = $name ?? $this->defaultGateway;

        if (!isset($this->gateways[$key])) {
            throw new InvalidArgumentException("La pasarela de pago '{$key}' no está registrada en el sistema.");
        }

        return $this->gateways[$key];
    }

    /**
     * Inicia el pago de una orden y registra el intento auditable en shop_payments.
     */
    public function createPaymentTransaction(ShopOrder $order, ?string $gatewayName = null): array
    {
        $gateway = $this->getGateway($gatewayName);
        $initResult = $gateway->initializePayment($order);

        $payment = ShopPayment::create([
            'order_id' => $order->id,
            'gateway' => $gateway->getIdentifier(),
            'transaction_id' => $initResult['gateway_reference'] ?? null,
            'amount' => $order->total,
            'currency' => 'USD',
            'status' => 'pending',
            'idempotency_key' => $order->idempotency_key,
            'payload' => $initResult,
        ]);

        return [
            'payment' => $payment,
            'checkout_url' => $initResult['checkout_url'] ?? null,
            'gateway_reference' => $initResult['gateway_reference'] ?? null,
        ];
    }
}
