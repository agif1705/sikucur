<?php

use App\Http\Controllers\AbsensiPdfController;
use App\Http\Controllers\MikrotikRemoteOntController;
use App\Http\Controllers\Surat\PdfSuratPeringatanController;
use App\Http\Controllers\Surat\SuratPdfController;
use App\Http\Controllers\Surat\SuratPengantarController;
use App\Http\Controllers\Test\TestAbsensiPdfController;
use App\Livewire\AbsensiPegawai\IzinPegawaiLivewire;
use App\Livewire\Homepage\AgendaPageLivewire;
use App\Livewire\Homepage\HomePageLivewire;
use App\Livewire\Homepage\KegiatanPageLivewire;
use App\Livewire\Homepage\KritikPageLivewire;
use App\Livewire\Tv\InformasiTvLivewire;
use Illuminate\Support\Facades\Route;

Route::get('/info', function () {
    phpinfo();
});
// Route::get('/', HomePageLivewire::class)->name('home');
Route::get('/', function () {
    return redirect('/admin');
});
Route::get('kegiatan', KegiatanPageLivewire::class)->name('kegiatan');
Route::get('kritik', KritikPageLivewire::class)->name('kritik');
Route::get('agenda', AgendaPageLivewire::class)->name('agenda');

Route::get('/tv/{sn}', InformasiTvLivewire::class)->name('tvinformasi');
Route::get('/tv-android/{slug}', function (string $slug) {
    return view('livewire.tv.TvAndroid', ['slug' => $slug]);
})->name('tvinformasi.android');
Route::get('/pdf/absensi/{bulan}/{tahun}', [AbsensiPdfController::class, 'index'])->name('absensipdf');
Route::get('/surat/peringatan/pegawai', [PdfSuratPeringatanController::class, 'index'])->name('Surat.peringatan.pdf');

Route::get('/surat/pengantar/template', [SuratPengantarController::class, 'template'])
    ->name('surat.pengantar.template');
Route::get('/surat/pengantar/{token}', [SuratPengantarController::class, 'form'])
    ->name('surat.pengantar.form')
    ->middleware('signed');
Route::post('/surat/pengantar/{token}', [SuratPengantarController::class, 'submit'])
    ->name('surat.pengantar.submit')
    ->middleware('signed');
Route::get('/surat/pengantar/{token}/download', [SuratPengantarController::class, 'download'])
    ->name('surat.pengantar.download')
    ->middleware('signed');
Route::get('/surat/pengantar/{token}/approve', [SuratPengantarController::class, 'approve'])
    ->name('surat.pengantar.approve')
    ->middleware('signed');
Route::get('/surat/pengantar/{token}/reject', [SuratPengantarController::class, 'reject'])
    ->name('surat.pengantar.reject')
    ->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('/surat/permohonan/{permohonan}/pdf', [SuratPdfController::class, 'preview'])->name('surat.permohonan.pdf');
    Route::get('/surat/permohonan/{permohonan}/download', [SuratPdfController::class, 'download'])->name('surat.permohonan.download');
    Route::get('/surat/pengantar/{pengantar}/pdf', [SuratPengantarController::class, 'preview'])
        ->name('surat.pengantar.pdf');
    Route::get('/mikrotik/remote-ont/{tracking}', MikrotikRemoteOntController::class)
        ->name('mikrotik.remote-ont');
    Route::get('/mikrotik/remote-ont-public/{tracking}', [MikrotikRemoteOntController::class, 'public'])
        ->name('mikrotik.remote-ont-public');
});
Route::get('/izin-pegawai/{link}/{nagari}', IzinPegawaiLivewire::class)
    ->name('izin-pegawai.form')
    ->middleware('signed');
Route::get('/test-pdf', [AbsensiPdfController::class, 'test'])
    ->middleware('auth')
    ->name('test.pdf');

// Test routes untuk debugging PDF (hanya development)
if (app()->environment(['local', 'development'])) {
    Route::get('/debug-pdf/{bulan}/{tahun}/{nagari?}', [TestAbsensiPdfController::class, 'testPdf'])
        ->name('debug.pdf');

    Route::get('/debug-data/{bulan}/{tahun}/{nagari?}', [TestAbsensiPdfController::class, 'testData'])
        ->name('debug.data');
}
