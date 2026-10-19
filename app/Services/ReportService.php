<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CashShift;
use App\Models\Equipment;
use App\Models\Payment;
use App\Models\RegionalWarehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Genera el contenido CSV detallado de un turno de caja / arqueo.
     */
    public function generateShiftCsv(CashShift $shift): string
    {
        $shift->loadMissing(['cashRegister.regionalWarehouse', 'cashier', 'payments.equipment']);
        $warehouse = $shift->cashRegister?->regionalWarehouse;
        $cashier = $shift->cashier;

        $output = fopen('php://temp', 'r+');

        // Encabezado del reporte
        fputcsv($output, ['BLANKISOL — ACTA DE ARQUEO Y CIERRE DE CAJA']);
        fputcsv($output, ['ID Turno', $shift->id]);
        fputcsv($output, ['Caja', $shift->cashRegister?->name ?? 'N/A']);
        fputcsv($output, ['Sucursal / Almacen', $warehouse?->name ?? 'N/A']);
        fputcsv($output, ['Cajero Responsable', $cashier?->name ?? 'N/A']);
        fputcsv($output, ['Fecha Apertura', $shift->opened_at?->format('Y-m-d H:i:s') ?? 'N/A']);
        fputcsv($output, ['Fecha Cierre', $shift->closed_at?->format('Y-m-d H:i:s') ?? 'En Curso']);
        fputcsv($output, ['Estado', strtoupper($shift->status)]);
        fputcsv($output, []);

        // Resumen Financiero USD
        fputcsv($output, ['RESUMEN MONEDA USD', 'MONTO ($ USD)']);
        fputcsv($output, ['Fondo Inicial Apertura', number_format((float) $shift->opening_balance_usd, 2)]);
        fputcsv($output, ['Total Recaudado en Turno', number_format((float) $shift->total_collected_usd, 2)]);
        fputcsv($output, ['Saldo Esperado en Sistema', number_format((float) $shift->expected_balance_usd, 2)]);
        fputcsv($output, ['Arqueo Fisico Declarado', number_format((float) $shift->closing_balance_usd, 2)]);
        fputcsv($output, ['Diferencia (Sobrante / Faltante)', number_format((float) $shift->difference_usd, 2)]);
        fputcsv($output, []);

        // Resumen Financiero CUP
        fputcsv($output, ['RESUMEN MONEDA CUP', 'MONTO ($ CUP)']);
        fputcsv($output, ['Fondo Inicial Apertura', number_format((float) $shift->opening_balance_cup, 2)]);
        fputcsv($output, ['Total Recaudado en Turno', number_format((float) $shift->total_collected_cup, 2)]);
        fputcsv($output, ['Saldo Esperado en Sistema', number_format((float) $shift->expected_balance_cup, 2)]);
        fputcsv($output, ['Arqueo Fisico Declarado', number_format((float) $shift->closing_balance_cup, 2)]);
        fputcsv($output, ['Diferencia (Sobrante / Faltante)', number_format((float) $shift->difference_cup, 2)]);
        fputcsv($output, []);

        // Detalle de Transacciones
        fputcsv($output, [
            'No. Recibo',
            'Fecha / Hora',
            'PIN Equipo',
            'Cliente',
            'Metodo Pago',
            'Pagado USD',
            'Pagado CUP',
            'Tasa Cambio',
            'Total Eq. USD',
            'Estado',
        ]);

        foreach ($shift->payments as $p) {
            fputcsv($output, [
                $p->receipt_number,
                $p->paid_at?->format('Y-m-d H:i:s') ?? $p->created_at->format('Y-m-d H:i:s'),
                $p->equipment?->tracking_pin ?? 'N/A',
                $p->client_name ?? 'N/A',
                strtoupper($p->payment_method),
                number_format((float) $p->amount_paid_usd, 2),
                number_format((float) $p->amount_paid_cup, 2),
                number_format((float) $p->exchange_rate_applied, 4),
                number_format((float) $p->equivalent_total_usd, 2),
                strtoupper($p->status),
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Genera el libro contable de pagos en formato CSV con filtros aplicados.
     */
    public function generatePaymentsCsv(array $filters = []): string
    {
        $query = Payment::with(['equipment', 'cashier', 'cashShift.cashRegister.regionalWarehouse'])
            ->latest('paid_at');

        if (!empty($filters['date_from'])) {
            $query->where('paid_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('paid_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (!empty($filters['warehouse_id'])) {
            $warehouseId = $filters['warehouse_id'];
            $query->whereHas('cashShift.cashRegister', fn ($q) => $q->where('regional_warehouse_id', $warehouseId));
        }

        $payments = $query->get();

        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['BLANKISOL — LIBRO CONTABLE DE PAGOS Y RECAUDACION']);
        fputcsv($output, ['Generado', now()->format('Y-m-d H:i:s')]);
        fputcsv($output, ['Filtros', json_encode($filters, JSON_UNESCAPED_UNICODE)]);
        fputcsv($output, []);

        fputcsv($output, [
            'No. Recibo',
            'Fecha',
            'Sucursal / Almacen',
            'Cajero',
            'PIN Equipo',
            'Bulto / Modelo',
            'Cliente',
            'Concepto',
            'Metodo',
            'USD Cobrado',
            'CUP Cobrado',
            'Tasa Cambio',
            'Eq. Total USD',
            'Referencia',
            'Estado',
        ]);

        foreach ($payments as $p) {
            $wh = $p->cashShift?->cashRegister?->regionalWarehouse;
            $eq = $p->equipment;

            fputcsv($output, [
                $p->receipt_number,
                $p->paid_at?->format('Y-m-d H:i:s') ?? $p->created_at->format('Y-m-d H:i:s'),
                $wh?->name ?? 'N/A',
                $p->cashier?->name ?? 'N/A',
                $eq?->tracking_pin ?? 'N/A',
                $eq ? "{$eq->brand} {$eq->model}" : 'N/A',
                $p->client_name ?? 'N/A',
                $p->concept ?? 'Liquidacion',
                strtoupper($p->payment_method),
                number_format((float) $p->amount_paid_usd, 2),
                number_format((float) $p->amount_paid_cup, 2),
                number_format((float) $p->exchange_rate_applied, 4),
                number_format((float) $p->equivalent_total_usd, 2),
                $p->transaction_reference ?? 'N/A',
                strtoupper($p->status),
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Obtiene métricas ejecutivas de recaudación y estado de cajas para el panel de auditoría.
     */
    public function getFinancialSummary(array $filters = []): array
    {
        $paymentsQuery = Payment::where('status', 'completed');

        if (!empty($filters['date_from'])) {
            $paymentsQuery->where('paid_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $paymentsQuery->where('paid_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $totalUsd = (float) (clone $paymentsQuery)->sum('amount_paid_usd');
        $totalCup = (float) (clone $paymentsQuery)->sum('amount_paid_cup');
        $totalEquivalentUsd = (float) (clone $paymentsQuery)->sum('equivalent_total_usd');
        $totalTransactions = (int) (clone $paymentsQuery)->count();

        $openShiftsCount = CashShift::where('status', 'open')->count();
        $closedShiftsCount = CashShift::where('status', 'closed')->count();
        
        $shiftsWithDiscrepancy = CashShift::where('status', 'closed')
            ->where(function ($q) {
                $q->where('difference_usd', '!=', 0)
                  ->orWhere('difference_cup', '!=', 0);
            })->count();

        return [
            'total_usd' => $totalUsd,
            'total_cup' => $totalCup,
            'total_equivalent_usd' => $totalEquivalentUsd,
            'total_transactions' => $totalTransactions,
            'open_shifts_count' => $openShiftsCount,
            'closed_shifts_count' => $closedShiftsCount,
            'shifts_with_discrepancy' => $shiftsWithDiscrepancy,
        ];
    }
}
