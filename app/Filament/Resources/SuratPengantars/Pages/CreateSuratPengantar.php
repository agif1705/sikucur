<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
use App\Models\Penduduk;
use App\Models\SuratPengantar;
use App\Models\WaliKorong;
use App\Services\SuratPengantarNotificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateSuratPengantar extends CreateRecord
{
    protected static string $resource = SuratPengantarResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] = $data['token'] ?? Str::random(32);
        $data['status'] = $data['status'] ?? SuratPengantar::STATUS_WAITING_APPROVAL;
        $data['used'] = true;
        $data['expired_at'] = $data['expired_at'] ?? now()->addHours(12);
        $data['nagari_id'] = $data['nagari_id'] ?? Auth::user()?->nagari_id;
        $data['petugas_id'] = $data['petugas_id'] ?? Auth::id();

        if (empty($data['wali_korong_id']) && ! empty($data['korong'])) {
            $data['wali_korong_id'] = WaliKorong::query()
                ->where('nagari_id', $data['nagari_id'])
                ->where('wilayah', $data['korong'])
                ->value('id');
        }

        if (empty($data['wali_korong_id'])) {
            throw ValidationException::withMessages([
                'wali_korong_id' => 'Wali korong untuk wilayah ini belum tersedia di data master.',
            ]);
        }

        $this->syncPendudukFromData($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->status === SuratPengantar::STATUS_WAITING_APPROVAL) {
            app(SuratPengantarNotificationService::class)->notifyPengantarSubmitted($this->record);
        }
    }

    private function syncPendudukFromData(array &$data): void
    {
        $penduduk = null;

        if (! empty($data['penduduk_id'])) {
            $penduduk = Penduduk::find($data['penduduk_id']);
        }

        if (! $penduduk && ! empty($data['pemohon_nik'])) {
            $penduduk = Penduduk::where('nik', $data['pemohon_nik'])->first();
        }

        if (! $penduduk) {
            return;
        }

        $data['penduduk_id'] = $penduduk->id;

        $updates = [];

        if (blank($penduduk->name) && filled($data['pemohon_nama'] ?? null)) {
            $updates['name'] = $data['pemohon_nama'];
        }

        if (blank($penduduk->alamat) && filled($data['pemohon_alamat'] ?? null)) {
            $updates['alamat'] = $data['pemohon_alamat'];
        }

        if (blank($penduduk->alamat_domisili)) {
            $alamatDomisili = $data['pemohon_alamat_domisili'] ?? null;
            if (blank($alamatDomisili)) {
                $alamatDomisili = $data['pemohon_alamat'] ?? null;
            }

            if (filled($alamatDomisili)) {
                $updates['alamat_domisili'] = $alamatDomisili;
            }
        }

        if (blank($penduduk->no_hp) && filled($data['pemohon_telepon'] ?? null)) {
            $updates['no_hp'] = $data['pemohon_telepon'];
        }

        if (blank($penduduk->korong) && filled($data['korong'] ?? null)) {
            $updates['korong'] = $data['korong'];
        }

        if ($updates !== []) {
            $penduduk->fill($updates);
            $penduduk->save();
            $penduduk->refresh();
        }

        if (blank($data['pemohon_nama'] ?? null) && filled($penduduk->name)) {
            $data['pemohon_nama'] = $penduduk->name;
        }

        if (blank($data['pemohon_alamat'] ?? null) && filled($penduduk->alamat)) {
            $data['pemohon_alamat'] = $penduduk->alamat;
        }

        if (blank($data['pemohon_alamat_domisili'] ?? null)) {
            $data['pemohon_alamat_domisili'] = $penduduk->alamat_domisili ?: ($data['pemohon_alamat'] ?? null);
        }

        if (blank($data['pemohon_telepon'] ?? null) && filled($penduduk->no_hp)) {
            $data['pemohon_telepon'] = $penduduk->no_hp;
        }

        if (blank($data['korong'] ?? null) && filled($penduduk->korong)) {
            $data['korong'] = $penduduk->korong;
        }
    }
}
