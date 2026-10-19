<?php

declare(strict_types=1);

namespace App\Services\WhatsApp\Drivers;

use App\Services\WhatsApp\Contracts\WhatsAppDriverInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogWhatsAppDriver implements WhatsAppDriverInterface
{
    public function sendMessage(
        string $recipientPhone,
        string $messageText,
        ?string $templateName = null,
        array $templateParams = []
    ): array {
        $simulatedId = 'WA-LOG-' . strtoupper(Str::random(16));

        Log::info("📱 [WhatsApp Log Driver] Message sent to {$recipientPhone}", [
            'message_id' => $simulatedId,
            'template' => $templateName,
            'params' => $templateParams,
            'body' => $messageText,
        ]);

        return [
            'success' => true,
            'message_id' => $simulatedId,
            'error' => null,
            'raw_response' => [
                'driver' => 'log',
                'recipient' => $recipientPhone,
                'logged_at' => now()->toIso8601String(),
            ],
            'provider' => 'log',
        ];
    }
}
