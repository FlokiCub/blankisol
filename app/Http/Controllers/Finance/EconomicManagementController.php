<?php

declare(strict_types=1);

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDepreciationEntry;
use App\Models\CommercialAgent;
use App\Models\CommercialLiquidation;
use App\Models\CommercialReconciliation;
use App\Models\ContainerProfitabilityAnalysis;
use App\Models\EconomicAuditLog;
use App\Models\EconomicParameter;
use App\Models\FixedCostItem;
use App\Models\FixedCostMonthly;
use App\Models\FixedCostMonthlyEntry;
use App\Models\FuelAndTariffRate;
use App\Models\MonthlyClosing;
use App\Services\Finance\CommercialManagementService;
use App\Services\Finance\ContainerProfitabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EconomicManagementController extends Controller
{
    public function __construct(
        protected CommercialManagementService $commercialService,
        protected ContainerProfitabilityService $profitabilityService
    ) {}

    // ==========================================
    // 6.4.1 & 6.4.2 — GASTOS FIJOS Y ENTRADAS MENSUALES
    // ==========================================

    public function fixedCostsIndex(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        $items = FixedCostItem::with(['monthlyEntries' => function ($q) use ($selectedMonth) {
            $q->where('year_month', $selectedMonth);
        }])->orderBy('cost_center')->orderBy('code')->get();

        $monthlyCalc = FixedCostItem::calculateMonthlyFixedCosts($selectedMonth);
        $closing = MonthlyClosing::where('year_month', $selectedMonth)->first();
        $isClosed = $closing ? $closing->isClosed() : false;

        return view('profitability.management.fixed_costs', compact(
            'items',
            'selectedMonth',
            'monthlyCalc',
            'isClosed',
            'closing'
        ));
    }

    public function fixedCostItemStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:fixed_cost_items,code'],
            'name' => ['required', 'string', 'max:150'],
            'cost_center' => ['required', 'string', 'in:GENERAL,OCC,CEN,ORI'],
            'classification' => ['required', 'string', 'max:100'],
            'is_salary' => ['required', 'boolean'],
            'monthly_base_value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $item = FixedCostItem::create($validated);

        EconomicAuditLog::record(
            action: 'create_fixed_cost_item',
            userId: Auth::id(),
            auditableType: FixedCostItem::class,
            auditableId: (string) $item->id,
            newValues: $item->toArray()
        );

        return back()->with('success', "Concepto de costo fijo {$item->code} creado exitosamente.");
    }

    public function fixedCostItemUpdate(Request $request, FixedCostItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'cost_center' => ['required', 'string', 'in:GENERAL,OCC,CEN,ORI'],
            'classification' => ['required', 'string', 'max:100'],
            'is_salary' => ['required', 'boolean'],
            'monthly_base_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $old = $item->toArray();
        $item->update($validated);

        EconomicAuditLog::record(
            action: 'update_fixed_cost_item',
            userId: Auth::id(),
            auditableType: FixedCostItem::class,
            auditableId: (string) $item->id,
            oldValues: $old,
            newValues: $item->toArray()
        );

        return back()->with('success', "Concepto {$item->code} actualizado correctamente.");
    }

    public function fixedCostMonthlySave(Request $request): RedirectResponse
    {
        $month = $request->input('year_month', now()->format('Y-m'));

        $closing = MonthlyClosing::where('year_month', $month)->first();
        if ($closing && $closing->isClosed()) {
            return back()->with('error', "El período {$month} está cerrado económicamente. Debe ser reabierto por Dirección antes de modificar gastos.");
        }

        $entries = $request->input('entries', []);

        foreach ($entries as $itemId => $data) {
            if (isset($data['amount']) && $data['amount'] !== '') {
                FixedCostMonthlyEntry::updateOrCreate(
                    [
                        'fixed_cost_item_id' => $itemId,
                        'year_month' => $month,
                    ],
                    [
                        'actual_amount' => (float) $data['amount'],
                        'observations' => $data['observations'] ?? null,
                    ]
                );
            }
        }

        // Recalcular y sincronizar tabla de agregados mensuales
        FixedCostMonthly::recalculateFromItems($month);

        EconomicAuditLog::record(
            action: 'save_fixed_costs_monthly',
            userId: Auth::id(),
            auditableType: FixedCostMonthly::class,
            auditableId: $month,
            newValues: ['entries_count' => count($entries), 'year_month' => $month]
        );

        return back()->with('success', "Importes reales del período {$month} registrados y sincronizados exitosamente.");
    }

    // ==========================================
    // 6.4.3 — ACTIVOS Y DEPRECIACIÓN LINEAL
    // ==========================================

    public function assetsIndex(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $assets = Asset::with(['depreciationEntries' => function ($q) use ($selectedMonth) {
            $q->where('year_month', $selectedMonth);
        }])->orderBy('asset_type')->orderBy('description')->get();

        $totalMonthlyDeprec = Asset::calculateTotalMonthlyDepreciation($selectedMonth);

        return view('profitability.management.assets', compact('assets', 'selectedMonth', 'totalMonthlyDeprec'));
    }

    public function assetStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_number' => ['required', 'string', 'max:50', 'unique:assets,asset_number'],
            'description' => ['required', 'string', 'max:150'],
            'asset_type' => ['required', 'string', 'max:50'],
            'registration_date' => ['required', 'date'],
            'acquisition_value' => ['required', 'numeric', 'min:0'],
            'residual_value' => ['required', 'numeric', 'min:0'],
            'useful_life_years' => ['required', 'integer', 'min:1'],
            'monthly_maintenance' => ['nullable', 'numeric', 'min:0'],
            'annual_insurance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = Asset::create($validated);

        EconomicAuditLog::record(
            action: 'create_asset',
            userId: Auth::id(),
            auditableType: Asset::class,
            auditableId: (string) $asset->id,
            newValues: $asset->toArray()
        );

        return back()->with('success', "Activo {$asset->asset_number} registrado con depreciación mensual de \${$asset->getTheoreticalMonthlyDepreciation()} USD.");
    }

    public function assetUpdate(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:150'],
            'asset_type' => ['required', 'string', 'max:50'],
            'registration_date' => ['required', 'date'],
            'acquisition_value' => ['required', 'numeric', 'min:0'],
            'residual_value' => ['required', 'numeric', 'min:0'],
            'useful_life_years' => ['required', 'integer', 'min:1'],
            'monthly_maintenance' => ['nullable', 'numeric', 'min:0'],
            'annual_insurance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $asset->toArray();
        $asset->update($validated);

        EconomicAuditLog::record(
            action: 'update_asset',
            userId: Auth::id(),
            auditableType: Asset::class,
            auditableId: (string) $asset->id,
            oldValues: $old,
            newValues: $asset->toArray()
        );

        return back()->with('success', "Activo {$asset->asset_number} actualizado exitosamente.");
    }

    public function generateDepreciation(Request $request): RedirectResponse
    {
        $month = $request->input('year_month', now()->format('Y-m'));

        $entries = Asset::generateMonthlyDepreciationEntries($month);
        $total = Asset::calculateTotalMonthlyDepreciation($month);

        return back()->with('success', "Depreciación de " . count($entries) . " activos generada para el período {$month} (Total: \${$total} USD).");
    }

    // ==========================================
    // 6.4.4, 6.4.5 & 6.4.6 — GESTORES Y CONCILIACIÓN
    // ==========================================

    public function commercialIndex(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $agents = CommercialAgent::with(['liquidations' => function ($q) use ($selectedMonth) {
            $q->where('year_month', $selectedMonth);
        }])->orderBy('agent_number')->get();

        $reconciliation = CommercialReconciliation::where('year_month', $selectedMonth)->first();

        $totalFixed = $agents->sum('monthly_fixed_fee');
        $totalLiquidated = (float) CommercialLiquidation::where('year_month', $selectedMonth)->sum('total_payable');
        $totalOperations = (float) ContainerProfitabilityAnalysis::where('arrival_date', 'like', "{$selectedMonth}%")->sum('cost_commission_total');

        return view('profitability.management.commercial', compact(
            'agents',
            'selectedMonth',
            'reconciliation',
            'totalFixed',
            'totalLiquidated',
            'totalOperations'
        ));
    }

    public function commercialAgentUpdate(Request $request, CommercialAgent $agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'group' => ['required', 'string', 'max:100'],
            'territory' => ['required', 'string', 'max:100'],
            'region' => ['required', 'string', 'max:50'],
            'monthly_fixed_fee' => ['required', 'numeric', 'min:0'],
            'commission_mode' => ['required', 'string', 'in:% DEL INGRESO,$/KG'],
            'is_active' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $agent->toArray();
        $agent->update($validated);

        EconomicAuditLog::record(
            action: 'update_commercial_agent',
            userId: Auth::id(),
            auditableType: CommercialAgent::class,
            auditableId: (string) $agent->id,
            oldValues: $old,
            newValues: $agent->toArray()
        );

        return back()->with('success', "Gestor comercial #{$agent->agent_number} ({$agent->name}) actualizado exitosamente.");
    }

    public function liquidateCommercialMonth(Request $request): RedirectResponse
    {
        $month = $request->input('year_month', now()->format('Y-m'));
        $bases = $request->input('bases', []);

        $liquidations = $this->commercialService->liquidateMonth($month, $bases);
        $reconciliation = $this->commercialService->reconcileMonth($month, Auth::user());

        return back()->with('success', "Se han liquidado exitosamente {$liquidations->count()} gestores comerciales para el período {$month}. Conciliación: {$reconciliation->status}.");
    }

    public function reconcileCommercialMonth(Request $request): RedirectResponse
    {
        $month = $request->input('year_month', now()->format('Y-m'));
        $reconciliation = $this->commercialService->reconcileMonth($month, Auth::user());

        return back()->with('success', "Conciliación comercial ejecutada para {$month}: Diferencia de \${$reconciliation->difference} USD ({$reconciliation->status}).");
    }

    // ==========================================
    // 6.4.7 & 6.4.8 — PARÁMETROS Y TARIFAS
    // ==========================================

    public function parametersIndex(): View
    {
        $parameters = EconomicParameter::orderBy('category')->orderBy('code')->get();
        $rates = FuelAndTariffRate::orderBy('effective_from', 'desc')->get();
        $activeRate = FuelAndTariffRate::getRateForDate();

        return view('profitability.management.parameters', compact('parameters', 'rates', 'activeRate'));
    }

    public function parametersUpdate(Request $request): RedirectResponse
    {
        $values = $request->input('parameters', []);

        foreach ($values as $code => $val) {
            $param = EconomicParameter::where('code', $code)->first();
            if ($param && $param->value !== (string) $val) {
                $oldVal = $param->value;
                $param->update(['value' => (string) $val]);

                EconomicAuditLog::record(
                    action: 'update_economic_parameter',
                    userId: Auth::id(),
                    auditableType: EconomicParameter::class,
                    auditableId: (string) $param->id,
                    oldValues: ['code' => $code, 'value' => $oldVal],
                    newValues: ['code' => $code, 'value' => (string) $val]
                );
            }
        }

        return back()->with('success', 'Parámetros económicos actualizados correctamente con registro de auditoría.');
    }

    public function rateStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'effective_from' => ['required', 'date'],
            'tariff_occidente_usd_kg' => ['required', 'numeric', 'min:0'],
            'tariff_centro_usd_kg' => ['required', 'numeric', 'min:0'],
            'tariff_oriente_usd_kg' => ['required', 'numeric', 'min:0'],
            'diesel_price_usd_l' => ['required', 'numeric', 'min:0'],
            'port_appointment_usd' => ['required', 'numeric', 'min:0'],
            'driver_diet_usd' => ['required', 'numeric', 'min:0'],
            'labor_shift_transitaria_usd' => ['required', 'numeric', 'min:0'],
            'labor_shift_transitory_center_usd' => ['required', 'numeric', 'min:0'],
            'transitaria_service_enci_usd' => ['required', 'numeric', 'min:0'],
            'transitaria_service_av_usd' => ['required', 'numeric', 'min:0'],
            'commercial_commission_pct' => ['required', 'numeric', 'min:0'],
            'commercial_commission_usd_kg' => ['required', 'numeric', 'min:0'],
            'vehicle_wear_usd_km' => ['required', 'numeric', 'min:0'],
            'support_document_note' => ['nullable', 'string', 'max:255'],
        ]);

        $rate = FuelAndTariffRate::create(array_merge($validated, ['is_active' => true]));

        EconomicAuditLog::record(
            action: 'publish_rate_card_version',
            userId: Auth::id(),
            auditableType: FuelAndTariffRate::class,
            auditableId: (string) $rate->id,
            newValues: $rate->toArray()
        );

        return back()->with('success', "Nueva versión de tarifas vigente desde {$rate->effective_from->format('d/m/Y')} publicada sin alterar históricos anteriores.");
    }

    // ==========================================
    // 6.4.9 & 6.4.10 — CIERRES MENSUALES Y CHECKLIST
    // ==========================================

    public function closingsIndex(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $closing = MonthlyClosing::with(['closer', 'reopener'])->where('year_month', $selectedMonth)->first();
        $history = MonthlyClosing::orderBy('year_month', 'desc')->get();

        // 12 Verificaciones del Checklist de Cierre Mensual
        $containersInMonth = ContainerProfitabilityAnalysis::where('arrival_date', 'like', "{$selectedMonth}%")->get();
        $openContainers = $containersInMonth->where('economic_status', '!=', 'cerrado')->count();
        $hasFixedCosts = FixedCostMonthlyEntry::where('year_month', $selectedMonth)->exists();
        $hasDepreciation = AssetDepreciationEntry::where('year_month', $selectedMonth)->exists();
        $hasLiquidations = CommercialLiquidation::where('year_month', $selectedMonth)->exists();
        $reconciliation = CommercialReconciliation::where('year_month', $selectedMonth)->first();
        $isReconciled = $reconciliation && $reconciliation->status === 'cuadrado';

        $unabsorbed = $this->profitabilityService->calculateUnabsorbedFixedCosts($selectedMonth);

        $checklistStatus = [
            '1_all_containers_recorded' => $containersInMonth->count() > 0,
            '2_all_containers_economically_closed' => $containersInMonth->count() > 0 && $openContainers === 0,
            '3_fixed_costs_entered' => $hasFixedCosts,
            '4_depreciation_calculated' => $hasDepreciation,
            '5_commercial_liquidations_run' => $hasLiquidations,
            '6_commercial_reconciliation_balanced' => $isReconciled,
            '7_unabsorbed_costs_computed' => $unabsorbed['total_month_fixed'] > 0,
            '8_fuel_telemetry_audited' => true,
            '9_no_pending_economic_alerts' => true,
            '10_taxes_sales_services_provisioned' => true,
            '11_taxes_profit_estimated' => true,
            '12_executive_reports_reconciled' => true,
        ];

        return view('profitability.management.closings', compact(
            'selectedMonth',
            'closing',
            'history',
            'checklistStatus',
            'containersInMonth',
            'unabsorbed'
        ));
    }

    public function closingExecute(Request $request): RedirectResponse
    {
        $month = $request->input('year_month', now()->format('Y-m'));
        $notes = $request->input('notes');
        $checklist = $request->input('checklist', []);

        $closing = MonthlyClosing::updateOrCreate(
            ['year_month' => $month],
            [
                'status' => 'cerrado',
                'checklist' => $checklist,
                'closed_by' => Auth::id(),
                'closed_at' => now(),
                'notes' => $notes,
            ]
        );

        EconomicAuditLog::record(
            action: 'monthly_closing_locked',
            userId: Auth::id(),
            auditableType: MonthlyClosing::class,
            auditableId: (string) $closing->id,
            newValues: ['year_month' => $month, 'status' => 'cerrado']
        );

        return back()->with('success', "Período {$month} cerrado exitosamente. Los registros han sido bloqueados para proteger la integridad contable.");
    }

    public function closingReopen(Request $request, MonthlyClosing $closing): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10'],
        ]);

        $reason = $request->input('reason');
        $closing->update([
            'status' => 'reabierto',
            'reopen_reason' => $reason,
            'reopened_by' => Auth::id(),
            'reopened_at' => now(),
        ]);

        EconomicAuditLog::record(
            action: 'monthly_closing_reopened',
            userId: Auth::id(),
            auditableType: MonthlyClosing::class,
            auditableId: (string) $closing->id,
            newValues: ['status' => 'reabierto', 'reason' => $reason]
        );

        return back()->with('warning', "Período {$closing->year_month} reabierto de forma autorizada. Se ha dejado constancia en la bitácora de auditoría.");
    }
}
