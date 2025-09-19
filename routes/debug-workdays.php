<?php

use Illuminate\Support\Facades\Route;
use App\Models\Nagari;

if (app()->environment(['local', 'development'])) {
    Route::get('/debug-workdays', function() {
        try {
            $nagari = Nagari::with('workDays')->first();

            if (!$nagari) {
                return response()->json(['error' => 'No nagari found'], 404);
            }

            $html = '<h2>Debug Work Days Configuration</h2>';
            $html .= '<h3>Nagari: ' . $nagari->name . '</h3>';

            $html .= '<table border="1" style="border-collapse: collapse;">';
            $html .= '<tr><th>Day</th><th>Is Working Day</th><th>Expected in PDF</th></tr>';

            foreach ($nagari->workDays as $workDay) {
                $expected = $workDay->is_working_day ? 'Akan muncul dalam laporan' : 'Ditandai sebagai Libur (L)';
                $color = $workDay->is_working_day ? 'green' : 'red';

                $html .= '<tr>';
                $html .= '<td>' . ucfirst($workDay->day) . '</td>';
                $html .= '<td style="color: ' . $color . '">' . ($workDay->is_working_day ? 'TRUE' : 'FALSE') . '</td>';
                $html .= '<td>' . $expected . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            $html .= '<h3>Test PDF Links:</h3>';
            $html .= '<ul>';
            $html .= '<li><a href="/pdf/absensi/9/2024?stream=true" target="_blank">September 2024 (dengan fix)</a></li>';
            $html .= '<li><a href="/pdf/absensi/8/2024?stream=true" target="_blank">Agustus 2024 (dengan fix)</a></li>';
            $html .= '</ul>';

            $html .= '<p><strong>Sekarang Sabtu/Minggu seharusnya ditandai sebagai "L" (Libur) di PDF report!</strong></p>';

            return $html;

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });
}
