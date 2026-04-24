<?php

namespace App\Filament\Resources\PermohonanSuratResource\Pages;

use App\Filament\Resources\PermohonanSuratResource;
use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\StatusSurat;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreatePermohonanSurat extends CreateRecord
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Derive pemohon data from Penduduk record (reliable fallback / override)
        if (!empty($data['penduduk_id'])) {
            $penduduk = Penduduk::find($data['penduduk_id']);
            if ($penduduk) {
                $data['pemohon_nik']           = $penduduk->nik;
                $data['pemohon_nama']          = $penduduk->name;
                $data['pemohon_alamat']        = $penduduk->alamat;
                $data['pemohon_telepon']       = $penduduk->no_hp;
                $data['pemohon_agama']         = $penduduk->agama;
                $data['pemohon_jk']            = $penduduk->jk;
                $data['pemohon_tempat_lahir']  = $penduduk->tempat_lahir;
                $data['pemohon_tanggal_lahir'] = $penduduk->tanggal_lahir;
            }
        }

        // Derive jenis surat data
        if (!empty($data['jenis_surat_id'])) {
            $jenisSurat = JenisSurat::find($data['jenis_surat_id']);
            if ($jenisSurat) {
                $data['pemohon_judul_surat'] = $jenisSurat->nama_jenis;
                $data['pemohon_kode_surat']  = $jenisSurat->kode_surat;
                if (empty($data['tanggal_estimasi_selesai']) && $jenisSurat->estimasi_hari) {
                    $data['tanggal_estimasi_selesai'] = now()->addDays($jenisSurat->estimasi_hari);
                }
            }
        }

        // Derive pejabat data from TandaTangan user
        if (!empty($data['TandaTangan'])) {
            $pejabat = User::with('jabatan')->find($data['TandaTangan']);
            if ($pejabat) {
                $data['PejabatTandaTangan_nama']    = $pejabat->name;
                $data['PejabatTandaTangan_jabatan'] = $pejabat->jabatan?->name ?? '';
            }
        }

        // Status default MASUK
        if (empty($data['status_id'])) {
            $statusMasuk = StatusSurat::where('kode_status', 'MASUK')->first();
            $data['status_id'] = $statusMasuk?->id;
        }

        // Tanggal permohonan
        if (empty($data['tanggal_permohonan'])) {
            $data['tanggal_permohonan'] = now();
        }

        // Nagari dari user login
        if (empty($data['nagari_id'])) {
            $data['nagari_id'] = Auth::user()?->nagari_id;
        }
        // Ambil keperluan dari form_data jika ada, fallback ke field keperluan
        if (!empty($data['form_data']['[KEPERLUAN_SURAT]'])) {
            $data['keperluan'] = $data['form_data']['[KEPERLUAN_SURAT]'];
        }

        // Nomor permohonan unik
        if (empty($data['nomor_permohonan'])) {
            $data['nomor_permohonan'] = 'BSG-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        }
        if (empty($data['petugas_id'])) {
            $data['petugas_id'] = Auth::id();
        }

        // Pastikan form_data adalah array
        $data['form_data'] = $data['form_data'] ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create tracking record untuk status pertama
        $permohonan = $this->record;

        \App\Models\TrackingSurat::create([
            'permohonan_id' => $permohonan->id,
            'status_lama_id' => null,
            'status_baru_id' => $permohonan->status_id,
            'petugas_id' => Auth::id(),
            'tanggal_perubahan' => now(),
            'catatan' => 'Permohonan baru dibuat',
        ]);
    }

    public function mount(): void
    {
        parent::mount();

        // Auto-fill data dari URL parameters (untuk buat surat baru dari riwayat)
        $queryParams = request()->query();

        if (!empty($queryParams)) {
            $autoFillData = [];

            if (isset($queryParams['nik'])) {
                $autoFillData['pemohon_nik'] = $queryParams['nik'];
            }

            if (isset($queryParams['nama'])) {
                $autoFillData['pemohon_nama'] = $queryParams['nama'];
            }

            if (isset($queryParams['alamat'])) {
                $autoFillData['pemohon_alamat'] = $queryParams['alamat'];
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

            if (!empty($autoFillData)) {
                $this->form->fill($autoFillData);
            }
        }
    }
}
