<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EquipmentStatus;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\Equipment;
use App\Models\WhatsAppNotification;
use App\Services\WhatsApp\Contracts\WhatsAppDriverInterface;
use App\Services\WhatsApp\Drivers\HttpApiWhatsAppDriver;
use App\Services\WhatsApp\Drivers\LocalGatewayWhatsAppDriver;
use App\Services\WhatsApp\Drivers\LogWhatsAppDriver;
use App\Services\WhatsApp\Drivers\MetaCloudWhatsAppDriver;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class WhatsAppNotificationService
{
    protected ?WhatsAppDriverInterface $driver = null;

    public function __construct(?WhatsAppDriverInterface $driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * Resolves the configured WhatsApp driver.
     */
    public function resolveDriver(?string $providerName = null): WhatsAppDriverInterface
    {
        $provider = $providerName ?? config('services.whatsapp.provider', 'log');

        return match ($provider) {
            'meta', 'meta_cloud_api'      => app(MetaCloudWhatsAppDriver::class),
            'local', 'baileys'            => app(LocalGatewayWhatsAppDriver::class),
            'http', 'ultramsg', 'wati', 'gupshup', 'twilio', 'custom' => app(HttpApiWhatsAppDriver::class),
            default                       => app(LogWhatsAppDriver::class),
        };
    }

    /**
     * Gets the active WhatsApp driver (or resolves it from current config).
     */
    public function getDriver(): WhatsAppDriverInterface
    {
        return $this->driver ?? $this->resolveDriver();
    }

    /**
     * Allows setting a custom driver at runtime (useful for tests and dynamic switching).
     */
    public function setDriver(WhatsAppDriverInterface $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Dispatches notification asynchronously via background job queue.
     */
    public function dispatchAsyncNotification(Equipment $equipment, ?string $customNote = null): void
    {
        SendWhatsAppNotificationJob::dispatch($equipment->id, $customNote);
    }

    /**
     * Construye el mensaje dinámico y lo envía al cliente a través del driver configurado.
     */
    public function sendStatusUpdateNotification(Equipment $equipment, ?string $customNote = null): WhatsAppNotification
    {
        $clientPhone = $equipment->client_data['phone'] ?? null;
        $clientName = $equipment->client_data['name'] ?? 'Estimado Cliente';
        $eventTrigger = is_object($equipment->current_status) ? $equipment->current_status->value : (string) $equipment->current_status;

        if (empty($clientPhone)) {
            Log::warning("No se pudo enviar WhatsApp para el equipo {$equipment->tracking_pin}: Teléfono no registrado.");
            return WhatsAppNotification::create([
                'equipment_id' => $equipment->id,
                'recipient_phone' => 'N/A',
                'recipient_name' => $clientName,
                'event_trigger' => $eventTrigger,
                'message_body' => 'Intento fallido: Teléfono no especificado.',
                'status' => 'failed',
                'provider' => config('services.whatsapp.provider', 'log'),
                'error_details' => 'Teléfono del cliente no registrado en ficha.',
            ]);
        }

        // 1. Construir texto enriquecido y metadatos de plantilla HSM
        $messageBody = $this->buildMessageText($equipment, $clientName, $customNote);
        $templateData = $this->resolveHsmTemplate($equipment, $clientName);

        // 2. Despachar al Gateway de WhatsApp vía Driver activo
        $dispatchResult = $this->getDriver()->sendMessage(
            recipientPhone: (string) $clientPhone,
            messageText: $messageBody,
            templateName: $templateData['template_name'],
            templateParams: $templateData['parameters']
        );

        // 3. Registrar en bitácora de auditoría con telemetría completa
        return WhatsAppNotification::create([
            'equipment_id' => $equipment->id,
            'recipient_phone' => (string) $clientPhone,
            'recipient_name' => $clientName,
            'event_trigger' => $eventTrigger,
            'message_body' => $messageBody,
            'status' => $dispatchResult['success'] ? 'sent' : 'failed',
            'provider' => $dispatchResult['provider'] ?? config('services.whatsapp.provider', 'log'),
            'template_name' => $templateData['template_name'],
            'gateway_message_id' => $dispatchResult['message_id'] ?? null,
            'error_details' => $dispatchResult['error'] ?? null,
            'raw_response' => $dispatchResult['raw_response'] ?? null,
            'sent_at' => now(),
        ]);
    }

    /**
     * Resuelve la plantilla HSM oficial de Meta para el estado del equipo.
     *
     * @return array{template_name: string|null, parameters: array<int, string>}
     */
    public function resolveHsmTemplate(Equipment $equipment, string $clientName): array
    {
        $statusCode = is_object($equipment->current_status) ? $equipment->current_status->value : (string) $equipment->current_status;
        $trackingUrl = url("/tracking/{$equipment->tracking_pin}");

        return match ($statusCode) {
            EquipmentStatus::PORT_ARRIVAL->value => [
                'template_name' => 'scgi_port_arrival',
                'parameters' => [
                    $clientName,
                    "{$equipment->brand} {$equipment->model}",
                    $equipment->tracking_pin,
                    $trackingUrl,
                ],
            ],
            EquipmentStatus::READY_FOR_DISPATCH->value => [
                'template_name' => 'scgi_ready_for_dispatch',
                'parameters' => [
                    $clientName,
                    "{$equipment->brand} {$equipment->model}",
                    $equipment->tracking_pin,
                    $trackingUrl,
                ],
            ],
            EquipmentStatus::REGIONAL_TRANSIT->value => [
                'template_name' => 'scgi_regional_transit',
                'parameters' => [
                    $clientName,
                    "{$equipment->brand} {$equipment->model}",
                    $equipment->currentWarehouse?->province ?? 'su provincia de destino',
                    $equipment->tracking_pin,
                    $trackingUrl,
                ],
            ],
            EquipmentStatus::IN_REGIONAL_WAREHOUSE->value => [
                'template_name' => 'scgi_regional_arrival',
                'parameters' => [
                    $clientName,
                    "{$equipment->brand} {$equipment->model}",
                    $equipment->currentWarehouse?->name ?? 'nuestro almacén regional',
                    $equipment->tracking_pin,
                    $trackingUrl,
                ],
            ],
            EquipmentStatus::DELIVERED->value => [
                'template_name' => 'scgi_equipment_delivered',
                'parameters' => [
                    $clientName,
                    "{$equipment->brand} {$equipment->model}",
                    $equipment->tracking_pin,
                ],
            ],
            default => [
                'template_name' => null,
                'parameters' => [],
            ],
        };
    }

    /**
     * Genera la plantilla de mensaje adecuada según el estado del equipo.
     */
    public function buildMessageText(Equipment $equipment, string $clientName, ?string $customNote = null): string
    {
        $trackingUrl = url("/tracking/{$equipment->tracking_pin}");
        $statusLabel = is_object($equipment->current_status) && method_exists($equipment->current_status, 'label') 
            ? $equipment->current_status->label() 
            : ucfirst(str_replace('_', ' ', (string) $equipment->current_status));

        $statusCode = is_object($equipment->current_status) ? $equipment->current_status->value : (string) $equipment->current_status;

        $msg = "🌟 *BLANKISOL — Actualización de tu Envío*\n\n";
        $msg .= "Hola *{$clientName}*,\n";
        $msg .= "Te informamos sobre el estado de tu equipo: *{$equipment->brand} {$equipment->model}*.\n\n";
        $msg .= "📍 *Estado Actual:* {$statusLabel}\n";
        $msg .= "🏷️ *PIN de Rastreo:* `{$equipment->tracking_pin}`\n";

        // Mensajes dinámicos según el hito
        switch ($statusCode) {
            case EquipmentStatus::PORT_ARRIVAL->value:
                $msg .= "🚢 *¡Tu equipo ya arribó a Cuba!* Se encuentra en el área de recepción aduanal del puerto para su inspección.\n";
                if ($equipment->duty_paid_abroad) {
                    $msg .= "✅ *Aranceles:* Pagados en el exterior ($0.00 a pagar en Cuba).\n";
                }
                break;

            case EquipmentStatus::IN_PVP_ASSEMBLY->value:
                $msg .= "🔧 *En Patio PVP:* Nuestros técnicos están realizando la puesta a punto, verificación de batería y pruebas eléctricas de calidad.\n";
                break;

            case EquipmentStatus::READY_FOR_DISPATCH->value:
                $msg .= "📦 *¡Cotejo de Despacho Exitoso!* Tu paquete ha sido verificado con éxito contra el manifiesto en el Puerto del Mariel y se encuentra listo para su salida a destino regional.\n";
                break;

            case EquipmentStatus::REGIONAL_TRANSIT->value:
                $prov = $equipment->currentWarehouse ? $equipment->currentWarehouse->province : 'tu provincia';
                $msg .= "🚛 *En Camino:* Tu equipo va en tránsito logístico hacia {$prov}.\n";
                break;

            case EquipmentStatus::IN_REGIONAL_WAREHOUSE->value:
                $whName = $equipment->currentWarehouse ? $equipment->currentWarehouse->name : 'nuestro almacén provincial';
                $msg .= "🏢 *¡Listo para Retiro!* Tu equipo ha llegado a {$whName}. Ya puedes coordinar su retiro o entrega.\n";
                break;

            case EquipmentStatus::DELIVERED->value:
                $msg .= "🎉 *¡Entrega Completada!* Tu equipo ha sido entregado exitosamente. ¡Gracias por confiar en BLANKISOL!\n";
                break;
        }

        if ($customNote) {
            $msg .= "\n📝 *Nota:* {$customNote}\n";
        }

        $msg .= "\n🔗 *Sigue tu equipo en vivo aquí:*\n{$trackingUrl}\n\n";
        $msg .= "Para consultas directas, responde a este chat.";

        return $msg;
    }
}
