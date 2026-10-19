<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    public function test_non_existent_route_returns_custom_404_page(): void
    {
        $response = $this->get('/ruta-inexistente-aleatoria-xyz');

        $response->assertStatus(404);
        $response->assertSee('Destino No Encontrado');
        $response->assertSee('BLANKISOL');
        $response->assertSee('Volver al Portal Principal');
    }
}