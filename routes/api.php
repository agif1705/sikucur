<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WusapiController;
use App\Http\Controllers\Api\TvAndroidController;
use App\Http\Controllers\Api\MikrotikController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\RekapPegawaiController;
use App\Http\Controllers\Api\VoucherController;

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/history-absensi', [AuthController::class, 'historyAbsensi']);
});

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
Route::post('/hotspot/{nagari}/{location}/login', [MikrotikController::class, 'index'])
    ->name('mikrotik.hotspot.sikucur.login');
Route::post('/hotspot/{nagari}/{location}/login/voucher', [VoucherController::class, 'index'])
    ->name('mikrotik.hotspot.sikucur.login.voucher');

Route::prefix('tv/{slug}')->group(function () {
    Route::get('/', [TvAndroidController::class, 'index']);
    Route::get('/absensi-hari-ini', [TvAndroidController::class, 'absensiHariIni']);
    Route::get('/videos', [TvAndroidController::class, 'videos']);
    Route::get('/gallery', [TvAndroidController::class, 'gallery']);
    Route::get('/realtime', [TvAndroidController::class, 'realtimeConfig']);
});
