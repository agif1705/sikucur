<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\MetaJenisSurat;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class MetaJenisSuratPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Meta Template Surat';
    protected static ?string $title = 'Meta Template Surat';
    protected static string $view = 'filament.pages.meta-jenis-surat-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'meta' => MetaJenisSurat::getMetaArray()
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📋 Meta Data Template Surat')
                    ->description('Kelola placeholder yang dapat digunakan dalam template surat. Tambah, edit, atau hapus placeholder sesuai kebutuhan.')
                    ->schema([
                        KeyValue::make('meta')
                            ->label('Daftar Placeholder')
                            ->keyLabel('Placeholder (contoh: [NAma])')
                            ->valueLabel('Deskripsi')
                            ->reorderable()
                            ->addActionLabel('➕ Tambah Placeholder')
                            ->columnSpanFull()
                            ->helperText('💡 Gunakan format [NamaPlaceholder] untuk placeholder. Contoh: [NAma], [Nik], [Alamat_lengkaP]'),
                    ]),

                Section::make('🎨 Contoh Penggunaan')
                    ->description('Berikut contoh cara menggunakan placeholder dalam template surat')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('example')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-4">
                                    <div class=" p-4 rounded border ">
                                        <h4 class="font-semibold text-sm mb-2">📄 Contoh Template:</h4>
                                        <pre class="text-xs p-3 rounded border overflow-x-auto"><code>Yang bertanda tangan di bawah ini [JaBatan] [NaMa_desa],
dengan ini menerangkan bahwa:

Nama        : [NAma]
NIK         : [Nik]
Alamat      : [Alamat_lengkaP]
Keperluan   : [Form_keperluan]

Demikian surat ini dibuat untuk digunakan sebagaimana mestinya.

[NaMa_desa], [TgL_surat]
[JaBatan]

[Nama_pejabat]
NIP. [Nip_pejabat]</code></pre>
                                    </div>

                                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                                        <h4 class="font-semibold text-sm text-blue-800 mb-2">💡 Tips Penggunaan:</h4>
                                        <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                                            <li>Placeholder harus diapit dengan kurung siku <code class="bg-blue-100 px-1 rounded">[...]</code></li>
                                            <li>Gunakan huruf besar/kecil yang konsisten</li>
                                            <li>Untuk form dinamis, awali dengan <code class="bg-blue-100 px-1 rounded">[form_]</code></li>
                                            <li>Placeholder akan otomatis diganti saat generate surat</li>
                                        </ul>
                                    </div>
                                </div>
                            ')),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Save meta data
     */
    public function save(): void
    {
        try {
            $data = $this->form->getState();

            if (empty($data['meta'])) {
                Notification::make()
                    ->title('⚠️ Peringatan')
                    ->body('Tidak ada data meta untuk disimpan.')
                    ->warning()
                    ->send();
                return;
            }

            // Sync meta data to database
            MetaJenisSurat::syncMeta($data['meta']);

            Notification::make()
                ->title('✅ Berhasil!')
                ->body('Meta template surat berhasil disimpan.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error')
                ->body('Gagal menyimpan meta: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Reset to default meta
     */
    public function resetToDefault(): void
    {
        $defaultMeta = [
            // Data Pemohon
            '[NAma]' => 'Nama lengkap pemohon',
            '[Nik]' => 'NIK pemohon',
            '[Alamat_lengkaP]' => 'Alamat lengkap pemohon',
            '[JK]' => 'Jenis kelamin pemohon',
            '[Tempat_lahir]' => 'Tempat lahir pemohon',
            '[Tanggal_lahir]' => 'Tanggal lahir pemohon',
            '[Telepon]' => 'Nomor telepon pemohon',

            // Data Surat
            '[Nomor_surat]' => 'Nomor surat lengkap',
            '[TgL_surat]' => 'Tanggal pembuatan surat',
            '[Form_keperluan]' => 'Keperluan/tujuan pembuatan surat',

            // Data Nagari
            '[NaMa_desa]' => 'Nama nagari/desa',
            '[Kecamatan]' => 'Nama kecamatan',
            '[Kabupaten]' => 'Nama kabupaten',
            '[Provinsi]' => 'Nama provinsi',

            // Data Pejabat
            '[JaBatan]' => 'Jabatan penandatangan surat',
            '[Nama_pejabat]' => 'Nama penandatangan surat',
            '[Nip_pejabat]' => 'NIP penandatangan surat',
        ];

        MetaJenisSurat::syncMeta($defaultMeta);

        $this->form->fill([
            'meta' => MetaJenisSurat::getMetaArray()
        ]);

        Notification::make()
            ->title('✅ Berhasil!')
            ->body('Meta telah direset ke default.')
            ->success()
            ->send();
    }

    /**
     * Header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset')
                ->label('Reset Default')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset ke Default?')
                ->modalDescription('Ini akan mengembalikan meta template ke nilai default. Meta custom akan tetap ada.')
                ->action('resetToDefault'),

            Action::make('save')
                ->label('💾 Simpan Meta')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('save'),
        ];
    }
}
