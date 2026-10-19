<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BusinessType;
use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Enums\UserRole;
use App\Models\CashRegister;
use App\Models\CashShift;
use App\Models\Equipment;
use App\Models\EquipmentStatusHistory;
use App\Models\Payment;
use App\Models\RegionalWarehouse;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint31FinancialReportsAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $auditor;
    protected User $driver;
    protected User $cashier;
    protected RegionalWarehouse $warehouse;
    protected CashRegister $register;
    protected CashShift $shift;
    protected Equipment $equipment;
    protected Payment $payment;
    protected ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportService = app(ReportService::class);

        // 1. Usuarios con roles RBAC
        $this->director = User::factory()->create(['role' => UserRole::DIRECTOR, 'name' => 'Director General']);
        $this->auditor = User::factory()->create(['role' => UserRole::AUDITOR, 'name' => 'Auditor Roberto']);
        $this->driver = User::factory()->create(['role' => UserRole::LOGISTICA_CHOFER, 'name' => 'Chofer Manuel']);

        // 2. Almacén y Caja
        $this->warehouse = RegionalWarehouse::factory()->create([
            'name' => 'Almacén Central La Habana',
            'province' => 'La Habana',
        ]);

        $this->cashier = User::factory()->create([
            'role' => UserRole::CAJERO_REGIONAL,
            'name' => 'Cajera Ana',
            'regional_warehouse_id' => $this->warehouse->id,
        ]);

        $this->register = CashRegister::create([
            'code' => 'CAJA-TEST-01',
            'name' => 'Caja Principal 01',
            'regional_warehouse_id' => $this->warehouse->id,
            'status' => 'closed',
            'is_active' => true,
        ]);

        // 3. Turno de caja cerrado con pagos
        $this->shift = CashShift::create([
            'cash_register_id' => $this->register->id,
            'cashier_user_id' => $this->cashier->id,
            'opened_at' => now()->subHours(8),
            'closed_at' => now()->subHours(1),
            'opening_balance_usd' => 100.00,
            'total_collected_usd' => 250.00,
            'expected_balance_usd' => 350.00,
            'closing_balance_usd' => 350.00,
            'difference_usd' => 0.00,
            'opening_balance_cup' => 5000.00,
            'total_collected_cup' => 35000.00,
            'expected_balance_cup' => 40000.00,
            'closing_balance_cup' => 39500.00,
            'difference_cup' => -500.00, // Faltante de 500 CUP
            'status' => 'closed',
        ]);

        // 4. Equipo y Pago
        $this->equipment = Equipment::factory()->readyForDispatch()->create([
            'tracking_pin' => 'BK-AUDIT-01',
            'vin_serial' => 'VIN-AUDIT-10029',
            'brand' => 'Yadea',
            'model' => 'G5 Pro',
            'duty_amount_usd' => 200.00,
            'shipping_fee_usd' => 50.00,
            'is_fully_paid' => true,
        ]);

        EquipmentStatusHistory::create([
            'equipment_id' => $this->equipment->id,
            'from_status' => EquipmentStatus::PORT_ARRIVAL->value,
            'to_status' => EquipmentStatus::READY_FOR_DISPATCH->value,
            'location' => 'Patio Mariel',
            'notes' => 'Cotejo completado con éxito.',
            'changed_by_user_id' => $this->director->id,
            'recorded_at' => now()->subHours(4),
        ]);

        $this->payment = Payment::create([
            'receipt_number' => 'REC-2026-0001',
            'equipment_id' => $this->equipment->id,
            'cash_shift_id' => $this->shift->id,
            'cashier_user_id' => $this->cashier->id,
            'concept' => 'Arancel y Flete',
            'amount_due_usd' => 250.00,
            'duty_amount_usd' => 200.00,
            'shipping_fee_usd' => 50.00,
            'amount_paid_usd' => 250.00,
            'amount_paid_cup' => 0.00,
            'exchange_rate_applied' => 350.0000,
            'equivalent_total_usd' => 250.00,
            'payment_method' => 'cash_usd',
            'status' => 'completed',
            'client_name' => 'Carlos Alberto Gómez',
            'paid_at' => now()->subHours(3),
        ]);
    }

    /**
     * Test: RBAC — Acceso restringido exclusivamente a roles Director y Auditor.
     */
    public function test_audit_module_rbac_access_control(): void
    {
        // Auditor tiene acceso total
        $responseAuditor = $this->actingAs($this->auditor)->get(route('audit.index'));
        $responseAuditor->assertStatus(200);

        // Director tiene acceso total
        $responseDirector = $this->actingAs($this->director)->get(route('audit.index'));
        $responseDirector->assertStatus(200);

        // Chofer / Logística es bloqueado con 403
        $responseDriver = $this->actingAs($this->driver)->get(route('audit.index'));
        $responseDriver->assertStatus(403);
    }

    /**
     * Test: ReportService genera el CSV de Acta de Arqueo con todos los datos y diferencias.
     */
    public function test_report_service_generates_accurate_shift_csv(): void
    {
        $csv = $this->reportService->generateShiftCsv($this->shift);

        $this->assertStringContainsString('ACTA DE ARQUEO Y CIERRE DE CAJA', $csv);
        $this->assertStringContainsString($this->shift->id, $csv);
        $this->assertStringContainsString('250.00', $csv); // Total USD
        $this->assertStringContainsString('35,000.00', $csv); // Total CUP
        $this->assertStringContainsString('-500.00', $csv); // Diferencia CUP
        $this->assertStringContainsString('REC-2026-0001', $csv); // Recibo
        $this->assertStringContainsString('BK-AUDIT-01', $csv); // PIN
    }

    /**
     * Test: ReportService genera el libro de pagos CSV con filtros de fecha y sucursal.
     */
    public function test_report_service_generates_payments_csv(): void
    {
        $csv = $this->reportService->generatePaymentsCsv([
            'warehouse_id' => $this->warehouse->id,
            'payment_method' => 'cash_usd',
        ]);

        $this->assertStringContainsString('LIBRO CONTABLE DE PAGOS Y RECAUDACION', $csv);
        $this->assertStringContainsString('REC-2026-0001', $csv);
        $this->assertStringContainsString('Carlos Alberto Gómez', $csv);
        $this->assertStringContainsString('250.00', $csv);
    }

    /**
     * Test: Endpoint de descarga de CSV de arqueo de turno.
     */
    public function test_audit_shift_csv_download_endpoint(): void
    {
        $response = $this->actingAs($this->auditor)->get(route('audit.shifts.csv', $this->shift));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('ACTA DE ARQUEO', $response->getContent());
    }

    /**
     * Test: Endpoint de descarga de CSV del libro de pagos.
     */
    public function test_audit_payments_csv_download_endpoint(): void
    {
        $response = $this->actingAs($this->auditor)->get(route('audit.payments.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LIBRO CONTABLE DE PAGOS', $response->getContent());
    }

    /**
     * Test: Vista imprimible de Acta de Arqueo.
     */
    public function test_audit_shift_printable_view(): void
    {
        $response = $this->actingAs($this->auditor)->get(route('audit.shifts.print', $this->shift));

        $response->assertStatus(200);
        $response->assertSee('Acta Oficial de Cierre y Arqueo de Caja');
        $response->assertSee('REC-2026-0001');
    }

    /**
     * Test: Búsqueda y visualización de trazabilidad cronológica inmutable por PIN.
     */
    public function test_audit_equipment_timeline_search(): void
    {
        $response = $this->actingAs($this->auditor)->get(route('audit.equipment.timeline', [
            'q' => 'BK-AUDIT-01',
        ]));

        $response->assertStatus(200);
        $response->assertSee('BK-AUDIT-01');
        $response->assertSee('Yadea G5 Pro');
        $response->assertSee('Cotejo completado con éxito.');
        $response->assertSee('Director General');
    }
}
