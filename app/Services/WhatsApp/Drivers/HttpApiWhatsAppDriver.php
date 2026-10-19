<?php

declare(strict_types=1);

namespace App\Services\WhatsApp\Drivers;

use App\Services\WhatsApp\Contracts\WhatsAppDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class HttpApiWhatsAppDriver implements WhatsAppDriverInterface
{
    protected string $baseUrl;
    protected string $token;
    protected string $instanceId;
    protected string $provider; // 'ultramsg', 'wati', 'gupshup', 'twilio', 'custom'
    protected array $payloadTemplate;
    protected array $headers;

    public function __construct(array $config = [])
    {
        $this->provider     = (string) ($config['provider'] ?? config('services.whatsapp.http.provider', 'ultramsg'));
        $this->baseUrl      = rtrim((string) ($config['base_url'] ?? config('services.whatsapp.http.base_url', 'https://api.ultramsg.com')), '/');
        $this->instanceId   = (string) ($config['instance_id'] ?? config('services.whatsapp.http.instance_id', ''));
        $this->token        = (string) ($config['token'] ?? config('services.whatsapp.http.token', ''));
        $this->headers      = $config['headers'] ?? config('services.whatsapp.http.headers', []);
        $this->payloadTemplate = $config['payload_template'] ?? config('services.whatsapp.http.payload_template', []);
    }

    public function sendMessage(
        string $recipientPhone,
        string $messageText,
        ?string $templateName = null,
        array $templateParams = []
    ): array {
        $cleanPhone = $this->sanitizePhoneNumber($recipientPhone);

        if (empty($cleanPhone)) {
            return $this->errorResponse("Número de teléfono inválido o vacío: {$recipientPhone}");
        }

        if (empty($this->token) || empty($this->instanceId)) {
            return $this->errorResponse('Faltan credenciales: instance_id y/o token no configurados');
        }

        try {
            $payload = $this->buildPayload($cleanPhone, $messageText, $templateName, $templateParams);
            $endpoint = $this->buildEndpoint();

            $response = Http::withHeaders($this->headers)
                ->timeout(15)
                ->acceptJson()
                ->post($endpoint, $payload);

            $responseData = $response->json() ?? [];

            if ($response->successful() && $this->isSuccessResponse($responseData)) {
                $messageId = $this->extractMessageId($responseData);

                Log::info("✅ [{$this->provider}] Mensaje enviado a {$cleanPhone} (ID: {$messageId})");

                return [
                    'success'       => true,
                    'message_id'    => $messageId,
                    'error'         => null,
                    'raw_response'  => $responseData,
                    'provider'      => $this->provider,
                ];
            }

            $errorMessage = $this->extractError($responseData, $response->body());
            $errorCode    = $responseData['error']['code'] ?? $responseData['code'] ?? $response->status();

            Log::error("❌ [{$this->provider} Error] Code: {$errorCode} — {$errorMessage}", [
                'phone'    => $cleanPhone,
                'response' => $responseData,
            ]);

            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => "{$this->provider} API Error [{$errorCode}]: {$errorMessage}",
                'raw_response'  => $responseData,
                'provider'      => $this->provider,
            ];
        } catch (Throwable $e) {
            Log::error("❌ [{$this->provider} Exception] {$e->getMessage()}", [
                'phone' => $cleanPhone,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => 'Excepción de comunicación: ' . $e->getMessage(),
                'raw_response'  => ['exception' => $e->getMessage()],
                'provider'      => $this->provider,
            ];
        }
    }

    /**
     * Construye el payload según el proveedor
     */
    protected function buildPayload(string $phone, string $text, ?string $templateName, array $params): array
    {
        // Si hay template personalizado en config, úsalo
        if (!empty($this->payloadTemplate)) {
            return $this->renderTemplate($phone, $text, $templateName, $params);
        }

        // Payloads por proveedor conocido
        return match ($this->provider) {
            'ultramsg'   => $this->ultramsgPayload($phone, $text),
            'wati'       => $this->watiPayload($phone, $text, $templateName, $params),
            'gupshup'    => $this->gupshupPayload($phone, $text, $templateName, $params),
            'twilio'     => $this->twilioPayload($phone, $text),
            'custom'     => $this->customPayload($phone, $text, $templateName, $params),
            default      => $this->ultramsgPayload($phone, $text), // default a Ultramsg
        };
    }

    /**
     * Ultramsg: POST /instance{id}/messages/chat
     * Docs: https://docs.ultramsg.com/
     */
    protected function ultramsgPayload(string $phone, string $text): array
    {
        return [
            'token' => $this->token,
            'to'    => $phone,
            'body'  => $text,
            'priority' => 10,
            'referenceId' => 'SCGI-' . uniqid(),
        ];
    }

    /**
     * Wati: POST /api/v1/sendSessionMessage/{phone}
     * Docs: https://wati.io/documentation/
     */
    protected function watiPayload(string $phone, string $text, ?string $templateName, array $params): array
    {
        if ($templateName) {
            return [
                'template_name' => $templateName,
                'broadcast_name' => 'SCGI-Notification',
                'parameters' => array_map(fn($p) => ['name' => 'param', 'value' => (string)$p], $params),
            ];
        }
        return [
            'messageText' => $text,
        ];
    }

    /**
     * Gupshup: POST /wa/api/v1/msg
     * Docs: https://www.gupshup.io/developer/whatsapp
     */
    protected function gupshupPayload(string $phone, string $text, ?string $templateName, array $params): array
    {
        $payload = [
            'channel' => 'whatsapp',
            'source'  => $this->instanceId, // en Gupshup esto es el source number
            'destination' => $phone,
            'message' => json_encode(['type' => 'text', 'text' => $text]),
            'src.name' => 'SCGI',
        ];

        if ($templateName) {
            $payload['message'] = json_encode([
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'es'],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => array_map(fn($p) => ['type' => 'text', 'text' => (string)$p], $params),
                    ]],
                ],
            ]);
        }

        return $payload;
    }

    /**
     * Twilio: POST /2010-04-01/Accounts/{AccountSid}/Messages.json
     * Docs: https://www.twilio.com/docs/whatsapp
     */
    protected function twilioPayload(string $phone, string $text): array
    {
        return [
            'From' => 'whatsapp:' . $this->instanceId, // Twilio usa AccountSid o sender number
            'To'   => 'whatsapp:' . $phone,
            'Body' => $text,
        ];
    }

    /**
     * Custom: usa payload_template del config
     */
    protected function customPayload(string $phone, string $text, ?string $templateName, array $params): array
    {
        return $this->renderTemplate($phone, $text, $templateName, $params);
    }

    protected function renderTemplate(string $phone, string $text, ?string $templateName, array $params): array
    {
        $template = $this->payloadTemplate;
        $replacements = [
            '{phone}'       => $phone,
            '{text}'        => $text,
            '{token}'       => $this->token,
            '{instance_id}' => $this->instanceId,
            '{template_name}' => $templateName ?? '',
        ];

        // Reemplazar placeholders recursivamente
        array_walk_recursive($template, function (&$value) use ($replacements, $params) {
            if (is_string($value)) {
                $value = strtr($value, $replacements);
                // Parámetros indexados: {param_0}, {param_1}, ...
                foreach ($params as $i => $p) {
                    $value = str_replace("{param_$i}", (string)$p, $value);
                }
            }
        });

        return $template;
    }

    protected function buildEndpoint(): string
    {
        return match ($this->provider) {
            'ultramsg'   => "{$this->baseUrl}/instance{$this->instanceId}/messages/chat",
            'wati'       => "{$this->baseUrl}/api/v1/sendSessionMessage/{$this->instanceId}", // Wati usa phone en URL
            'gupshup'    => "{$this->baseUrl}/wa/api/v1/msg",
            'twilio'     => "{$this->baseUrl}/2010-04-01/Accounts/{$this->instanceId}/Messages.json",
            'custom'     => $this->baseUrl,
            default      => "{$this->baseUrl}/instance{$this->instanceId}/messages/chat",
        };
    }

    protected function isSuccessResponse(array $data): bool
    {
        return match ($this->provider) {
            'ultramsg'   => isset($data['sent']) && $data['sent'] === true,
            'wati'       => isset($data['message_id']) || isset($data['id']),
            'gupshup'    => isset($data['message_id']) || (isset($data['status']) && $data['status'] === 'success'),
            'twilio'     => isset($data['sid']),
            'custom'     => !empty($data), // configurable via template
            default      => isset($data['sent']) && $data['sent'] === true,
        };
    }

    protected function extractMessageId(array $data): string
    {
        return match ($this->provider) {
            'ultramsg'   => $data['id'] ?? $data['message_id'] ?? 'ULTRAMSG-' . uniqid(),
            'wati'       => $data['message_id'] ?? $data['id'] ?? 'WATI-' . uniqid(),
            'gupshup'    => $data['message_id'] ?? 'GUPSHUP-' . uniqid(),
            'twilio'     => $data['sid'] ?? 'TWILIO-' . uniqid(),
            'custom'     => $data['message_id'] ?? $data['id'] ?? $data['sid'] ?? 'CUSTOM-' . uniqid(),
            default      => $data['id'] ?? 'MSG-' . uniqid(),
        };
    }

    protected function extractError(array $data, string $body): string
    {
        return $data['error']['message']
            ?? $data['message']
            ?? $data['error']
            ?? $data['error_msg']
            ?? $body
            ?? 'Error desconocido';
    }

    protected function errorResponse(string $message): array
    {
        return [
            'success'       => false,
            'message_id'    => null,
            'error'         => $message,
            'raw_response'  => null,
            'provider'      => $this->provider,
        ];
    }

    /**
     * Normaliza a E.164 sin + (ej: 5351234567)
     * Ultramsg y otros esperan formato internacional sin +
     */
    public function sanitizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        // Cuba: 8 dígitos empezando en 5 → anteponer 53
        if (strlen($digits) === 8 && str_starts_with($digits, '5')) {
            $digits = '53' . $digits;
        }

        // Si ya tiene código país (ej: 535xxxxxxx), dejarlo
        // Si es número local sin código, asumir Cuba (53)
        if (strlen($digits) === 8) {
            $digits = '53' . $digits;
        }

        return $digits;
    }
}