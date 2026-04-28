<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
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
        $data['status'] = $data['status'] ?? SuratPengantar::STATUS_SUBMITTED;
        $data['used'] = true;
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

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->status === SuratPengantar::STATUS_SUBMITTED) {
            app(SuratPengantarNotificationService::class)->notifyPengantarSubmitted($this->record);
        }
    }
}
