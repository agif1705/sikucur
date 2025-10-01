<?php

use App\Models\TvInformasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WusapiController;
use App\Http\Controllers\Api\MikrotikController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\RekapPegawaiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Route::get('/fonnte-webhook', [FonnteController::class, 'handleWebhook']);
// ini kehadiran dari fonnte
Route::post('/kehadiran', [WhatsAppController::class, 'kehadiran']);
Route::post('/handle/command', [WhatsAppController::class, 'handleCommand'])->name('izin-pegawai-handleCommand');
Route::post('/kehadiran/report/harian', [WhatsAppController::class, 'scheduleHarian'])->name('scheduleHarian');
Route::post('/wuzapi/webhook', [WusapiController::class, 'webhook'])->name('wuzapi.webhook');
Route::post('/rekap/fingerprint', [RekapPegawaiController::class, 'webhook'])->name('rekap.webhook.fingerprint');
Route::post('/rekap/absensi/bulanan', [RekapPegawaiController::class, 'absensiBulanan'])->name('rekap.webhook.absensi.bulanan');
Route::post('/hotspot/sikucur/login', [MikrotikController::class, 'index'])
    ->name('mikrotik.hotspot.sikucur.login');
