<?php

declare(strict_types=1);

namespace App\Services\WhatsApp\Drivers;

use App\Services\WhatsApp\Contracts\WhatsAppDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LocalGatewayWhatsAppDriver implements WhatsAppDriverInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? config('services.whatsapp.local.base_url', 'http://127.0.0.1:3333')), '/');
        $this->apiKey = (string) ($config['api_key'] ?? config('services.whatsapp.local.api_key', 'scgi_local_key_2026'));
    }

    public function sendMessage(
        string $recipientPhone,
        string $messageText,
        ?string $templateName = null,
        array $templateParams = []
    ): array {
        $cleanPhone = $this->sanitizePhoneNumber($recipientPhone);

        if (empty($cleanPhone)) {
            return [
                'success' => false,
                'message_id' => null,
                'error' => "Número de teléfono inválido o vacío: {$recipientPhone}",
                'raw_response' => null,
                'provider' => 'local',
            ];
        }

        $endpoint = "{$this->baseUrl}/send";

        $payload = [
            'phone' => $cleanPhone,
            'message' => $messageText,
            'api_key' => $this->apiKey,
        ];

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post($endpoint, $payload);

            $responseData = $response->json() ?? [];

            if ($response->successful() && !empty($responseData['success'])) {
                $messageId = $responseData['message_id'] ?? ('LOCAL-WA-' . uniqid());

                Log::info("✅ [Local Gateway WhatsApp] Mensaje enviado a {$cleanPhone} (ID: {$messageId})");

                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'error' => null,
                    'raw_response' => $responseData,
                    'provider' => 'local',
                ];
            }

            $errorMessage = $responseData['error'] ?? $response->body() ?? 'Error desconocido del Gateway Local';

            Log::error("❌ [Local Gateway Error] {$errorMessage}", [
                'phone' => $cleanPhone,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => "Gateway Local Error: {$errorMessage}",
                'raw_response' => $responseData,
                'provider' => 'local',
            ];
        } catch (Throwable $e) {
            Log::error("❌ [Local Gateway Exception] {$e->getMessage()}", [
                'phone' => $cleanPhone,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'No se pudo conectar con el microservicio WhatsApp local en ' . $this->baseUrl . '. ¿Está iniciado? (' . $e->getMessage() . ')',
                'raw_response' => ['exception' => $e->getMessage()],
                'provider' => 'local',
            ];
        }
    }

    /**
     * Normaliza el número a formato numérico E.164 sin signo más (ej: 5354040481).
     */
    public function sanitizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        // Si empieza con 5 y tiene 8 dígitos (móvil Cuba 5XXXXXXX), anteponer prefijo país 53
        if (strlen($digits) === 8 && str_starts_with($digits, '5')) {
            $digits = '53' . $digits;
        }

        return $digits;
    }
}
