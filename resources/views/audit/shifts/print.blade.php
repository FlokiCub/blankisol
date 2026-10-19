<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Acta de Arqueo #{{ $shift->id }} - BLANKISOL</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #111827;
      background: #ffffff;
      margin: 0;
      padding: 24px;
      font-size: 12px;
      line-height: 1.4;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #111827;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .header h1 {
      margin: 0;
      font-size: 18px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .header p {
      margin: 2px 0 0 0;
      font-size: 11px;
      color: #4b5563;
    }
    .grid-2 {
      display: flex;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 16px;
    }
    .box {
      flex: 1;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      padding: 10px;
    }
    .box h3 {
      margin: 0 0 6px 0;
      font-size: 12px;
      text-transform: uppercase;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 4px;
    }
    .table-summary {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
    }
    .table-summary td {
      padding: 3px 0;
    }
    .table-summary td.amount {
      text-align: right;
      font-family: monospace;
      font-weight: bold;
    }
    .table-detail {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
    }
    .table-detail th, .table-detail td {
      border: 1px solid #d1d5db;
      padding: 6px 8px;
      text-align: left;
    }
    .table-detail th {
      background: #f3f4f6;
      font-size: 10px;
      text-transform: uppercase;
    }
    .signatures {
      margin-top: 48px;
      display: flex;
      justify-content: space-between;
      gap: 40px;
    }
    .sig-box {
      flex: 1;
      text-align: center;
      border-top: 1px solid #111827;
      padding-top: 8px;
      font-size: 11px;
    }
    @media print {
      body { padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 20px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
      🖨️ Imprimir / Guardar en PDF
    </button>
  </div>

  <div class="header">
    <h1>BLANKISOL — Acta Oficial de Cierre y Arqueo de Caja</h1>
    <p>Sistema de Control y Gestión de Importaciones (SCGI) • Sucursal: {{ $shift->cashRegister?->regionalWarehouse?->name ?? 'N/A' }}</p>
  </div>

  <div class="grid-2">
    <div class="box">
      <h3>Datos del Turno</h3>
      <table class="table-summary">
        <tr><td><strong>ID Turno:</strong></td><td>{{ $shift->id }}</td></tr>
        <tr><td><strong>Caja / Ventanilla:</strong></td><td>{{ $shift->cashRegister?->name }}</td></tr>
        <tr><td><strong>Cajero(a) Responsable:</strong></td><td>{{ $shift->cashier?->name }}</td></tr>
        <tr><td><strong>Apertura:</strong></td><td>{{ $shift->opened_at?->format('Y-m-d H:i:s') }}</td></tr>
        <tr><td><strong>Cierre:</strong></td><td>{{ $shift->closed_at?->format('Y-m-d H:i:s') ?? 'En Curso' }}</td></tr>
      </table>
    </div>

    <div class="box">
      <h3>Resumen de Arqueo Multimoneda</h3>
      <table class="table-summary">
        <tr>
          <td><strong>Recaudación USD:</strong></td>
          <td class="amount">${{ number_format((float) $shift->total_collected_usd, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Diferencia USD:</strong></td>
          <td class="amount" style="{{ (float)$shift->difference_usd != 0 ? 'color: #dc2626;' : '' }}">
            ${{ number_format((float) $shift->difference_usd, 2) }}
          </td>
        </tr>
        <tr>
          <td><strong>Recaudación CUP:</strong></td>
          <td class="amount">${{ number_format((float) $shift->total_collected_cup, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Diferencia CUP:</strong></td>
          <td class="amount" style="{{ (float)$shift->difference_cup != 0 ? 'color: #dc2626;' : '' }}">
            ${{ number_format((float) $shift->difference_cup, 2) }}
          </td>
        </tr>
      </table>
    </div>
  </div>

  <h3 style="margin-top: 20px; font-size: 13px; text-transform: uppercase;">Detalle de Transacciones Registradas ({{ $shift->payments->count() }})</h3>
  <table class="table-detail">
    <thead>
      <tr>
        <th>No. Recibo</th>
        <th>Hora</th>
        <th>PIN / Bulto</th>
        <th>Cliente</th>
        <th>Método</th>
        <th style="text-align: right;">USD</th>
        <th style="text-align: right;">CUP</th>
        <th style="text-align: right;">Eq. Total USD</th>
      </tr>
    </thead>
    <tbody>
      @forelse($shift->payments as $p)
        <tr>
          <td><strong>{{ $p->receipt_number }}</strong></td>
          <td>{{ $p->paid_at ? $p->paid_at->format('H:i') : '' }}</td>
          <td>{{ $p->equipment?->tracking_pin ?? 'N/A' }} ({{ $p->equipment?->brand }})</td>
          <td>{{ $p->client_name }}</td>
          <td>{{ strtoupper($p->payment_method) }}</td>
          <td style="text-align: right; font-family: monospace;">${{ number_format((float) $p->amount_paid_usd, 2) }}</td>
          <td style="text-align: right; font-family: monospace;">${{ number_format((float) $p->amount_paid_cup, 2) }}</td>
          <td style="text-align: right; font-family: monospace; font-weight: bold;">${{ number_format((float) $p->equivalent_total_usd, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #6b7280;">Sin transacciones durante el turno.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="signatures">
    <div class="sig-box">
      <strong>Firma del Cajero(a)</strong><br>
      {{ $shift->cashier?->name }}<br>
      CI: _______________________
    </div>
    <div class="sig-box">
      <strong>Firma del Auditor / Supervisor</strong><br>
      Dirección de Auditoría e Ingresos<br>
      Fecha: {{ date('d/m/Y') }}
    </div>
  </div>

</body>
</html>
