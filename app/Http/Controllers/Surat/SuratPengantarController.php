<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\Nagari;
use App\Models\Penduduk;
use App\Models\SuratPengantar;
use App\Models\WaliKorong;
use App\Services\SuratPengantarNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PDF;

class SuratPengantarController extends Controller
{
    public function form(string $token)
    {
        $pengantar = SuratPengantar::where('token', $token)->firstOrFail();

        if ($pengantar->expired_at && $pengantar->expired_at->isPast()) {
            abort(410, 'Link surat pengantar sudah kedaluwarsa.');
        }

        return view('surat.pengantar-form', [
            'pengantar' => $pengantar,
            'wilayahs' => $this->getWilayahs(),
            'submitUrl' => $this->generateSubmitUrl($pengantar),
        ]);
    }

    public function submit(Request $request, string $token, SuratPengantarNotificationService $notificationService)
    {
        $pengantar = SuratPengantar::where('token', $token)->firstOrFail();

        if ($pengantar->expired_at && $pengantar->expired_at->isPast()) {
            abort(410, 'Link surat pengantar sudah kedaluwarsa.');
        }

        $validated = $request->validate([
            'pemohon_nik' => ['required', 'digits:16'],
            'pemohon_nama' => ['required', 'string', 'max:255'],
            'pemohon_alamat' => ['required', 'string'],
            'pemohon_alamat_domisili' => ['nullable', 'string'],
            'pemohon_telepon' => ['nullable', 'string', 'max:20'],
            'korong' => ['required', 'string'],
            'keperluan' => ['required', 'string'],
            'tanggal_pengantar' => ['nullable', 'date'],
        ]);

        $waliKorong = WaliKorong::where('nagari_id', $pengantar->nagari_id)
            ->where('wilayah', $validated['korong'])
            ->first();

        if (! $waliKorong) {
            return back()->withErrors([
                'korong' => 'Wali korong untuk wilayah ini tidak ditemukan.',
            ])->withInput();
        }

        $penduduk = Penduduk::where('nik', $validated['pemohon_nik'])->first();
        if ($penduduk) {
            $pengantar->penduduk_id = $penduduk->id;
            if (! $penduduk->korong) {
                $penduduk->korong = $validated['korong'];
                $penduduk->save();
            }
        }

        $pengantar->fill([
            'pemohon_nik' => $validated['pemohon_nik'],
            'pemohon_nama' => $validated['pemohon_nama'],
            'pemohon_alamat' => $validated['pemohon_alamat'],
            'pemohon_alamat_domisili' => $validated['pemohon_alamat_domisili'] ?? null,
            'pemohon_telepon' => $validated['pemohon_telepon'] ?? null,
            'korong' => $validated['korong'],
            'keperluan' => $validated['keperluan'],
            'tanggal_pengantar' => $validated['tanggal_pengantar'] ?? now()->toDateString(),
            'wali_korong_id' => $waliKorong->id,
            'status' => SuratPengantar::STATUS_SUBMITTED,
            'used' => true,
        ]);
        $pengantar->save();

        $notificationService->notifyPengantarSubmitted($pengantar);

        $downloadUrl = URL::temporarySignedRoute('surat.pengantar.download', now()->addMinutes(30), [
            'token' => $pengantar->token,
        ]);

        return view('surat.pengantar-form', [
            'pengantar' => $pengantar,
            'wilayahs' => $this->getWilayahs(),
            'submitUrl' => $this->generateSubmitUrl($pengantar),
            'success' => true,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    public function template()
    {
        $pdf = PDF::loadView('pdf.surat-pengantar', [
            'pengantar' => null,
            'logo' => $this->resolveLogoPath(),
        ]);

        $pdf->setPaper('F4', 'portrait');

        return $pdf->download('surat-pengantar-template.pdf');
    }

    public function download(string $token)
    {
        $pengantar = SuratPengantar::where('token', $token)->firstOrFail();

        $pdf = PDF::loadView('pdf.surat-pengantar', [
            'pengantar' => $pengantar,
            'logo' => $this->resolveLogoPath(),
        ]);

        $pdf->setPaper('F4', 'portrait');

        return $pdf->download('surat-pengantar-'.$pengantar->id.'.pdf');
    }

    public function preview(SuratPengantar $pengantar)
    {
        abort_unless(Auth::check(), 403);

        $pdf = PDF::loadView('pdf.surat-pengantar', [
            'pengantar' => $pengantar,
            'logo' => $this->resolveLogoPath(),
        ]);

        $pdf->setPaper('F4', 'portrait');

        return $pdf->stream('surat-pengantar-'.$pengantar->id.'.pdf');
    }

    private function getWilayahs(): array
    {
        return SuratPengantar::WILAYAHS;
    }

    private function generateSubmitUrl(SuratPengantar $pengantar): string
    {
        return URL::temporarySignedRoute(
            'surat.pengantar.submit',
            $pengantar->expired_at ?? now()->addMinutes(30),
            ['token' => $pengantar->token],
        );
    }

    private function resolveLogoPath(): string
    {
        $nagari = Nagari::with('suratKepala')->find(1);
        $kepala = $nagari?->suratKepala;

        $absPath = null;

        if ($kepala?->logo) {
            $path = Storage::disk('public')->path($kepala->logo);
            if (file_exists($path)) {
                $absPath = $path;
            }
        }

        if (! $absPath) {
            $absPath = Storage::disk('public')->path('logosurat.png');
        }

        if (! file_exists($absPath)) {
            return '';
        }

        $mime = mime_content_type($absPath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($absPath));
    }
}
