<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use App\Models\WdmsModel;
use App\Services\Pdf\AbsensiReportBulananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

/**
 * Controller untuk generate dan download laporan PDF absensi bulanan
 */
class AbsensiPdfController extends Controller
{
    /**
     * Generate dan download laporan absensi PDF
     *
     * @param int $bulan Bulan (1-12)
     * @param int $tahun Tahun
     * @param AbsensiReportBulananService $service Service untuk generate laporan
     * @return \Illuminate\Http\Response
     */
    public function index($bulan, $tahun, AbsensiReportBulananService $service)
    {
        try {
            // Validasi input
            $bulan = (int) $bulan;
            $tahun = (int) $tahun;

            if ($bulan < 1 || $bulan > 12) {
                abort(400, 'Bulan tidak valid. Harus antara 1-12.');
            }

            if ($tahun < 2020 || $tahun > now()->year + 1) {
                abort(400, 'Tahun tidak valid.');
            }

            // Pastikan user sudah login dan memiliki nagari
            if (!Auth::check() || !Auth::user()->nagari) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh laporan ini.');
            }

            $nagari_id = Auth::user()->nagari->id;
            $filename = "Laporan_Absensi_" . Auth::user()->nagari->name . "_{$bulan}_{$tahun}.pdf";

            // Generate laporan
            $report = $service->generate($tahun, $bulan, $nagari_id);

            if (!$report || !isset($report['pdf'])) {
                abort(500, 'Gagal membuat laporan PDF.');
            }

            // Download PDF langsung
            return $report['pdf']->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating PDF report: ' . $e->getMessage(), [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'user_id' => Auth::id(),
                'nagari_id' => Auth::user()->nagari->id ?? null
            ]);

            abort(500, 'Terjadi kesalahan saat membuat laporan PDF.');
        }
    }
}