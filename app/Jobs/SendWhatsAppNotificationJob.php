<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Equipment;
use App\Services\WhatsAppNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número máximo de intentos permitidos.
     */
    public int $tries = 3;

    /**
     * Tiempo de espera antes de reintentar (en segundos).
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 180];

    public function __construct(
        public string $equipmentId,
        public ?string $customNote = null
    ) {}

    public function handle(WhatsAppNotificationService $service): void
    {
        $equipment = Equipment::find($this->equipmentId);

        if (!$equipment) {
            Log::warning("WhatsApp Notification Job cancelado: Equipo ID {$this->equipmentId} no encontrado.");
            return;
        }

        $service->sendStatusUpdateNotification($equipment, $this->customNote);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("❌ WhatsApp Notification Job falló definitivamente para Equipo ID {$this->equipmentId}: {$exception->getMessage()}", [
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
