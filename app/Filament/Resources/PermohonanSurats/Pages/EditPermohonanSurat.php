<?php

namespace App\Filament\Resources\PermohonanSurats\Pages;

use App\Filament\Resources\PermohonanSurats\PermohonanSuratResource;
use App\Models\SuratPengantar;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPermohonanSurat extends EditRecord
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentSuratPengantarId = $this->record->surat_pengantar_id;

        if ($currentSuratPengantarId) {
            $incomingSuratPengantarId = $data['surat_pengantar_id'] ?? $currentSuratPengantarId;

            if ((int) $incomingSuratPengantarId !== (int) $currentSuratPengantarId) {
                throw ValidationException::withMessages([
                    'surat_pengantar_id' => 'Surat pengantar tidak boleh diganti. Buat permohonan baru jika membutuhkan surat pengantar lain.',
                ]);
            }

            $pengantar = SuratPengantar::find($currentSuratPengantarId);

            if ($pengantar?->jenis_surat_id) {
                $data['jenis_surat_id'] = $pengantar->jenis_surat_id;
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
