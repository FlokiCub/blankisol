<?php

declare(strict_types=1);

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\CashShift;
use App\Models\Equipment;
use App\Models\Payment;
use App\Models\RegionalWarehouse;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Panel principal de Auditoría y Control Financiero.
     */
    public function index(Request $request): View
    {
        $filters = [
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        $summary = $this->reportService->getFinancialSummary($filters);

        $recentShifts = CashShift::with(['cashRegister.regionalWarehouse', 'cashier'])
            ->latest('opened_at')
            ->take(8)
            ->get();

        $recentPayments = Payment::with(['equipment', 'cashShift.cashRegister.regionalWarehouse'])
            ->latest('paid_at')
            ->take(10)
            ->get();

        $warehouses = RegionalWarehouse::where('is_active', true)->get();

        return view('audit.index', compact('summary', 'recentShifts', 'recentPayments', 'warehouses', 'filters'));
    }

    /**
     * Listado y arqueos de turnos de caja por sucursal.
     */
    public function shifts(Request $request): View
    {
        $query = CashShift::with(['cashRegister.regionalWarehouse', 'cashier', 'payments'])
            ->latest('opened_at');

        if ($request->filled('warehouse_id')) {
            $warehouseId = $request->query('warehouse_id');
            $query->whereHas('cashRegister', fn ($q) => $q->where('regional_warehouse_id', $warehouseId));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->boolean('discrepancy_only')) {
            $query->where('status', 'closed')
                ->where(function ($q) {
                    $q->where('difference_usd', '!=', 0)
                      ->orWhere('difference_cup', '!=', 0);
                });
        }

        $shifts = $query->paginate(15)->withQueryString();
        $warehouses = RegionalWarehouse::where('is_active', true)->get();

        return view('audit.shifts.index', compact('shifts', 'warehouses'));
    }

    /**
     * Detalle completo de un turno de caja y sus transacciones.
     */
    public function showShift(CashShift $shift): View
    {
        $shift->load(['cashRegister.regionalWarehouse', 'cashier', 'payments.equipment']);

        return view('audit.shifts.show', compact('shift'));
    }

    /**
     * Vista imprimible / Acta oficial de Cierre y Arqueo.
     */
    public function printShift(CashShift $shift): View
    {
        $shift->load(['cashRegister.regionalWarehouse', 'cashier', 'payments.equipment']);

        return view('audit.shifts.print', compact('shift'));
    }

    /**
     * Descarga del reporte CSV de un turno de caja.
     */
    public function downloadShiftCsv(CashShift $shift): Response
    {
        $csvContent = $this->reportService->generateShiftCsv($shift);
        $filename = "acta_arqueo_turno_{$shift->id}_" . date('Ymd_His') . '.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Libro contable de pagos multimoneda con filtros avanzados.
     */
    public function payments(Request $request): View
    {
        $query = Payment::with(['equipment', 'cashier', 'cashShift.cashRegister.regionalWarehouse'])
            ->latest('paid_at');

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'ILIKE', "%{$search}%")
                  ->orWhere('client_name', 'ILIKE', "%{$search}%")
                  ->orWhere('transaction_reference', 'ILIKE', "%{$search}%")
                  ->orWhereHas('equipment', fn ($sub) => $sub->where('tracking_pin', 'ILIKE', "%{$search}%"));
            });
        }

        if ($request->filled('warehouse_id')) {
            $warehouseId = $request->query('warehouse_id');
            $query->whereHas('cashShift.cashRegister', fn ($q) => $q->where('regional_warehouse_id', $warehouseId));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->query('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->where('paid_at', '>=', Carbon::parse($request->query('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('paid_at', '<=', Carbon::parse($request->query('date_to'))->endOfDay());
        }

        $payments = $query->paginate(20)->withQueryString();
        $warehouses = RegionalWarehouse::where('is_active', true)->get();

        return view('audit.payments.index', compact('payments', 'warehouses'));
    }

    /**
     * Descarga del libro contable de pagos en CSV.
     */
    public function downloadPaymentsCsv(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'payment_method', 'warehouse_id']);
        $csvContent = $this->reportService->generatePaymentsCsv($filters);
        $filename = 'libro_pagos_' . date('Ymd_His') . '.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Búsqueda y trazabilidad inmutable de equipos y sus hitos de auditoría.
     */
    public function equipmentTimeline(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $equipment = null;

        if (!empty($search)) {
            $equipment = Equipment::with([
                'statusHistories.changedBy',
                'pvpInspections.technician',
                'payments.cashier',
                'currentWarehouse',
                'dispatchRoutes',
                'whatsAppNotifications',
            ])
            ->where('tracking_pin', strtoupper($search))
            ->orWhere('vin_serial', strtoupper($search))
            ->orWhere('solve_cargo_tracking_id', strtoupper($search))
            ->first();
        }

        return view('audit.equipment.timeline', compact('equipment', 'search'));
    }
}
