<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BLANKISOL SCGI Torre de Control
|--------------------------------------------------------------------------
*/

// Public tracking portal
Route::get('/', [PublicTrackingController::class, 'index'])->name('home');
Route::get('/tracking', [PublicTrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [PublicTrackingController::class, 'search'])->name('tracking.search')->middleware('throttle:30,1');
Route::get('/tracking/{pin}', [PublicTrackingController::class, 'show'])->name('tracking.show')->middleware('throttle:60,1');
Route::get('/api/tracking/{pin}', [PublicTrackingController::class, 'apiLookup'])->name('api.tracking')->middleware('throttle:60,1');

// Contact & Quote Requests
Route::post('/contacto', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,60');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Standard User / Operator Area
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // Área de Despacho Cuba - Escáner de Recepción y Cotejo de Manifiesto (Protegido por RBAC)
    Route::middleware(['role:director,despacho_puerto,aduana'])->prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('/scanner', [\App\Http\Controllers\DispatchScannerController::class, 'index'])->name('scanner');
        Route::post('/scanner', [\App\Http\Controllers\DispatchScannerController::class, 'processScan'])->name('scan.process');
        Route::post('/lookup', [\App\Http\Controllers\DispatchScannerController::class, 'lookupManifest'])->name('lookup');
        Route::post('/discrepancy', [\App\Http\Controllers\DispatchScannerController::class, 'reportDiscrepancy'])->name('discrepancy');
    });

    // Módulo de Cajas Regionales y Recaudación Multimoneda USD/CUP (Protegido por RBAC)
    Route::middleware(['role:director,cajero_regional'])->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CashierController::class, 'index'])->name('index');
        Route::post('/shift/open', [\App\Http\Controllers\CashierController::class, 'openShift'])->name('shift.open');
        Route::post('/shift/{shift}/close', [\App\Http\Controllers\CashierController::class, 'closeShift'])->name('shift.close');
        Route::get('/lookup', [\App\Http\Controllers\CashierController::class, 'lookupEquipment'])->name('lookup');
        Route::post('/payment', [\App\Http\Controllers\CashierController::class, 'storePayment'])->name('payment.store');
        Route::get('/receipt/{payment}', [\App\Http\Controllers\CashierController::class, 'showReceipt'])->name('receipt');
        Route::post('/equipment/{equipment}/deliver', [\App\Http\Controllers\CashierController::class, 'deliver'])->name('deliver');
    });

    // Módulo de Aduana - Semáforos 48h/72h e Inspección (Protegido por RBAC)
    Route::middleware(['role:director,aduana'])->prefix('customs')->name('customs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Customs\CustomsController::class, 'index'])->name('index');
        Route::get('/{equipment}', [\App\Http\Controllers\Customs\CustomsController::class, 'show'])->name('show');
        Route::post('/{equipment}/start-inspection', [\App\Http\Controllers\Customs\CustomsController::class, 'startInspection'])->name('start-inspection');
        Route::post('/{equipment}/clear', [\App\Http\Controllers\Customs\CustomsController::class, 'clear'])->name('clear');
        Route::post('/{equipment}/hold', [\App\Http\Controllers\Customs\CustomsController::class, 'hold'])->name('hold');
    });

    // Módulo de Hojas de Ruta y Logística Regional (Protegido por RBAC)
    Route::middleware(['role:director,logistica_chofer,despacho_puerto'])->prefix('logistics')->name('logistics.')->group(function () {
        Route::get('/routes', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'index'])->name('routes.index');
        Route::get('/routes/create', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'create'])->name('routes.create');
        Route::post('/routes', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'store'])->name('routes.store');
        Route::get('/routes/{route}', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'show'])->name('routes.show');
        Route::post('/routes/{route}/dispatch', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'dispatchConvoy'])->name('routes.dispatch');
        Route::post('/routes/{route}/receive', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'receiveConvoy'])->name('routes.receive');
        Route::post('/routes/{route}/items', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'addItem'])->name('routes.items.add');
        Route::delete('/routes/{route}/items/{equipment}', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'removeItem'])->name('routes.items.remove');
        Route::post('/routes/{route}/scan-receive', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'scanReceive'])->name('routes.scan-receive');
        Route::get('/routes/{route}/viability', [\App\Http\Controllers\Logistics\LogisticsRouteController::class, 'getViability'])->name('routes.viability');
    });

    // Módulo de Factibilidad y Rentabilidad Económica (Protegido por RBAC: Director, Auditor y Jefe Económico)
    Route::middleware(['role:director,auditor,jefe_economico'])->prefix('profitability')->name('profitability.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'index'])->name('index');
        Route::get('/operational-containers', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'operationalContainers'])->name('operational-containers');
        Route::post('/link-operational/{containerId}', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'linkOperationalContainer'])->name('link-operational');
        Route::get('/create', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'store'])->name('store');
        Route::get('/simulator', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'simulator'])->name('simulator');
        Route::post('/simulate-live', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'simulateLive'])->name('simulate-live');
        Route::get('/monthly-summary', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'monthlySummary'])->name('monthly-summary');
        Route::get('/report/pdf', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'exportPdfReport'])->name('report.pdf');
        Route::get('/report/excel', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'exportExcelReport'])->name('report.excel');
        Route::post('/alerts/{alert}/justify', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'justifyAlert'])->name('alerts.justify');
        Route::get('/{analysis}', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'show'])->name('show');
        Route::post('/{analysis}/close', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'closeAnalysis'])->name('close');
        Route::post('/{analysis}/reopen', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'reopenAnalysis'])->name('reopen');
        Route::get('/{analysis}/csv', [\App\Http\Controllers\Finance\ContainerProfitabilityController::class, 'exportCsv'])->name('csv');

        // Fase 6.4 — Gestión Económica y Configuración
        Route::prefix('management')->name('management.')->group(function () {
            // 6.4.1 & 6.4.2 Costos Fijos
            Route::get('/fixed-costs', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'fixedCostsIndex'])->name('fixed-costs');
            Route::post('/fixed-costs/items', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'fixedCostItemStore'])->name('fixed-costs.items.store');
            Route::put('/fixed-costs/items/{item}', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'fixedCostItemUpdate'])->name('fixed-costs.items.update');
            Route::post('/fixed-costs/monthly', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'fixedCostMonthlySave'])->name('fixed-costs.monthly.save');

            // 6.4.3 Activos y Depreciación
            Route::get('/assets', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'assetsIndex'])->name('assets');
            Route::post('/assets', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'assetStore'])->name('assets.store');
            Route::put('/assets/{asset}', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'assetUpdate'])->name('assets.update');
            Route::post('/assets/depreciation', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'generateDepreciation'])->name('assets.depreciation');

            // 6.4.4, 6.4.5, 6.4.6 Gestores Comerciales y Conciliación
            Route::get('/commercial', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'commercialIndex'])->name('commercial');
            Route::put('/commercial/{agent}', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'commercialAgentUpdate'])->name('commercial.update');
            Route::post('/commercial/liquidate', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'liquidateCommercialMonth'])->name('commercial.liquidate');
            Route::post('/commercial/reconcile', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'reconcileCommercialMonth'])->name('commercial.reconcile');

            // 6.4.7 & 6.4.8 Parámetros Económicos y Tarifas
            Route::get('/parameters', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'parametersIndex'])->name('parameters');
            Route::post('/parameters', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'parametersUpdate'])->name('parameters.update');
            Route::post('/rates', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'rateStore'])->name('rates.store');

            // 6.4.9 & 6.4.10 Cierres Mensuales y Checklist
            Route::get('/closings', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'closingsIndex'])->name('closings');
            Route::post('/closings', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'closingExecute'])->name('closings.execute');
            Route::post('/closings/{closing}/reopen', [\App\Http\Controllers\Finance\EconomicManagementController::class, 'closingReopen'])->name('closings.reopen');
        });
    });

    // Módulo de Auditoría Financiera y Reportes (Protegido por RBAC: Director y Auditor)
    Route::middleware(['role:director,auditor'])->prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Audit\AuditController::class, 'index'])->name('index');
        Route::get('/shifts', [\App\Http\Controllers\Audit\AuditController::class, 'shifts'])->name('shifts.index');
        Route::get('/shifts/{shift}', [\App\Http\Controllers\Audit\AuditController::class, 'showShift'])->name('shifts.show');
        Route::get('/shifts/{shift}/print', [\App\Http\Controllers\Audit\AuditController::class, 'printShift'])->name('shifts.print');
        Route::get('/shifts/{shift}/csv', [\App\Http\Controllers\Audit\AuditController::class, 'downloadShiftCsv'])->name('shifts.csv');
        Route::get('/payments', [\App\Http\Controllers\Audit\AuditController::class, 'payments'])->name('payments.index');
        Route::get('/payments/export/csv', [\App\Http\Controllers\Audit\AuditController::class, 'downloadPaymentsCsv'])->name('payments.csv');
        Route::get('/equipment/timeline', [\App\Http\Controllers\Audit\AuditController::class, 'equipmentTimeline'])->name('equipment.timeline');
    });

    // Módulo de Importación Masiva de Manifiestos (Protegido por RBAC: Director, Despacho Puerto, Aduana)
    Route::middleware(['role:director,despacho_puerto,aduana'])->prefix('manifests')->name('manifests.')->group(function () {
        Route::get('/import', [\App\Http\Controllers\Manifest\ManifestImportController::class, 'index'])->name('import.index');
        Route::post('/import/preview', [\App\Http\Controllers\Manifest\ManifestImportController::class, 'preview'])->name('import.preview');
        Route::post('/import/execute', [\App\Http\Controllers\Manifest\ManifestImportController::class, 'execute'])->name('import.execute');
        Route::get('/import/template', [\App\Http\Controllers\Manifest\ManifestImportController::class, 'downloadTemplate'])->name('import.template');
    });
});


// Admin Panel Routes (Exclusivo Dirección General)
Route::middleware(['auth', 'role:director'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User & Role Management (7 Perfiles)
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
});
