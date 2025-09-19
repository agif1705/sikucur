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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index($bulan, $tahun, AbsensiReportBulananService $service, Request $request)
    {
        try {
            Log::info('PDF Generation Started', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'user_id' => Auth::id()
            ]);

            // Validasi input
            $bulan = (int) $bulan;
            $tahun = (int) $tahun;
            $isDownload = $request->query('download', false); // Default download

            if ($bulan < 1 || $bulan > 12) {
                Log::warning('Invalid month provided', ['bulan' => $bulan]);
                abort(400, 'Bulan tidak valid. Harus antara 1-12.');
            }

            if ($tahun < 2020 || $tahun > now()->year + 1) {
                Log::warning('Invalid year provided', ['tahun' => $tahun]);
                abort(400, 'Tahun tidak valid.');
            }

            // Pastikan user sudah login dan memiliki nagari
            if (!Auth::check() || !Auth::user()->nagari) {
                Log::warning('User access denied', [
                    'authenticated' => Auth::check(),
                    'has_nagari' => Auth::user() ? (bool) Auth::user()->nagari : false
                ]);
                abort(403, 'Anda tidak memiliki akses untuk mengunduh laporan ini.');
            }

            $nagari_id = Auth::user()->nagari->id;
            $nagari_name = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', Auth::user()->nagari->name);
            $filename = "Laporan_Absensi_{$nagari_name}_{$bulan}_{$tahun}.pdf";

            Log::info('Generating PDF with parameters', [
                'nagari_id' => $nagari_id,
                'filename' => $filename
            ]);

            // Generate laporan
            $report = $service->generate($tahun, $bulan, $nagari_id);

            if (!$report || !isset($report['pdf'])) {
                Log::error('Failed to generate PDF report', [
                    'report_exists' => (bool) $report,
                    'pdf_exists' => isset($report['pdf']) ?? false
                ]);
                abort(500, 'Gagal membuat laporan PDF.');
            }

            Log::info('PDF generated successfully', [
                'filename' => $filename,
                'has_data' => isset($report['data']),
                'data_count' => isset($report['data']) ? count($report['data']) : 0
            ]);

            // Simpan ke storage untuk debugging
            try {
                $storagePath = "public/test/{$filename}";
                Storage::put($storagePath, $report['pdf']->output());

                Log::info('PDF saved to storage successfully', [
                    'path' => $storagePath,
                    'size' => Storage::size($storagePath) . ' bytes'
                ]);
            } catch (\Exception $storageError) {
                Log::error('Failed to save PDF to storage', [
                    'error' => $storageError->getMessage(),
                    'path' => $storagePath ?? 'unknown'
                ]);
                // Continue dengan download meskipun gagal simpan ke storage
            }

            // Download atau stream berdasarkan parameter
            if ($isDownload) {
                Log::info('Downloading PDF', ['filename' => $filename]);
                return $report['pdf']->download($filename);
            } else {
                Log::info('Streaming PDF', ['filename' => $filename]);
                return $report['pdf']->stream($filename);
            }

        } catch (\Exception $e) {
            Log::error('Error generating PDF report: ' . $e->getMessage(), [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'user_id' => Auth::id(),
                'nagari_id' => Auth::user()->nagari->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Coba buat PDF sederhana untuk debugging
            try {
                $simplePdf = PDF::loadHTML('<h1>Test PDF - Error Debugging</h1><p>Error: ' . $e->getMessage() . '</p>');
                return $simplePdf->download('error_debug.pdf');
            } catch (\Exception $pdfError) {
                Log::error('Even simple PDF failed', ['error' => $pdfError->getMessage()]);
                abort(500, 'Terjadi kesalahan fatal saat membuat laporan PDF: ' . $e->getMessage());
            }
        }
    }

    /**
     * Method untuk testing PDF generation tanpa data kompleks
     */
    public function test()
    {
        try {
            Log::info('Testing PDF generation');

            $html = '
            <h1>Test PDF Generation</h1>
            <p>Timestamp: ' . now() . '</p>
            <p>User: ' . (Auth::user()->name ?? 'Guest') . '</p>
            <table border="1" style="border-collapse: collapse;">
                <tr>
                    <th>No</th>
                    <th>Test Data</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Sample Row 1</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Sample Row 2</td>
                </tr>
            </table>
            ';

            $pdf = PDF::loadHTML($html);
            $filename = 'test_pdf_' . now()->format('Y_m_d_H_i_s') . '.pdf';

            // Simpan ke storage
            $storagePath = "public/test/{$filename}";
            Storage::makeDirectory('public/test');
            Storage::put($storagePath, $pdf->output());

            Log::info('Test PDF created successfully', [
                'filename' => $filename,
                'path' => $storagePath,
                'size' => Storage::size($storagePath) . ' bytes'
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Test PDF failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Test PDF failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
