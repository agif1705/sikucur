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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class AbsensiPdfController extends Controller
{
    public function index($bulan, $tahun, AbsensiReportBulananService $service)
    {
        $filename = "absensi-pegawai-{$bulan}-{$tahun}.pdf";
        $path = storage_path("app/private/public/absensi/{$filename}");
        $nagari_id = auth()->user()->nagari->id;
        if (file_exists($path)) {
            return response()->download($path);
        }
        $report = $service->generate($tahun, $bulan, $nagari_id);
        return $report['pdf']->download($report['filename']);
    }
}
