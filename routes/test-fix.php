<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiPdfController;

if (app()->environment(['local', 'development'])) {
    Route::get('/test-pdf-fix/{bulan}/{tahun}', function($bulan, $tahun) {
        try {
            $controller = new AbsensiPdfController();
            $service = new \App\Services\Pdf\AbsensiReportBulananService();

            // Simulasi request
            $request = new \Illuminate\Http\Request();
            $request->merge(['stream' => true]);

            return $controller->index($bulan, $tahun, $service, $request);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('test.pdf.fix');
}
