<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Informe Ejecutivo de Rentabilidad — BLANKISOL S.R.L.</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-light/favicon.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-light/favicon-32x32.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-light/apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    @page {
      size: letter portrait;
      margin: 12mm 15mm 15mm 15mm;
    }

    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #0f172a;
      background: #f8fafc;
      margin: 0;
      padding: 24px;
      font-size: 11px;
      line-height: 1.45;
    }

    .report-sheet {
      max-width: 900px;
      margin: 0 auto;
      background: #ffffff;
      padding: 32px 36px;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    }

    .no-print {
      max-width: 900px;
      margin: 0 auto 20px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0f172a;
      color: #ffffff;
      padding: 14px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 12px;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, #0284c7, #0369a1);
      color: #ffffff;
    }

    .btn-primary:hover {
      background: #0284c7;
    }

    .btn-secondary {
      background: #334155;
      color: #f1f5f9;
    }

    .btn-secondary:hover {
      background: #475569;
    }

    /* Membrete */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 16px;
      margin-bottom: 20px;
    }

    .company-title {
      font-size: 20px;
      font-weight: 900;
      color: #0f172a;
      letter-spacing: -0.5px;
      text-transform: uppercase;
      margin: 0;
    }

    .company-sub {
      font-size: 10px;
      font-weight: 600;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 2px 0 0 0;
    }

    .doc-badge {
      display: inline-block;
      margin-top: 6px;
      background: #f1f5f9;
      color: #0369a1;
      border: 1px solid #cbd5e1;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .meta-box {
      text-align: right;
      font-size: 10px;
      color: #334155;
    }

    .meta-box strong {
      color: #0f172a;
    }

    /* Filtros Aplicados */
    .filter-bar {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      font-size: 10px;
    }

    .filter-item {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .filter-item span.label {
      color: #64748b;
      font-weight: 600;
      text-transform: uppercase;
    }

    .filter-item span.val {
      color: #0f172a;
      font-weight: 700;
      background: #ffffff;
      padding: 2px 6px;
      border-radius: 4px;
      border: 1px solid #cbd5e1;
    }

    /* Grids de KPIs */
    .section-title {
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #0f172a;
      border-left: 3px solid #0284c7;
      padding-left: 8px;
      margin: 20px 0 10px 0;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 18px;
    }

    .kpi-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px 12px;
    }

    .kpi-label {
      font-size: 9px;
      font-weight: 600;
      text-transform: uppercase;
      color: #64748b;
    }

    .kpi-value {
      font-size: 16px;
      font-weight: 800;
      color: #0f172a;
      font-family: 'JetBrains Mono', monospace;
      margin-top: 2px;
    }

    .kpi-sub {
      font-size: 9px;
      color: #64748b;
      margin-top: 2px;
    }

    .highlight-emerald {
      color: #047857 !important;
    }

    .highlight-rose {
      color: #b91c1c !important;
    }

    .highlight-cyan {
      color: #0369a1 !important;
    }

    /* Tablas */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10px;
      margin-bottom: 20px;
    }

    th {
      background: #f1f5f9;
      color: #334155;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 9px;
      letter-spacing: 0.3px;
      padding: 7px 8px;
      border: 1px solid #cbd5e1;
    }

    td {
      padding: 6px 8px;
      border: 1px solid #e2e8f0;
    }

    tr:nth-child(even) td {
      background: #f8fafc;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .font-mono {
      font-family: 'JetBrains Mono', monospace;
    }

    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 8.5px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .badge-factible {
      background: #dcfce7;
      color: #15803d;
      border: 1px solid #86efac;
    }

    .badge-marginal {
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
    }

    .badge-no-factible {
      background: #fee2e2;
      color: #b91c1c;
      border: 1px solid #fca5a5;
    }

    .badge-cerrado {
      background: #f1f5f9;
      color: #334155;
      border: 1px solid #cbd5e1;
    }

    .badge-abierto {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }

    /* Firmas */
    .signatures-block {
      margin-top: 40px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      page-break-inside: avoid;
    }

    .signature-card {
      border-top: 1px solid #475569;
      padding-top: 10px;
      text-align: center;
    }

    .sig-role {
      font-weight: 800;
      text-transform: uppercase;
      font-size: 9.5px;
      color: #0f172a;
    }

    .sig-name {
      font-size: 9px;
      color: #475569;
      margin-top: 2px;
    }

    .sig-date {
      font-size: 8.5px;
      color: #64748b;
      margin-top: 18px;
      border-top: 1px dashed #cbd5e1;
      padding-top: 4px;
    }

    @media print {
      body {
        background: #ffffff;
        padding: 0;
      }
      .report-sheet {
        box-shadow: none;
        border: none;
        padding: 0;
        max-width: 100%;
      }
      .no-print {
        display: none !important;
      }
      .page-break {
        page-break-before: always;
      }
    }
  </style>
