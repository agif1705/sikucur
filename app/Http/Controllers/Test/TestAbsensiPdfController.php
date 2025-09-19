<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Services\Pdf\AbsensiReportBulananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Test controller untuk debugging PDF absensi
 * Hanya aktif di environment development
 */
class TestAbsensiPdfController extends Controller
{
    protected $absensiService;

    public function __construct(AbsensiReportBulananService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    /**
     * Test PDF generation dengan parameter yang diberikan
     */
    public function testPdf($bulan, $tahun, $nagari = 1)
    {
        // Hanya allow di development environment
        if (!app()->environment(['local', 'development'])) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        try {
            Log::info('Testing PDF generation', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nagari' => $nagari
            ]);

            $result = $this->absensiService->generate((int)$bulan, (int)$tahun, (int)$nagari);

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                    'message' => 'PDF generation failed'
                ], 500);
            }

            // Stream PDF ke browser
            return $result['pdf']->stream("test-absensi-{$bulan}-{$tahun}.pdf");

        } catch (\Exception $e) {
            Log::error('Test PDF generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nagari' => $nagari
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test struktur data tanpa generate PDF
     */
    public function testData($bulan, $tahun, $nagari = 1)
    {
        // Hanya allow di development environment
        if (!app()->environment(['local', 'development'])) {
            return response()->json(['error' => 'Not available in production'], 403);
        }

        try {
            $result = $this->absensiService->generate((int)$bulan, (int)$tahun, (int)$nagari);

            return response()->json([
                'success' => true,
                'data_structure' => isset($result['data']) ? array_keys($result['data']) : 'no data key',
                'has_pdf' => isset($result['pdf']),
                'has_error' => isset($result['error']),
                'sample_data' => isset($result['data']) && !empty($result['data'])
                    ? collect($result['data'])->take(1)->toArray()
                    : 'no data',
                'parameters' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nagari' => $nagari
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'parameters' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nagari' => $nagari
                ]
            ], 500);
        }
    }
}
