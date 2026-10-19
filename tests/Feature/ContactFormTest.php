<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_contact_quote_request_successfully(): void
    {
        $payload = [
            'nombre' => 'María Fernández',
            'empresa' => 'Importaciones Caribe S.A.',
            'email' => 'maria@caribe.com',
            'telefono' => '+5351234567',
            'modalidad' => 'maritimo',
            'origen' => 'Colón, Panamá',
            'destino' => 'Puerto del Mariel, Cuba',
            'mercancia' => '20 Vehículos eléctricos y repuestos',
            'descripcion' => 'Requerimos cotización para contenedor FCL con trámite aduanal.',
            'aceptado_privacidad' => '1',
        ];

        $response = $this->post(route('contact.store'), $payload);

        $response->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_requests', [
            'nombre' => 'María Fernández',
            'email' => 'maria@caribe.com',
            'modalidad' => 'maritimo',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact.store'), []);

        $response->assertSessionHasErrors(['nombre', 'email', 'modalidad', 'descripcion', 'aceptado_privacidad']);
    }

    public function test_contact_form_accepts_json_requests(): void
    {
        $payload = [
            'nombre' => 'Roberto Gómez',
            'email' => 'roberto@soluciones.com',
            'modalidad' => 'aereo',
            'descripcion' => 'Envío urgente de 3 bultos de piezas solares.',
            'aceptado_privacidad' => '1',
        ];

        $response = $this->postJson(route('contact.store'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('contact_requests', [
            'nombre' => 'Roberto Gómez',
            'modalidad' => 'aereo',
        ]);
    }
}