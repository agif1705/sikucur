<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\Nagari;
use App\Models\PermohonanSurat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class SuratPdfController extends Controller
{
 public function preview(PermohonanSurat $permohonan)
 {
  abort_unless(Auth::check(), 403);

  $permohonan->load(['jenisSurat', 'status', 'nagari', 'petugas', 'penduduk']);

  $pdf = PDF::loadView('pdf.surat-permohonan', [
   'permohonan' => $permohonan,
   'logo'       => $this->resolveLogoPath(),
  ]);

  $pdf->setPaper('F4', 'portrait');

  return $pdf->stream("surat-{$permohonan->nomor_permohonan}.pdf");
 }

 public function download(PermohonanSurat $permohonan)
 {
  abort_unless(Auth::check(), 403);

  $permohonan->load([
   'jenisSurat',
   'status',
   'nagari',
   'petugas',
   'penduduk',
  ]);

  $pdf = PDF::loadView('pdf.surat-permohonan', [
   'permohonan' => $permohonan,
   'logo'       => $this->resolveLogoPath(),
  ]);

  $pdf->setPaper('F4', 'portrait');

  return $pdf->download("surat-{$permohonan->nomor_permohonan}.pdf");
 }

 /**
  * Resolve logo as base64 data URI so DomPDF can read it regardless of OS path format.
  * Tries SuratKepala record first, falls back to storage/app/public/logosurat.png.
  */
 private function resolveLogoPath(): string
 {
  $nagari = Nagari::with('suratKepala')->find(1);
  $kepala = $nagari?->suratKepala;

  $absPath = null;

  if ($kepala?->logo) {
   $p = Storage::disk('public')->path($kepala->logo);
   if (file_exists($p)) {
    $absPath = $p;
   }
  }

  if (!$absPath) {
   $absPath = Storage::disk('public')->path('logosurat.png');
  }

  if (!file_exists($absPath)) {
   return '';
  }

  $mime = mime_content_type($absPath) ?: 'image/png';
  return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absPath));
 }
}
