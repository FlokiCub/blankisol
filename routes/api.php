<?php

declare(strict_types=1);

use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SCGI Torre de Control & Webhooks
|--------------------------------------------------------------------------
*/

// Public Tracking API
Route::get('/tracking/{pin}', [PublicTrackingController::class, 'apiLookup'])
    ->name('api.tracking.lookup')
    ->middleware('throttle:60,1');

// WhatsApp Meta Cloud API Webhook Endpoints
Route::prefix('webhooks/whatsapp')->name('webhooks.whatsapp.')->group(function () {
    Route::get('/', [WhatsAppWebhookController::class, 'verify'])->name('verify');
    Route::post('/', [WhatsAppWebhookController::class, 'handle'])->name('handle')->middleware('throttle:120,1');
});
