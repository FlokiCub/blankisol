<?php

declare(strict_types=1);

namespace App\Services\Shop\Payment;

use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopPayment;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Nombre identificador del proveedor (ej. 'sandbox', 'stripe', 'paypal').
     */
    public function getIdentifier(): string;

    /**
     * Inicializa la transacción de pago para una orden comercial en USD.
     * Retorna URL de redirección, client secret o datos de checkout.
     */
    public function initializePayment(ShopOrder $order): array;

    /**
     * Procesa y valida criptográficamente una notificación asíncrona de webhook.
     */
    public function handleWebhook(Request $request): array;

    /**
     * Consulta el estado de una transacción ante la pasarela externa.
     */
    public function verifyPaymentStatus(string $gatewayReference): array;

    /**
     * Procesa reembolso total o parcial en USD.
     */
    public function refund(ShopPayment $payment, ?float $amount = null, string $reason = ''): array;
}
