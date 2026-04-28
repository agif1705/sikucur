<?php

namespace App\Filament\Resources\PermohonanSurats\Pages;

use App\Filament\Resources\PermohonanSurats\PermohonanSuratResource;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\StatusSurat;
use App\Models\SuratPengantar;
use App\Models\TrackingSurat;
use App\Models\User;
use App\Services\SuratPengantarNotificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatePermohonanSurat extends CreateRecord
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['surat_pengantar_id'])) {
            throw ValidationException::withMessages([
                'surat_pengantar_id' => 'Surat pengantar wajib diisi sebelum membuat surat.',
            ]);
        }

        $pengantar = SuratPengantar::find($data['surat_pengantar_id']);
        if (! $pengantar || $pengantar->status !== SuratPengantar::STATUS_SUBMITTED) {
            throw ValidationException::withMessages([
                'surat_pengantar_id' => 'Surat pengantar belum selesai diisi.',
            ]);
        }

        if ($pengantar->permohonanSurat()->exists()) {
            throw ValidationException::withMessages([
                'surat_pengantar_id' => 'Surat pengantar ini sudah dipakai untuk permohonan surat lain.',
            ]);
        }

        if (! empty($data['penduduk_id'])) {
            $penduduk = Penduduk::find($data['penduduk_id']);
            if ($penduduk) {
                $data['pemohon_nik'] = $penduduk->nik;
                $data['pemohon_nama'] = $penduduk->name;
                $data['pemohon_alamat'] = $penduduk->alamat;
                $data['pemohon_alamat_domisili'] = $penduduk->alamat_domisili;
                $data['pemohon_telepon'] = $penduduk->no_hp;
                $data['pemohon_agama'] = $penduduk->agama;
                $data['pemohon_jk'] = $penduduk->jk;
                $data['pemohon_tempat_lahir'] = $penduduk->tempat_lahir;
                $data['pemohon_tanggal_lahir'] = $penduduk->tanggal_lahir;
            }
        }

        if (! empty($data['jenis_surat_id'])) {
            $jenisSurat = JenisSurat::find($data['jenis_surat_id']);
            if ($jenisSurat) {
                $data['pemohon_judul_surat'] = $jenisSurat->nama_jenis;
                $data['pemohon_kode_surat'] = $jenisSurat->kode_surat;

                if (empty($data['tanggal_estimasi_selesai']) && $jenisSurat->estimasi_hari) {
                    $data['tanggal_estimasi_selesai'] = now()->addDays($jenisSurat->estimasi_hari);
                }
            }
        }

        if (! empty($data['TandaTangan'])) {
            $pejabat = User::with('jabatan')->find($data['TandaTangan']);
            if ($pejabat) {
                $data['PejabatTandaTangan_nama'] = $pejabat->name;
                $data['PejabatTandaTangan_jabatan'] = $pejabat->jabatan?->name ?? '';
            }
        }

        $data['penduduk_id'] = $data['penduduk_id'] ?? $pengantar->penduduk_id;
        $data['pemohon_nik'] = $data['pemohon_nik'] ?? $pengantar->pemohon_nik;
        $data['pemohon_nama'] = $data['pemohon_nama'] ?? $pengantar->pemohon_nama;
        $data['pemohon_alamat'] = $data['pemohon_alamat'] ?? $pengantar->pemohon_alamat;
        $data['pemohon_alamat_domisili'] = $data['pemohon_alamat_domisili'] ?? $pengantar->pemohon_alamat_domisili;
        $data['pemohon_telepon'] = $data['pemohon_telepon'] ?? $pengantar->pemohon_telepon;
        $data['keperluan'] = $data['keperluan'] ?? $pengantar->keperluan;

        if (empty($data['status_id'])) {
            $data['status_id'] = StatusSurat::where('kode_status', 'MASUK')->value('id');
        }

        $data['tanggal_permohonan'] = $data['tanggal_permohonan'] ?? now();
        $data['nagari_id'] = $data['nagari_id'] ?? Auth::user()?->nagari_id;
        $data['petugas_id'] = $data['petugas_id'] ?? Auth::id();

        if (! empty($data['form_data']['[KEPERLUAN_SURAT]'])) {
            $data['keperluan'] = $data['form_data']['[KEPERLUAN_SURAT]'];
        }

        $data['nomor_permohonan'] = $data['nomor_permohonan']
            ?? 'BSG-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        $data['form_data'] = $data['form_data'] ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        $permohonan = $this->record;

        TrackingSurat::create([
            'permohonan_id' => $permohonan->id,
            'status_lama_id' => null,
            'status_baru_id' => $permohonan->status_id,
            'petugas_id' => Auth::id(),
            'tanggal_perubahan' => now(),
            'catatan' => 'Permohonan baru dibuat',
        ]);

        app(SuratPengantarNotificationService::class)->notifyPermohonanCreated($permohonan);
    }

    public function mount(): void
    {
        parent::mount();

        $queryParams = request()->query();
        if (empty($queryParams)) {
            return;
        }

        $autoFillData = [];

        if (isset($queryParams['surat_pengantar_id'])) {
            $pengantar = SuratPengantar::find($queryParams['surat_pengantar_id']);

            if ($pengantar) {
                $autoFillData['surat_pengantar_id'] = $pengantar->id;
                $autoFillData['penduduk_id'] = $pengantar->penduduk_id;
                $autoFillData['selected_penduduk'] = $pengantar->penduduk_id;
                $autoFillData['pemohon_nik'] = $pengantar->pemohon_nik;
                $autoFillData['pemohon_nama'] = $pengantar->pemohon_nama;
                $autoFillData['pemohon_alamat'] = $pengantar->pemohon_alamat;
                $autoFillData['pemohon_alamat_domisili'] = $pengantar->pemohon_alamat_domisili;
                $autoFillData['pemohon_telepon'] = $pengantar->pemohon_telepon;
                $autoFillData['keperluan'] = $pengantar->keperluan;
            }
        }

        if (isset($queryParams['nik'])) {
            $autoFillData['pemohon_nik'] = $queryParams['nik'];
        }

        if (isset($queryParams['nama'])) {
            $autoFillData['pemohon_nama'] = $queryParams['nama'];
        }

        if (isset($queryParams['alamat'])) {
            $autoFillData['pemohon_alamat'] = $queryParams['alamat'];
        }

        if (isset($queryParams['alamat_domisili'])) {
            $autoFillData['pemohon_alamat_domisili'] = $queryParams['alamat_domisili'];
        }

        if (isset($queryParams['telepon'])) {
            $autoFillData['pemohon_telepon'] = $queryParams['telepon'];
        }

        if (isset($queryParams['email'])) {
            $autoFillData['pemohon_email'] = $queryParams['email'];
        }

        if (isset($queryParams['nagari_id'])) {
            $autoFillData['nagari_id'] = $queryParams['nagari_id'];
        }

        if ($autoFillData !== []) {
            $this->form->fill($autoFillData);
        }
    }
}
