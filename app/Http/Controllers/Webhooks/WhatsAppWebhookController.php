<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verificación del Webhook por parte de Meta Cloud API (GET request).
     */
    public function verify(Request $request): Response|JsonResponse
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedToken = config('services.whatsapp.webhook_verify_token', 'scgi_secure_wa_webhook_token_2026');

        if ($mode === 'subscribe' && !empty($token) && !empty($expectedToken) && hash_equals((string) $expectedToken, (string) $token)) {
            Log::info("✅ Webhook de WhatsApp verificado exitosamente por Meta.");
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning("⛔ Intento no autorizado de verificación de Webhook de WhatsApp.", [
            'mode' => $mode,
            'ip' => $request->ip(),
        ]);

        return response()->json(['error' => 'Forbidden'], 403);
    }

    /**
     * Recepción de eventos en tiempo real desde Meta Cloud API (POST request).
     */
    public function handle(Request $request): JsonResponse
    {
        // Verificación de firma HMAC-SHA256 si app_secret está configurado
        $appSecret = config('services.whatsapp.meta.app_secret');
        if (!empty($appSecret)) {
            $signatureHeader = $request->header('X-Hub-Signature-256');
            if (empty($signatureHeader)) {
                Log::warning("⛔ Webhook WhatsApp rechazado: Falta cabecera X-Hub-Signature-256.");
                return response()->json(['error' => 'Missing signature'], 401);
            }

            $expectedHash = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);
            if (!hash_equals($expectedHash, $signatureHeader)) {
                Log::warning("⛔ Webhook WhatsApp rechazado: Firma HMAC no coincide.");
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $payload = $request->all();

        Log::debug("📥 Webhook de WhatsApp recibido y validado.");

        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                $value = $change['value'] ?? [];

                // 1. Procesar actualizaciones de estado de entrega / lectura
                if (isset($value['statuses']) && is_array($value['statuses'])) {
                    $this->processStatuses($value['statuses']);
                }

                // 2. Procesar mensajes entrantes de clientes
                if (isset($value['messages']) && is_array($value['messages'])) {
                    $this->processIncomingMessages($value['messages'], $value['contacts'] ?? []);
                }
            }
        }

        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }

    /**
     * Procesa los estados de entrega (sent, delivered, read, failed).
     *
     * @param array<int, array<string, mixed>> $statuses
     */
    protected function processStatuses(array $statuses): void
    {
        foreach ($statuses as $statusData) {
            $gatewayId = $statusData['id'] ?? null;
            $newStatus = $statusData['status'] ?? null;
            $timestamp = isset($statusData['timestamp']) ? Carbon::createFromTimestamp((int) $statusData['timestamp']) : now();

            if (!$gatewayId || !$newStatus) {
                continue;
            }

            $notification = WhatsAppNotification::where('gateway_message_id', $gatewayId)->first();

            if (!$notification) {
                Log::warning("⚠️ Webhook status recibido para mensaje desconocido ID: {$gatewayId}");
                continue;
            }

            $updateData = ['status' => $newStatus];

            if ($newStatus === 'delivered') {
                $updateData['delivered_at'] = $timestamp;
            } elseif ($newStatus === 'read') {
                $updateData['read_at'] = $timestamp;
                if (!$notification->delivered_at) {
                    $updateData['delivered_at'] = $timestamp;
                }
            } elseif ($newStatus === 'failed') {
                $errors = $statusData['errors'] ?? [];
                $updateData['error_details'] = json_encode($errors, JSON_UNESCAPED_UNICODE);
            }

            $notification->update($updateData);

            Log::info("🔄 Notificación WhatsApp {$notification->id} actualizada a '{$newStatus}' (Gateway: {$gatewayId})");
        }
    }

    /**
     * Procesa mensajes entrantes enviados por los clientes.
     *
     * @param array<int, array<string, mixed>> $messages
     * @param array<int, array<string, mixed>> $contacts
     */
    protected function processIncomingMessages(array $messages, array $contacts): void
    {
        foreach ($messages as $msg) {
            $fromPhone = $msg['from'] ?? 'Unknown';
            $msgType = $msg['type'] ?? 'text';
            $text = $msg['text']['body'] ?? "[Tipo {$msgType}]";

            Log::info("💬 Mensaje de cliente recibido de {$fromPhone}: {$text}");
        }
    }
}
