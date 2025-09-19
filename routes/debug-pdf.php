<?php
// Test file untuk debugging PDF stream vs download
// Akses via /debug-pdf-params

use Illuminate\Support\Facades\Route;

if (app()->environment(['local', 'development'])) {
    Route::get('/debug-pdf-params', function(\Illuminate\Http\Request $request) {

        // Simulasi parameter yang berbeda
        $html .= '<h3>Debug Work Days Issue</h3>';
        $html .= '<p><strong>Masalah:</strong> Sabtu & Minggu masih muncul di report padahal di work_days bernilai false</p>';
        $html .= '<p><strong>Solusi:</strong> Service sekarang akan check work_days nagari untuk exclude weekend</p>';

        $html .= '<h4>Test dengan bulan yang berbeda:</h4>';
        $testCases = [
            ['url' => '/pdf/absensi/9/2024?stream=true', 'expected' => 'September 2024 - Sabtu/Minggu tidak dihitung sebagai hari kerja'],
            ['url' => '/pdf/absensi/8/2024?stream=true', 'expected' => 'Agustus 2024 - Sabtu/Minggu tidak dihitung sebagai hari kerja'],
            ['url' => '/pdf/absensi/10/2024?stream=true', 'expected' => 'Oktober 2024 - Sabtu/Minggu tidak dihitung sebagai hari kerja'],
        ];

        $html = '<h2>PDF URL Parameter Debug</h2>';
        $html .= '<p>Current month: ' . now()->month . ', Current year: ' . now()->year . '</p>';
        $html .= '<table border="1" style="border-collapse: collapse; width: 100%;">';
        $html .= '<tr><th>Test URL</th><th>Expected Behavior</th><th>Test Link</th></tr>';

        foreach($testCases as $case) {
            $html .= '<tr>';
            $html .= '<td>' . $case['url'] . '</td>';
            $html .= '<td>' . $case['expected'] . '</td>';
            $html .= '<td><a href="' . $case['url'] . '" target="_blank">Test</a></td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $html .= '<h3>Tips:</h3>';
        $html .= '<ul>';
        $html .= '<li>URL tanpa parameter → Stream (default)</li>';
        $html .= '<li>URL dengan ?stream=true → Stream</li>';
        $html .= '<li>URL dengan ?download=true → Download</li>';
        $html .= '</ul>';

        $html .= '<h3>Klik link di atas untuk test behavior yang berbeda</h3>';

        return $html;
    });
}
