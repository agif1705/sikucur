<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
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

        if (
            $pengantar->used
            && in_array($pengantar->status, [
                SuratPengantar::STATUS_WAITING_APPROVAL,
                SuratPengantar::STATUS_SUBMITTED,
                SuratPengantar::STATUS_REJECTED,
            ], true)
        ) {
            return back()->withErrors([
                'keperluan' => 'Surat pengantar sudah diproses dan tidak dapat diubah lagi.',
            ]);
        }

        return view('surat.pengantar-form', [
            'pengantar' => $pengantar,
            'wilayahs' => $this->getWilayahs(),
            'jenisSurats' => $this->getJenisSurats(),
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
            'jenis_surat_id' => ['required', 'exists:jenis_surat,id'],
            'keperluan' => ['required', 'string'],
            'tanggal_pengantar' => ['nullable', 'date'],
        ]);

        $penduduk = Penduduk::where('nik', $validated['pemohon_nik'])->first();

        // Untuk link warga yang belum cocok via no_hp, NIK wajib ditemukan.
        if (! $pengantar->penduduk_id && ! $penduduk) {
            return back()->withErrors([
                'pemohon_nik' => 'Daftarkan NIK/ kependudukan ada di Kantor Nagari',
            ])->withInput();
        }

        if ($penduduk) {
            $pengantar->penduduk_id = $penduduk->id;
        }

        if (blank($validated['pemohon_telepon'] ?? null) && filled($pengantar->pemohon_telepon)) {
            $validated['pemohon_telepon'] = $pengantar->pemohon_telepon;
        }

        $masterPenduduk = null;
        if ($pengantar->penduduk_id) {
            $masterPenduduk = Penduduk::find($pengantar->penduduk_id);
        } elseif ($penduduk) {
            $masterPenduduk = $penduduk;
        }

        if ($masterPenduduk) {
            $this->syncMissingPendudukData($masterPenduduk, $validated);
            $pengantar->penduduk_id = $masterPenduduk->id;
        }

        // Jika link ini milik warga terdaftar (auto prefill dari no_hp), data identitas
        // harus mengikuti master penduduk dan warga hanya mengisi keperluan.
        if ($masterPenduduk) {
            $validated['pemohon_nik'] = $masterPenduduk->nik ?: $validated['pemohon_nik'];
            $validated['pemohon_nama'] = $masterPenduduk->name ?: $validated['pemohon_nama'];
            $validated['pemohon_alamat'] = $masterPenduduk->alamat ?: $validated['pemohon_alamat'];
            $validated['pemohon_alamat_domisili'] = $masterPenduduk->alamat_domisili
                ?: ($validated['pemohon_alamat_domisili'] ?? $validated['pemohon_alamat']);
            $validated['pemohon_telepon'] = $masterPenduduk->no_hp ?: ($validated['pemohon_telepon'] ?? null);
            $validated['korong'] = $masterPenduduk->korong ?: $validated['korong'];
        }

        $waliKorong = WaliKorong::where('nagari_id', $pengantar->nagari_id)
            ->where('wilayah', $validated['korong'])
            ->first();

        if (! $waliKorong) {
            return back()->withErrors([
                'korong' => 'Wali korong untuk wilayah ini tidak ditemukan.',
            ])->withInput();
        }

        $pengantar->fill([
            'pemohon_nik' => $validated['pemohon_nik'],
            'pemohon_nama' => $validated['pemohon_nama'],
            'pemohon_alamat' => $validated['pemohon_alamat'],
            'pemohon_alamat_domisili' => $validated['pemohon_alamat_domisili'] ?? null,
            'pemohon_telepon' => $validated['pemohon_telepon'] ?? null,
            'korong' => $validated['korong'],
            'jenis_surat_id' => $validated['jenis_surat_id'],
            'keperluan' => $validated['keperluan'],
            'tanggal_pengantar' => now()->toDateString(),
            'expired_at' => now()->addHours(12),
            'wali_korong_id' => $waliKorong->id,
            'status' => SuratPengantar::STATUS_WAITING_APPROVAL,
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
            'jenisSurats' => $this->getJenisSurats(),
            'submitUrl' => $this->generateSubmitUrl($pengantar),
            'success' => true,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    public function approve(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Link persetujuan tidak valid.');
        }

        $pengantar = SuratPengantar::where('token', $token)->firstOrFail();

        if ($pengantar->expired_at && $pengantar->expired_at->isPast()) {
            abort(410, 'Link persetujuan sudah kedaluwarsa.');
        }

        if ($pengantar->status === SuratPengantar::STATUS_REJECTED) {
            return response('Surat pengantar ini sudah ditolak sebelumnya.', 200);
        }

        $pengantar->status = SuratPengantar::STATUS_SUBMITTED;
        $pengantar->wali_response_at = now();
        $pengantar->save();

        return response('Surat pengantar disetujui. Pegawai dapat melanjutkan pembuatan surat.', 200);
    }

    public function reject(Request $request, string $token)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Link penolakan tidak valid.');
        }

        $pengantar = SuratPengantar::where('token', $token)->firstOrFail();

        if ($pengantar->expired_at && $pengantar->expired_at->isPast()) {
            abort(410, 'Link penolakan sudah kedaluwarsa.');
        }

        if ($pengantar->status === SuratPengantar::STATUS_SUBMITTED) {
            return response('Surat pengantar ini sudah disetujui sebelumnya.', 200);
        }

        $pengantar->status = SuratPengantar::STATUS_REJECTED;
        $pengantar->wali_response_at = now();
        $pengantar->save();

        return response('Surat pengantar ditolak.', 200);
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

    private function getJenisSurats(): array
    {
        return JenisSurat::query()
            ->orderBy('nama_jenis')
            ->pluck('nama_jenis', 'id')
            ->all();
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

    private function syncMissingPendudukData(Penduduk $penduduk, array $validated): void
    {
        $updates = [];

        if (blank($penduduk->name) && filled($validated['pemohon_nama'] ?? null)) {
            $updates['name'] = $validated['pemohon_nama'];
        }

        if (blank($penduduk->alamat) && filled($validated['pemohon_alamat'] ?? null)) {
            $updates['alamat'] = $validated['pemohon_alamat'];
        }

        if (blank($penduduk->alamat_domisili)) {
            $alamatDomisili = $validated['pemohon_alamat_domisili'] ?? null;
            if (blank($alamatDomisili)) {
                $alamatDomisili = $validated['pemohon_alamat'] ?? null;
            }

            if (filled($alamatDomisili)) {
                $updates['alamat_domisili'] = $alamatDomisili;
            }
        }

        if (blank($penduduk->no_hp) && filled($validated['pemohon_telepon'] ?? null)) {
            $updates['no_hp'] = $validated['pemohon_telepon'];
        }

        if (blank($penduduk->korong) && filled($validated['korong'] ?? null)) {
            $updates['korong'] = $validated['korong'];
        }

        if ($updates !== []) {
            $penduduk->fill($updates);
            $penduduk->save();
        }
    }
}
