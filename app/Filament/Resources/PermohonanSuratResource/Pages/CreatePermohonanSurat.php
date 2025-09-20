<?php

namespace App\Filament\Resources\PermohonanSuratResource\Pages;

use App\Filament\Resources\PermohonanSuratResource;
use App\Models\StatusSurat;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatePermohonanSurat extends CreateRecord
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set status default ke 'MSK' (Masuk)
        if (!isset($data['status_id'])) {
            $statusMasuk = StatusSurat::where('kode_status', 'MSK')->first();
            if ($statusMasuk) {
                $data['status_id'] = $statusMasuk->id;
            }
        }

        // Set tanggal permohonan jika belum diset
        if (!isset($data['tanggal_permohonan'])) {
            $data['tanggal_permohonan'] = now();
        }

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
