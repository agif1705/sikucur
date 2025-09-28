<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tv\InformasiTvLivewire;
use App\Http\Controllers\FonnteController;
use App\Livewire\Homepage\HomePageLivewire;
use App\Livewire\Homepage\AgendaPageLivewire;
use App\Livewire\Homepage\KritikPageLivewire;
use App\Http\Controllers\AbsensiPdfController;
use App\Livewire\TvInformasi\TvNagariLivewire;
use App\Http\Controllers\FingerPrintController;
use App\Livewire\Homepage\KegiatanPageLivewire;
use App\Livewire\Surat\TemplateSuratPeringatan;
use App\Livewire\AbsensiPegawai\IzinPegawaiLivewire;
use App\Http\Controllers\Surat\PdfSuratPeringatanController;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', HomePageLivewire::class)->name('home');
Route::get('/', function () {
    return redirect('/admin');
});
Route::get('kegiatan', KegiatanPageLivewire::class)->name('kegiatan');
Route::get('kritik', KritikPageLivewire::class)->name('kritik');
Route::get('agenda', AgendaPageLivewire::class)->name('agenda');

Route::get('/tv/{sn}', InformasiTvLivewire::class)->name('tvinformasi');
Route::get('/pdf/absensi/{bulan}/{tahun}', [AbsensiPdfController::class, 'index'])->name('absensipdf');
Route::get('/surat/peringatan/pegawai', [PdfSuratPeringatanController::class, 'index'])->name('Surat.peringatan.pdf');
Route::get('/izin-pegawai/{link}/{nagari}', IzinPegawaiLivewire::class)
    ->name('izin-pegawai.form')
    ->middleware('signed');
Route::get('/test-pdf', [App\Http\Controllers\AbsensiPdfController::class, 'test'])
    ->middleware('auth')
    ->name('test.pdf');

// Test routes untuk debugging PDF (hanya development)
if (app()->environment(['local', 'development'])) {
    Route::get('/debug-pdf/{bulan}/{tahun}/{nagari?}', [App\Http\Controllers\Test\TestAbsensiPdfController::class, 'testPdf'])
        ->name('debug.pdf');

    Route::get('/debug-data/{bulan}/{tahun}/{nagari?}', [App\Http\Controllers\Test\TestAbsensiPdfController::class, 'testData'])
        ->name('debug.data');
}