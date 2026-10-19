<?php

declare(strict_types=1);

namespace App\Services\WhatsApp\Drivers;

use App\Services\WhatsApp\Contracts\WhatsAppDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MetaCloudWhatsAppDriver implements WhatsAppDriverInterface
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $apiVersion;
    protected string $baseUrl;

    public function __construct(array $config = [])
    {
        $this->phoneNumberId = (string) ($config['phone_number_id'] ?? config('services.whatsapp.meta.phone_number_id', ''));
        $this->accessToken = (string) ($config['access_token'] ?? config('services.whatsapp.meta.access_token', ''));
        $this->apiVersion = (string) ($config['api_version'] ?? config('services.whatsapp.meta.api_version', 'v20.0'));
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? config('services.whatsapp.meta.base_url', 'https://graph.facebook.com')), '/');
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
                'provider' => 'meta',
            ];
        }

        $endpoint = "{$this->baseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
        ];

        if ($templateName) {
            $components = [];
            if (!empty($templateParams)) {
                $parameters = [];
                foreach ($templateParams as $param) {
                    $parameters[] = [
                        'type' => 'text',
                        'text' => (string) $param,
                    ];
                }
                $components[] = [
                    'type' => 'body',
                    'parameters' => $parameters,
                ];
            }

            $langCode = ($templateName === 'hello_world') ? 'en_US' : 'es';
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $templateName,
                'language' => ['code' => $langCode],
                'components' => $components,
            ];
        } else {
            $payload['type'] = 'text';
            $payload['text'] = [
                'preview_url' => true,
                'body' => $messageText,
            ];
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->acceptJson()
                ->post($endpoint, $payload);

            $responseData = $response->json() ?? [];

            if ($response->successful() && isset($responseData['messages'][0]['id'])) {
                $messageId = $responseData['messages'][0]['id'];

                Log::info("✅ [Meta WhatsApp] Mensaje enviado a {$cleanPhone} (ID: {$messageId})");

                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'error' => null,
                    'raw_response' => $responseData,
                    'provider' => 'meta',
                ];
            }

            $errorMessage = $responseData['error']['message'] ?? $response->body() ?? 'Error desconocido de Meta Graph API';
            $errorCode = $responseData['error']['code'] ?? $response->status();

            Log::error("❌ [Meta WhatsApp Error] Code: {$errorCode} — {$errorMessage}", [
                'phone' => $cleanPhone,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => "Meta API Error [{$errorCode}]: {$errorMessage}",
                'raw_response' => $responseData,
                'provider' => 'meta',
            ];
        } catch (Throwable $e) {
            Log::error("❌ [Meta WhatsApp Exception] {$e->getMessage()}", [
                'phone' => $cleanPhone,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Excepción de comunicación: ' . $e->getMessage(),
                'raw_response' => ['exception' => $e->getMessage()],
                'provider' => 'meta',
            ];
        }
    }

    /**
     * Sanitizes phone number to E.164 without leading plus (e.g. 5351234567).
     */
    public function sanitizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        // Si empieza con 5 y tiene 8 dígitos (Cuba móvil nacional 5XXXXXXX), anteponer código país 53
        if (strlen($digits) === 8 && str_starts_with($digits, '5')) {
            $digits = '53' . $digits;
        }

        return $digits;
    }
}
