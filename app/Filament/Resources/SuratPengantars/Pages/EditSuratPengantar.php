<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
use App\Models\Penduduk;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratPengantar extends EditRecord
{
    protected static string $resource = SuratPengantarResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->syncPendudukFromData($data);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