</head>
<body>

  <!-- Barra de Acciones (No Imprimible) -->
  <div class="no-print">
    <div style="display: flex; align-items: center; gap: 12px;">
      <i class="fa-solid fa-file-pdf" style="font-size: 22px; color: #38bdf8;"></i>
      <div>
        <div style="font-weight: 800; font-size: 13px;">Informe Oficial de Rentabilidad y Factibilidad Económica</div>
        <div style="font-size: 11px; color: #94a3b8;">SCGI BLANKISOL S.R.L. — Listo para impresión oficial o archivo PDF</div>
      </div>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('profitability.index', request()->query()) }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Volver al Dashboard
      </a>
      <button onclick="window.print()" class="btn btn-primary">
        <i class="fa-solid fa-print"></i> Imprimir / Guardar en PDF
      </button>
    </div>
  </div>

  <div class="report-sheet">
    <!-- Membrete Corporativo Oficial -->
    <div class="header">
      <div style="display: flex; align-items: center; gap: 14px;">
        <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" style="width: 50px; height: 50px; object-fit: contain; border-radius: 10px; border: 1px solid #cbd5e1; padding: 2px;">
        <div>
          <h1 class="company-title">SCGI BLANKISOL S.R.L.</h1>
          <p class="company-sub">Sistema de Control de Gestión e Inteligencia Económica</p>
          <span class="doc-badge">
            <i class="fa-solid fa-scale-balanced" style="margin-right: 4px;"></i>
            Informe Ejecutivo de Factibilidad y Rentabilidad de Contenedores
          </span>
        </div>
      </div>
      <div class="meta-box">
        <div><strong>Fecha Emisión:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        <div><strong>Generado por:</strong> {{ Auth::user()->name }}</div>
        <div><strong>Rol Operativo:</strong> {{ strtoupper(Auth::user()->role_display ?? (is_object(Auth::user()->role) ? Auth::user()->role->value : (string) Auth::user()->role)) }}</div>
        <div><strong>Código Reporte:</strong> RPT-ECON-{{ now()->format('Ymd-His') }}</div>
      </div>
    </div>

    <!-- Parámetros y Filtros del Reporte -->
    <div class="filter-bar">
      <div class="filter-item">
        <span class="label">Período:</span>
        <span class="val">{{ request('period') ?: 'Todos los meses registrados' }}</span>
      </div>
      <div class="filter-item">
        <span class="label">Transitaria:</span>
        <span class="val">{{ request('transitaria') ?: 'Todas (ENCI, A.V, Otras)' }}</span>
      </div>
      <div class="filter-item">
        <span class="label">Región:</span>
        <span class="val">
          @if(request('region') === 'OCC') Occidente @elseif(request('region') === 'CEN') Centro @elseif(request('region') === 'ORI') Oriente @else Todas las Regiones @endif
        </span>
      </div>
      <div class="filter-item">
        <span class="label">Estado Económico:</span>
        <span class="val">{{ request('economic_status') ? ucfirst(str_replace('_', ' ', request('economic_status'))) : 'Todos' }}</span>
      </div>
      <div class="filter-item">
        <span class="label">Veredicto:</span>
        <span class="val">{{ request('verdict') ?: 'Todos los veredictos' }}</span>
      </div>
    </div>

    <!-- Resumen Ejecutivo de 18+ Indicadores -->
    <div class="section-title">1. Indicadores Ejecutivos Consolidados (Servidor)</div>
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-label">Contenedores Evaluados</div>
        <div class="kpi-value">{{ $totalContainers }}</div>
        <div class="kpi-sub">Muestra total analizada</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Carga Total Transportada</div>
        <div class="kpi-value font-mono">{{ number_format($totalKg, 0) }} kg</div>
        <div class="kpi-sub">Media: {{ number_format($avgKgPerContainer, 0) }} kg/cont.</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Ingresos Facturados</div>
        <div class="kpi-value font-mono highlight-cyan">${{ number_format($totalRevenue, 2) }}</div>
        <div class="kpi-sub">Tarifa media: ${{ number_format($avgRevenuePerKg, 2) }}/kg</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Costos Variables Totales</div>
        <div class="kpi-value font-mono">${{ number_format($totalVariableCost, 2) }}</div>
        <div class="kpi-sub">${{ number_format($avgVariableCostPerKg, 2) }}/kg transportado</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-label">Margen de Contribución (MC)</div>
        <div class="kpi-value font-mono highlight-emerald">${{ number_format($totalContributionMargin, 2) }}</div>
        <div class="kpi-sub">MC Ratio: {{ number_format($contributionMarginRatio, 1) }}%</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Costos Fijos Asignados</div>
        <div class="kpi-value font-mono">${{ number_format($totalFixedAssigned, 2) }}</div>
        <div class="kpi-sub">Absorción por volumen</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Utilidad Antes de Impuestos</div>
        <div class="kpi-value font-mono {{ $totalProfitBeforeTax >= 0 ? 'highlight-emerald' : 'highlight-rose' }}">
          ${{ number_format($totalProfitBeforeTax, 2) }}
        </div>
        <div class="kpi-sub">Base imponible general</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Tributos Totales Estimados</div>
        <div class="kpi-value font-mono">${{ number_format($totalTaxes, 2) }}</div>
        <div class="kpi-sub">10% Ventas + 1% Terr + 35% Util</div>
      </div>

      <div class="kpi-card" style="grid-column: span 2; background: #f0fdf4; border-color: #bbf7d0;">
        <div class="kpi-label" style="color: #166534;">Utilidad Neta Total Estimada</div>
        <div class="kpi-value font-mono highlight-emerald" style="font-size: 20px;">${{ number_format($totalNetProfit, 2) }}</div>
        <div class="kpi-sub" style="color: #15803d;">Margen Neto Ponderado: <strong>{{ number_format($avgNetMargin, 1) }}%</strong></div>
      </div>
      <div class="kpi-card" style="grid-column: span 2;">
        <div class="kpi-label">Distribución de Veredictos de Viabilidad</div>
        <div style="display: flex; gap: 8px; margin-top: 6px;">
          <span class="badge badge-factible">🟢 Factibles: {{ $feasibleCount }}</span>
          <span class="badge badge-marginal">🟡 Marginales: {{ $marginalCount }}</span>
          <span class="badge badge-no-factible">🔴 Déficit: {{ $unfeasibleCount }}</span>
        </div>
        <div class="kpi-sub" style="margin-top: 4px;">Estados: {{ $closedCount }} Cerrados, {{ $inReviewCount }} En Revisión, {{ $openCount }} Abiertos</div>
      </div>
    </div>

    <!-- Desglose de Costos por Etapas y Desempeño Regional -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
      <div>
        <div class="section-title">2. Estructura de Costos por Etapas</div>
        <table>
          <thead>
            <tr>
              <th>Etapa Operativa</th>
              <th class="text-right">Monto (USD)</th>
              <th class="text-right">% Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Etapa 1: Puerto Mariel (Flete + Diésel + Dietas)</td>
              <td class="text-right font-mono">${{ number_format($costStructure['puerto'], 2) }}</td>
              <td class="text-right font-mono">{{ $totalCostsSum > 0 ? number_format(($costStructure['puerto'] / $totalCostsSum) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
              <td>Etapa 2: Transitaria y Desagrupe</td>
              <td class="text-right font-mono">${{ number_format($costStructure['transitaria'], 2) }}</td>
              <td class="text-right font-mono">{{ $totalCostsSum > 0 ? number_format(($costStructure['transitaria'] / $totalCostsSum) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
              <td>Etapa 3: Distribución Regional (Traspaso + Entrega)</td>
              <td class="text-right font-mono">${{ number_format($costStructure['distribucion'], 2) }}</td>
              <td class="text-right font-mono">{{ $totalCostsSum > 0 ? number_format(($costStructure['distribucion'] / $totalCostsSum) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
              <td>Etapa 4: Comisiones Comerciales</td>
              <td class="text-right font-mono">${{ number_format($costStructure['comisiones'], 2) }}</td>
              <td class="text-right font-mono">{{ $totalCostsSum > 0 ? number_format(($costStructure['comisiones'] / $totalCostsSum) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
              <td>Costos Fijos Asignados</td>
              <td class="text-right font-mono">${{ number_format($totalFixedAssigned, 2) }}</td>
              <td class="text-right font-mono">{{ $totalCostsSum > 0 ? number_format(($totalFixedAssigned / $totalCostsSum) * 100, 1) : 0 }}%</td>
            </tr>
            <tr style="font-weight: 800; background: #f1f5f9;">
              <td>TOTAL COSTOS OPERATIVOS</td>
              <td class="text-right font-mono">${{ number_format($totalCostsSum, 2) }}</td>
              <td class="text-right font-mono">100.0%</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <div class="section-title">3. Comparativa por Región</div>
        <table>
          <thead>
            <tr>
              <th>Polo Regional</th>
              <th class="text-right">Carga (kg)</th>
              <th class="text-right">Ingreso ($)</th>
              <th class="text-right">Margen Directo ($)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Polo Occidente</strong></td>
              <td class="text-right font-mono">{{ number_format($regionalComparison['occidente']['kg'], 0) }}</td>
              <td class="text-right font-mono">${{ number_format($regionalComparison['occidente']['revenue'], 2) }}</td>
              <td class="text-right font-mono {{ $regionalComparison['occidente']['profit'] >= 0 ? 'highlight-emerald' : 'highlight-rose' }}">
                ${{ number_format($regionalComparison['occidente']['profit'], 2) }}
              </td>
            </tr>
            <tr>
              <td><strong>Polo Centro</strong></td>
              <td class="text-right font-mono">{{ number_format($regionalComparison['centro']['kg'], 0) }}</td>
              <td class="text-right font-mono">${{ number_format($regionalComparison['centro']['revenue'], 2) }}</td>
              <td class="text-right font-mono {{ $regionalComparison['centro']['profit'] >= 0 ? 'highlight-emerald' : 'highlight-rose' }}">
                ${{ number_format($regionalComparison['centro']['profit'], 2) }}
              </td>
            </tr>
            <tr>
              <td><strong>Polo Oriente</strong></td>
              <td class="text-right font-mono">{{ number_format($regionalComparison['oriente']['kg'], 0) }}</td>
              <td class="text-right font-mono">${{ number_format($regionalComparison['oriente']['revenue'], 2) }}</td>
              <td class="text-right font-mono {{ $regionalComparison['oriente']['profit'] >= 0 ? 'highlight-emerald' : 'highlight-rose' }}">
                ${{ number_format($regionalComparison['oriente']['profit'], 2) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detalle de Contenedores Incluidos -->
    <div class="section-title">4. Listado Detallado de Contenedores Analizados ({{ $analyses->count() }})</div>
    <table>
      <thead>
        <tr>
          <th>Contenedor</th>
          <th>Arribo</th>
          <th>Transitaria</th>
          <th class="text-right">Peso (kg)</th>
          <th class="text-right">Ingresos ($)</th>
          <th class="text-right">Costos Var ($)</th>
          <th class="text-right">MC ($)</th>
          <th class="text-right">Fijos ($)</th>
          <th class="text-right">Util. Neta ($)</th>
          <th class="text-center">Margen</th>
          <th class="text-center">Veredicto</th>
          <th class="text-center">Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($analyses as $item)
          <tr>
            <td class="font-mono"><strong>{{ $item->container_id }}</strong></td>
            <td>{{ $item->arrival_date->format('d/m/Y') }}</td>
            <td>{{ $item->transitaria }}</td>
            <td class="text-right font-mono">{{ number_format($item->kg_total, 0) }}</td>
            <td class="text-right font-mono">${{ number_format($item->revenue_total, 2) }}</td>
            <td class="text-right font-mono">${{ number_format($item->cost_variable_total, 2) }}</td>
            <td class="text-right font-mono highlight-emerald">${{ number_format($item->contribution_margin, 2) }}</td>
            <td class="text-right font-mono">${{ number_format($item->fixed_costs_assigned_total, 2) }}</td>
            <td class="text-right font-mono {{ $item->net_profit >= 0 ? 'highlight-emerald' : 'highlight-rose' }}">
              ${{ number_format($item->net_profit, 2) }}
            </td>
            <td class="text-center font-mono">{{ number_format($item->net_margin_pct, 1) }}%</td>
            <td class="text-center">
              <span class="badge {{ $item->decision_verdict === 'FACTIBLE' ? 'badge-factible' : ($item->decision_verdict === 'MARGINAL' ? 'badge-marginal' : 'badge-no-factible') }}">
                {{ $item->decision_verdict }}
              </span>
            </td>
            <td class="text-center">
              <span class="badge {{ $item->economic_status === 'cerrado' ? 'badge-cerrado' : 'badge-abierto' }}">
                {{ $item->economic_status }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="12" class="text-center" style="padding: 16px; color: #64748b;">
              No se encontraron contenedores con los filtros especificados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Bloque de Firmas y Aprobaciones Ejecutivas -->
    <div class="signatures-block">
      <div class="signature-card">
        <div class="sig-role">Elaborado Por</div>
        <div class="sig-name">{{ Auth::user()->name }}</div>
        <div style="font-size: 8px; color: #64748b;">Analista Económico / Especialista</div>
        <div class="sig-date">Firma: ________________________<br>Fecha: {{ now()->format('d/m/Y') }}</div>
      </div>

      <div class="signature-card">
        <div class="sig-role">Revisado Por</div>
        <div class="sig-name">Jefe Económico</div>
        <div style="font-size: 8px; color: #64748b;">Dpto. Finanzas y Control de Gestión</div>
        <div class="sig-date">Firma: ________________________<br>Fecha: _____ / _____ / _________</div>
      </div>

      <div class="signature-card">
        <div class="sig-role">Aprobado Por</div>
        <div class="sig-name">Dirección General</div>
        <div style="font-size: 8px; color: #64748b;">SCGI BLANKISOL S.R.L.</div>
        <div class="sig-date">Firma: ________________________<br>Fecha: _____ / _____ / _________</div>
      </div>
    </div>
  </div>

</body>
</html>
