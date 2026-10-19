<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\Equipment;
use App\Models\WhatsAppNotification;
use App\Services\WhatsApp\Drivers\LogWhatsAppDriver;
use App\Services\WhatsApp\Drivers\MetaCloudWhatsAppDriver;
use App\Services\WhatsAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class Sprint24WhatsAppGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected WhatsAppNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WhatsAppNotificationService::class);
    }

    /**
     * Test: Sanitización de teléfonos para Cuba y formato internacional E.164.
     */
    public function test_phone_sanitization_formats_cuban_numbers_properly(): void
    {
        $driver = new MetaCloudWhatsAppDriver([
            'phone_number_id' => '123456789',
            'access_token' => 'mock_token',
        ]);

        // Formato nacional móvil Cuba (8 dígitos empezando en 5) -> debe agregar 53
        $this->assertEquals('5352345678', $driver->sanitizePhoneNumber('52345678'));
        $this->assertEquals('5352345678', $driver->sanitizePhoneNumber('+53 5 234-5678'));
        $this->assertEquals('5352345678', $driver->sanitizePhoneNumber('  +53-52345678  '));
        $this->assertEquals('13055551234', $driver->sanitizePhoneNumber('+1 (305) 555-1234'));
    }

    /**
     * Test: Envío exitoso a través de Meta Cloud API con simulación HTTP.
     */
    public function test_meta_cloud_driver_sends_message_successfully(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '5352345678', 'wa_id' => '5352345678']],
                'messages' => [['id' => 'wamid.HBgLMjAyNjEwMDIxNDAwMDAVAgARGBI5OEUwRUQyRkI2MzAxNEM5AA==']],
            ], 200),
        ]);

        $metaDriver = new MetaCloudWhatsAppDriver([
            'phone_number_id' => 'PHONE_100293',
            'access_token' => 'EAAG_MOCK_TOKEN_2026',
            'api_version' => 'v20.0',
            'base_url' => 'https://graph.facebook.com',
        ]);

        $result = $metaDriver->sendMessage(
            recipientPhone: '+53 5 234 5678',
            messageText: 'Hola, tu equipo ha arribado a Cuba',
            templateName: 'scgi_port_arrival',
            templateParams: ['Carlos Gómez', 'Yadea G5 Pro', 'BK-78492', 'https://scgi.cu/tracking/BK-78492']
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('wamid.HBgLMjAyNjEwMDIxNDAwMDAVAgARGBI5OEUwRUQyRkI2MzAxNEM5AA==', $result['message_id']);
        $this->assertEquals('meta', $result['provider']);
        $this->assertNull($result['error']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'PHONE_100293/messages')
                && $request['to'] === '5352345678'
                && $request['type'] === 'template'
                && $request['template']['name'] === 'scgi_port_arrival';
        });
    }

    /**
     * Test: Manejo de errores devueltos por la API de Meta (Token expirado o número no válido).
     */
    public function test_meta_cloud_driver_handles_api_errors_gracefully(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'error' => [
                    'message' => 'Invalid OAuth access token - Cannot parse access token',
                    'type' => 'OAuthException',
                    'code' => 190,
                    'fbtrace_id' => 'Az123MockTrace',
                ],
            ], 401),
        ]);

        $metaDriver = new MetaCloudWhatsAppDriver([
            'phone_number_id' => 'PHONE_100293',
            'access_token' => 'INVALID_TOKEN',
        ]);

        $result = $metaDriver->sendMessage(
            recipientPhone: '+53 5 234 5678',
            messageText: 'Prueba de error'
        );

        $this->assertFalse($result['success']);
        $this->assertNull($result['message_id']);
        $this->assertStringContainsString('Meta API Error [190]', $result['error']);
    }

    /**
     * Test: WhatsAppNotificationService registra la notificación y sus metadatos en la base de datos.
     */
    public function test_service_records_notification_with_telemetry_in_database(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.TEST_DB_LOG_001']],
            ], 200),
        ]);

        config(['services.whatsapp.provider' => 'meta']);

        $equipment = Equipment::factory()->readyForDispatch()->create([
            'tracking_pin' => 'BK-WA-TEST',
            'client_data' => [
                'name' => 'María Elena',
                'phone' => '+53 5 444 5555',
            ],
        ]);

        $notification = $this->service->sendStatusUpdateNotification($equipment);

        $this->assertEquals('sent', $notification->status);
        $this->assertEquals('meta', $notification->provider);
        $this->assertEquals('scgi_ready_for_dispatch', $notification->template_name);
        $this->assertEquals('wamid.TEST_DB_LOG_001', $notification->gateway_message_id);

        $this->assertDatabaseHas('whatsapp_notifications', [
            'id' => $notification->id,
            'equipment_id' => $equipment->id,
            'recipient_phone' => '+53 5 444 5555',
            'gateway_message_id' => 'wamid.TEST_DB_LOG_001',
            'provider' => 'meta',
            'status' => 'sent',
        ]);
    }

    /**
     * Test: Despacho asíncrono vía Queue Job.
     */
    public function test_async_job_dispatches_properly_to_queue(): void
    {
        Queue::fake();

        $equipment = Equipment::factory()->create();

        $this->service->dispatchAsyncNotification($equipment, 'Nota adicional de prueba');

        Queue::assertPushed(SendWhatsAppNotificationJob::class, function ($job) use ($equipment) {
            return $job->equipmentId === $equipment->id && $job->customNote === 'Nota adicional de prueba';
        });
    }

    /**
     * Test: Verificación de Webhook Meta Cloud API (GET Challenge).
     */
    public function test_webhook_get_verification_with_valid_token_returns_challenge(): void
    {
        $verifyToken = config('services.whatsapp.webhook_verify_token');
        $challenge = '1158201244';

        $response = $this->getJson("/api/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token={$verifyToken}&hub_challenge={$challenge}");

        $response->assertStatus(200);
        $this->assertEquals($challenge, $response->getContent());
    }

    /**
     * Test: Verificación de Webhook rechaza token inválido con 403.
     */
    public function test_webhook_get_verification_rejects_invalid_token(): void
    {
        $response = $this->getJson('/api/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=WRONG_TOKEN&hub_challenge=12345');

        $response->assertStatus(403);
    }

    /**
     * Test: Webhook POST actualiza estados de entrega (delivered) y lectura (read).
     */
    public function test_webhook_post_updates_delivery_and_read_status(): void
    {
        $equipment = Equipment::factory()->create();

        $notification = WhatsAppNotification::create([
            'equipment_id' => $equipment->id,
            'recipient_phone' => '+53 5 111 2222',
            'recipient_name' => 'Cliente Test',
            'event_trigger' => 'ready_for_dispatch',
            'message_body' => 'Mensaje de prueba',
            'status' => 'sent',
            'gateway_message_id' => 'wamid.HOOK_TEST_123',
            'provider' => 'meta',
            'sent_at' => now()->subMinutes(5),
        ]);

        // 1. Simular callback de entrega (delivered)
        $deliveredPayload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'WHATSAPP_BUSINESS_ACCOUNT_ID',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => ['display_phone_number' => '15550234567'],
                                'statuses' => [
                                    [
                                        'id' => 'wamid.HOOK_TEST_123',
                                        'status' => 'delivered',
                                        'timestamp' => (string) now()->timestamp,
                                        'recipient_id' => '5351112222',
                                    ],
                                ],
                            ],
                            'field' => 'messages',
                        ],
                    ],
                ],
            ],
        ];

        $responseDelivered = $this->postJson('/api/webhooks/whatsapp', $deliveredPayload);
        $responseDelivered->assertStatus(200);

        $notification->refresh();
        $this->assertEquals('delivered', $notification->status);
        $this->assertNotNull($notification->delivered_at);

        // 2. Simular callback de lectura (read)
        $readPayload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'statuses' => [
                                    [
                                        'id' => 'wamid.HOOK_TEST_123',
                                        'status' => 'read',
                                        'timestamp' => (string) now()->timestamp,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseRead = $this->postJson('/api/webhooks/whatsapp', $readPayload);
        $responseRead->assertStatus(200);

        $notification->refresh();
        $this->assertEquals('read', $notification->status);
        $this->assertNotNull($notification->read_at);
    }
}
