<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\WhatsAppNotificationService;
use App\Services\WhatsApp\Drivers\MetaCloudWhatsAppDriver;
use App\Services\WhatsApp\Drivers\LogWhatsAppDriver;
use Illuminate\Console\Command;

class SendTestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test 
                            {phone=5354040481 : Número de teléfono del destinatario (ej. +53 54040481)}
                            {--driver= : Forzar driver específico: meta o log}
                            {--template=hello_world : Nombre de plantilla oficial (ej. hello_world o custom)}
                            {--text= : Texto libre de mensaje (solo válido en ventana de 24h)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un mensaje de prueba a través de la pasarela de WhatsApp configurada';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppNotificationService $service): int
    {
        $phone = (string) $this->argument('phone');
        $forcedDriver = $this->option('driver');
        $template = $this->option('template');
        $text = $this->option('text');

        $activeProvider = $forcedDriver ?? config('services.whatsapp.provider', 'log');

        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("📲 SCGI — Prueba de Gateway WhatsApp Business API");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->line("• Destinatario: <comment>{$phone}</comment>");
        $this->line("• Driver activo: <comment>{$activeProvider}</comment>");

        if ($activeProvider === 'local' || $activeProvider === 'baileys') {
            $localUrl = config('services.whatsapp.local.base_url', 'http://127.0.0.1:3333');
            $this->line("• URL Gateway Local (Baileys/QR): <info>{$localUrl}</info>");
            $this->line("• Panel QR Web: <comment>{$localUrl}/qr</comment>");
            $driver = app(\App\Services\WhatsApp\Drivers\LocalGatewayWhatsAppDriver::class);
        } elseif ($activeProvider === 'meta') {
            $phoneNumberId = config('services.whatsapp.meta.phone_number_id');
            $hasToken = !empty(config('services.whatsapp.meta.access_token'));
            $this->line("• Meta Phone Number ID: " . ($phoneNumberId ? "<info>{$phoneNumberId}</info>" : "<error>NO CONFIGURADO</error>"));
            $this->line("• Meta Access Token: " . ($hasToken ? "<info>CONFIGURADO (✓)</info>" : "<error>NO CONFIGURADO</error>"));

            if (empty($phoneNumberId) || !$hasToken) {
                $this->error("\n❌ ERROR: Faltan credenciales en tu archivo .env:");
                $this->line("   WHATSAPP_PROVIDER=meta");
                $this->line("   WHATSAPP_PHONE_NUMBER_ID=tu_phone_number_id");
                $this->line("   WHATSAPP_ACCESS_TOKEN=tu_access_token_de_meta\n");
                $this->comment("Si prefieres usar el Gateway Local sin bloqueos de Meta:");
                $this->line("   php artisan whatsapp:test {$phone} --driver=local\n");
                return Command::FAILURE;
            }

            $driver = app(MetaCloudWhatsAppDriver::class);
        } else {
            $this->warn("ℹ️  Modo SIMULACIÓN (driver=log): El mensaje se registrará en storage/logs/laravel.log.");
            $this->comment("   Para enviar a un teléfono real mediante WhatsApp oficial, configura WHATSAPP_PROVIDER=local o meta.");
            $driver = app(LogWhatsAppDriver::class);
        }

        $messageContent = $text ?: "¡Hola! Este es un mensaje de prueba del Sistema de Control y Gestión de Importaciones (SCGI).";

        $this->info("\nEnviando mensaje...");

        $templateToUse = $text ? null : $template;

        $result = $driver->sendMessage(
            recipientPhone: $phone,
            messageText: $messageContent,
            templateName: $templateToUse,
            templateParams: []
        );

        if ($result['success']) {
            $this->info("\n✅ ¡ÉXITO! Mensaje despachado correctamente.");
            $this->line("• ID de Mensaje Gateway: <info>" . ($result['message_id'] ?? 'N/A') . "</info>");
            $this->line("• Proveedor: <info>{$result['provider']}</info>");
            return Command::SUCCESS;
        }

        $this->error("\n❌ FALLO EN EL ENVÍO:");
        $this->line("• Error reportado: <error>{$result['error']}</error>");
        if (!empty($result['raw_response'])) {
            $this->line("• Respuesta del Gateway:");
            $this->line(json_encode($result['raw_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return Command::FAILURE;
    }
}
