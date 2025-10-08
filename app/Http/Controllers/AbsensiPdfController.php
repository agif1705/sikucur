<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use PDF;
use App\Models\WdmsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;
use App\Services\Pdf\AbsensiReportBulananService;

class AbsensiPdfController extends Controller
{
    public function index($bulan, $tahun, AbsensiReportBulananService $service, Request $request)
    {
        try {
            Log::info('PDF Generation Started', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'user_id' => Auth::id()
            ]);

            $bulan = (int) $bulan;
            $tahun = (int) $tahun;

            $isStream = $request->query('stream', false);
            $isDownload = $request->query('download', false);
            $shouldStream = $isStream || (!$isDownload);

            if ($bulan < 1 || $bulan > 12) {
                abort(400, 'Bulan tidak valid. Harus antara 1-12.');
            }

            if ($tahun < 2020 || $tahun > now()->year + 1) {
                abort(400, 'Tahun tidak valid.');
            }

            if (!Auth::check() || !Auth::user()->nagari) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh laporan ini.');
            }

            $nagari_id = Auth::user()->nagari->id;
            $nagari_name = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', Auth::user()->nagari->name);

            // Generate filename sederhana untuk tracking yang mudah
            $filename = "Laporan_Absensi_{$nagari_name}_{$bulan}_{$tahun}.pdf";

            // Cek apakah PDF sudah ada di storage (1 file per bulan-tahun-nagari)
            $storagePath = "public/absensi/{$filename}";
            // Generate laporan baru
            $report = $service->generate($tahun, $bulan, $nagari_id);

            if (!$report || !isset($report['pdf'])) {
                abort(500, 'Gagal membuat laporan PDF.');
            }

            // Simpan ke storage
            try {
                $directory = 'public/absensi';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }
                Storage::put($storagePath, $report['pdf']->output());
                Log::info('PDF saved to storage successfully');
            } catch (\Exception $storageError) {
                Log::error('Failed to save PDF to storage: ' . $storageError->getMessage());
            }

            // Stream atau download
            if ($shouldStream) {
                return $report['pdf']->stream($filename);
            } else {
                return $report['pdf']->download($filename);
            }
        } catch (\Exception $e) {
            Log::error('Error generating PDF report: ' . $e->getMessage());

            try {
                $simplePdf = PDF::loadHTML('<h1>Error</h1><p>' . $e->getMessage() . '</p>');
                return $simplePdf->download('error_debug.pdf');
            } catch (\Exception $pdfError) {
                abort(500, 'Terjadi kesalahan fatal saat membuat laporan PDF: ' . $e->getMessage());
            }
        }
    }

    public function test()
    {
        try {
            $html = '<h1>Test PDF Generation</h1><p>Timestamp: ' . now() . '</p>';
            $pdf = PDF::loadHTML($html);
            return $pdf->stream('test.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
