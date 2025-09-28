<?php

namespace App\Http\Controllers\Surat;

use App\Models\Nagari;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PdfSuratPeringatanController extends Controller
{
    public static function integerToRoman($number)
    {
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }

    public function index(Request $request)
    {
        $nagari = Nagari::find(1);
        // dd($nagari->wali->name); // Ganti dengan ID nagari yang sesuai
        $nagariKop = $nagari->suratKepala;
        // Contoh data, bisa diganti dari DB / Filament form
        $data = [
            'nomor' => '800/____/NS/' . self::integerToRoman(now()->month) . "/" . now()->year,
            'kota' => 'Basung',
            'tanggal' => now()->format('d-F-Y'),
            'lampiran' => '-',
            'perihal' => 'Surat Peringatan I',
            'kepada' => 'Sdr. Pegawai Nagari',
            'tahun' => '2025',
            'penandatangan' => $nagari->wali->name,
            'logo' => Storage::disk('public')->path($nagariKop->logo),
        ];
        $pdf = PDF::loadView('pdf.template-surat-peringatan', $data)
            ->setPaper([0, 0, 595.28, 935.43], 'portrait');

        // unduh
        return $pdf->stream('surat_peringatan.pdf');
        // atau tampil di browser: return $pdf->stream('surat_peringatan.pdf');
    }
}