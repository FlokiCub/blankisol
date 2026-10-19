<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAlert;
use App\Models\User;
use App\Services\Finance\ContainerProfitabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint6DashboardAndReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $jefeEconomico;
    protected User $unauthorizedUser;
    protected ContainerProfitabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->director = User::factory()->create(['role' => UserRole::DIRECTOR->value]);
        $this->jefeEconomico = User::factory()->create(['role' => UserRole::JEFE_ECONOMICO->value]);
        $this->unauthorizedUser = User::factory()->create(['role' => UserRole::LOGISTICA_CHOFER->value]);
        $this->service = app(ContainerProfitabilityService::class);
    }

    /**
     * Valida filtros del dashboard principal (Fase 6.5.1 y 6.5.7).
     */
    public function test_dashboard_multi_criteria_filters(): void
    {
        // Contenedor 1: ENCI, 2026-05, Oriente déficit
        $c1 = $this->service->calculateAndSave([
            'container_id' => 'CONT-ENCI-001',
            'arrival_date' => '2026-05-10',
            'transitaria' => 'ENCI',
            'kg_occidente' => 5000,
            'kg_centro' => 2000,
            'kg_oriente' => 1000,
        ], $this->director->id);

        // Contenedor 2: A.V, 2026-06, Solo Occidente
        $c2 = $this->service->calculateAndSave([
            'container_id' => 'CONT-AV-002',
            'arrival_date' => '2026-06-15',
            'transitaria' => 'A.V',
            'kg_occidente' => 8000,
            'kg_centro' => 0,
            'kg_oriente' => 0,
        ], $this->director->id);

        // Sin filtros: ve ambos
        $response = $this->actingAs($this->jefeEconomico)->get(route('profitability.index'));
        $response->assertOk();
        $response->assertSee('CONT-ENCI-001');
        $response->assertSee('CONT-AV-002');

        // Filtro por transitaria A.V
        $responseAv = $this->actingAs($this->jefeEconomico)->get(route('profitability.index', ['transitaria' => 'A.V']));
        $responseAv->assertOk();
        $responseAv->assertSee('CONT-AV-002');
        $responseAv->assertDontSee('CONT-ENCI-001');

        // Filtro por período 2026-05
        $responseMay = $this->actingAs($this->jefeEconomico)->get(route('profitability.index', ['period' => '2026-05']));
        $responseMay->assertOk();
        $responseMay->assertSee('CONT-ENCI-001');
        $responseMay->assertDontSee('CONT-AV-002');
    }

    /**
     * Valida la exportación de informe oficial imprimible / PDF (Fase 6.5.8).
     */
    public function test_export_pdf_report_renders_with_official_letterhead_and_kpis(): void
    {
        $this->service->calculateAndSave([
            'container_id' => 'CONT-TEST-PDF',
            'arrival_date' => '2026-08-01',
            'transitaria' => 'ENCI',
            'kg_occidente' => 4500,
            'kg_centro' => 2500,
            'kg_oriente' => 3000,
        ], $this->director->id);

        // Usuario no autorizado es bloqueado por RBAC (403)
        $this->actingAs($this->unauthorizedUser)
            ->get(route('profitability.report.pdf'))
            ->assertForbidden();

        // Jefe económico accede al informe PDF
        $response = $this->actingAs($this->jefeEconomico)->get(route('profitability.report.pdf'));
        $response->assertOk();
        $response->assertViewIs('profitability.reports.pdf_report');
        $response->assertSee('SCGI BLANKISOL S.R.L.');
        $response->assertSee('Informe Ejecutivo de Factibilidad y Rentabilidad de Contenedores');
        $response->assertSee('CONT-TEST-PDF');
        $response->assertSee('Elaborado Por');
        $response->assertSee('Jefe Económico');
        $response->assertSee('Dirección General');
    }

    /**
     * Valida la exportación Excel (CSV UTF-8 BOM) con resumen y detalle (Fase 6.5.9).
     */
    public function test_export_excel_streamed_report(): void
    {
        $this->service->calculateAndSave([
            'container_id' => 'CONT-TEST-EXCEL',
            'arrival_date' => '2026-08-05',
            'transitaria' => 'ENCI',
            'kg_occidente' => 6000,
            'kg_centro' => 1500,
            'kg_oriente' => 2000,
        ], $this->director->id);

        $response = $this->actingAs($this->jefeEconomico)->get(route('profitability.report.excel'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Capturar contenido streamed
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('SCGI BLANKISOL S.R.L.', $content);
        $this->assertStringContainsString('RESUMEN EJECUTIVO CONSOLIDADO', $content);
        $this->assertStringContainsString('CONT-TEST-EXCEL', $content);
        $this->assertStringContainsString('ID Contenedor', $content);
    }

    /**
     * Valida la generación y justificación de alertas económicas (Fase 6.5.10).
     */
    public function test_alerts_auto_detection_and_justification(): void
    {
        // Crear un contenedor deficitario con sobrecostos y desvío de combustible
        $analysis = $this->service->calculateAndSave([
            'container_id' => 'CONT-DEFICIT-01',
            'arrival_date' => '2026-09-01',
            'transitaria' => 'ENCI',
            'kg_occidente' => 500,
            'kg_centro' => 100,
            'kg_oriente' => 100,
            'other_direct_costs' => 3000, // Genera pérdida neta
            'liters_port' => 150,         // Supera norma de 60L
        ], $this->director->id);

        // Verificar que las alertas fueron creadas automáticamente
        $analysis->refresh();
        $alerts = $analysis->alerts;
        $this->assertNotEmpty($alerts);

        $criticalAlert = $alerts->firstWhere('severity', 'alta');
        $this->assertNotNull($criticalAlert);
        $this->assertFalse($criticalAlert->is_investigated);

        // Visualizar en ficha económica del contenedor
        $responseShow = $this->actingAs($this->jefeEconomico)->get(route('profitability.show', $analysis));
        $responseShow->assertOk();
        $responseShow->assertSee('Alertas Económicas y Semáforo de Riesgo Operativo');
        $responseShow->assertSee('ALTA SEVERIDAD');

        // Justificar formalmente la alerta
        $justifyResponse = $this->actingAs($this->jefeEconomico)->post(route('profitability.alerts.justify', $criticalAlert), [
            'justification' => 'Desviación prevista por entrega prioritaria de insumos humanitarios autorizada por Dirección.',
        ]);

        $justifyResponse->assertRedirect();
        $justifyResponse->assertSessionHas('success');

        $criticalAlert->refresh();
        $this->assertTrue($criticalAlert->is_investigated);
        $this->assertEquals($this->jefeEconomico->id, $criticalAlert->resolved_by);
        $this->assertNotNull($criticalAlert->resolved_at);
        $this->assertStringContainsString('insumos humanitarios', $criticalAlert->justification);
    }
}
