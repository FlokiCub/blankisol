<?php

declare(strict_types=1);

namespace App\Services\WhatsApp\Contracts;

interface WhatsAppDriverInterface
{
    /**
     * Send a WhatsApp message (text or template) to a given recipient.
     *
     * @param string $recipientPhone Phone number with country code (e.g., +5351234567)
     * @param string $messageText Rendered human-readable message text
     * @param string|null $templateName Optional Meta Cloud HSM Template name
     * @param array<string, mixed> $templateParams Key-value or indexed parameters for HSM template
     * @return array{success: bool, message_id: string|null, error: string|null, raw_response: array<string, mixed>|null, provider: string}
     */
    public function sendMessage(
        string $recipientPhone,
        string $messageText,
        ?string $templateName = null,
        array $templateParams = []
    ): array;
}
