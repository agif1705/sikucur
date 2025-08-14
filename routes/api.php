<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FonnteController;
use App\Http\Controllers\Api\WusapiController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\FingerPrintController;
use App\Models\TvInformasi;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Route::get('/fonnte-webhook', [FonnteController::class, 'handleWebhook']);
// ini kehadiran dari fonnte
Route::post('/kehadiran', [WhatsAppController::class, 'kehadiran']);
Route::post('/link-generate/izin-pegawai', [WhatsAppController::class, 'handleCommand'])->name('izin-pegawai-handleCommand');
Route::post('/report/harian/pimpinan', [WhatsAppController::class, 'scheduleHarian'])->name('scheduleHarian');

Route::post('/wuzapi/webhook', [WusapiController::class, 'webhook'])->name('wuzapi.webhook');

// Route::post('/wdms/webhook', [FingerPrintController::class, 'store'])->name('wdms.webhook');
Route::post('/wdms/webhook', [FingerPrintController::class, 'store'])->name('wdms.webhook.tv');
