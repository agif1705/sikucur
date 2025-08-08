<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tv\InformasiTvLivewire;
use App\Livewire\Homepage\HomePageLivewire;
use App\Livewire\Homepage\AgendaPageLivewire;
use App\Livewire\Homepage\KritikPageLivewire;
use App\Http\Controllers\AbsensiPdfController;
use App\Http\Controllers\FingerPrintController;
use App\Http\Controllers\FonnteController;
use App\Livewire\Homepage\KegiatanPageLivewire;
use App\Livewire\TvInformasi\TvNagariLivewire;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', HomePageLivewire::class)->name('home');
Route::get('/fingerprint', [FingerPrintController::class, 'index'])->name('fingerprint');
Route::get('kegiatan', KegiatanPageLivewire::class)->name('kegiatan');
Route::get('kritik', KritikPageLivewire::class)->name('kritik');
Route::get('agenda', AgendaPageLivewire::class)->name('agenda');
Route::get('/tv/{sn}', InformasiTvLivewire::class)->name('tvinformasi');
Route::get('/pdf/absensi/{bulan}/{tahun}', [AbsensiPdfController::class, 'index'])->name('absensipdf');
