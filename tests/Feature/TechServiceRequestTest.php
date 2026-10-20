<?php

namespace Tests\Feature;

use App\Models\TechServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_tech_services_landing_page(): void
    {
        $response = $this->get(route('tech_services.index'));

        $response->assertOk();
        $response->assertSee('Servicios Tecnológicos Integrales');
        $response->assertSee('Limpieza e higienización');
        $response->assertSee('Sistemas de extracción y cocinas industriales');
        $response->assertSee('Hoteles');
        $response->assertSee('Solicitud de Presupuesto para Servicios Tecnológicos');
    }

    public function test_can_submit_tech_service_quote_request_successfully(): void
    {
        $payload = [
            'nombre' => 'Carlos Gutiérrez',
            'empresa' => 'Cadena Hotelera del Sol',
            'email' => 'carlos.gutierrez@hoteldelsol.cu',
            'telefono' => '+53 52123456',
            'servicio_interes' => 'Limpieza profunda y mantenimiento de sistemas mecánicos de extracción e inyección',
            'tipo_instalacion' => 'Cocina principal buffet',
            'ubicacion' => 'Varadero, Matanzas',
            'superficie_volumen' => '3 campanas y 45 metros de ductos',
            'nivel_complejidad' => 'Media',
            'plazo_deseado' => 'Corto Plazo (1 a 2 semanas)',
            'descripcion' => 'Mantenimiento integral preventivo de campanas extractoras y turbina previo a auditoría sanitaria.',
            'aceptado_privacidad' => '1',
        ];

        $response = $this->post(route('tech_services.quote.store'), $payload);

        $response->assertSessionHas('tech_success');
        $this->assertDatabaseHas('tech_service_requests', [
            'nombre' => 'Carlos Gutiérrez',
            'empresa' => 'Cadena Hotelera del Sol',
            'email' => 'carlos.gutierrez@hoteldelsol.cu',
            'servicio_interes' => 'Limpieza profunda y mantenimiento de sistemas mecánicos de extracción e inyección',
            'nivel_complejidad' => 'Media',
        ]);
    }

    public function test_validates_required_fields_for_tech_service_quote(): void
    {
        $response = $this->post(route('tech_services.quote.store'), []);

        $response->assertSessionHasErrors([
            'nombre',
            'email',
            'telefono',
            'servicio_interes',
            'descripcion',
            'aceptado_privacidad',
        ]);
    }

    public function test_can_submit_quote_request_via_json(): void
    {
        $payload = [
            'nombre' => 'Elena Navarro',
            'empresa' => 'Almacenes Frigoríficos S.A.',
            'email' => 'elena@frigoalmacen.com',
            'telefono' => '+53 53987654',
            'servicio_interes' => 'Mantenimiento de cámaras frías y túneles de congelación',
            'tipo_instalacion' => 'Túnel de refrigeración #2',
            'descripcion' => 'Revisión técnica de evaporadores y desinfección sanitaria periódica.',
            'aceptado_privacidad' => '1',
        ];

        $response = $this->postJson(route('tech_services.quote.store'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('tech_service_requests', [
            'nombre' => 'Elena Navarro',
            'servicio_interes' => 'Mantenimiento de cámaras frías y túneles de congelación',
        ]);
    }

    public function test_honeypot_field_blocks_database_storage_from_bots(): void
    {
        $payload = [
            'nombre' => 'Spam Bot',
            'email' => 'bot@spammer.org',
            'telefono' => '123456789',
            'servicio_interes' => 'Cualquier servicio',
            'descripcion' => 'Viagra buy now',
            'aceptado_privacidad' => '1',
            'empresa_url_check' => 'http://spam-link.com', // Honeypot filled
        ];

        $response = $this->post(route('tech_services.quote.store'), $payload);

        $response->assertSessionHas('tech_success');
        $this->assertDatabaseMissing('tech_service_requests', [
            'email' => 'bot@spammer.org',
        ]);
    }
}
