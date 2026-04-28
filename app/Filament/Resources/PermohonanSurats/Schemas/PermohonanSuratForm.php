<?php

namespace App\Filament\Resources\PermohonanSurats\Schemas;

use App\Models\JenisSurat;
use App\Models\PermohonanSurat;
use App\Models\StatusSurat;
use App\Models\SuratPengantar;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PermohonanSuratForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Penduduk & Surat Pengantar')
                        ->schema([
                            Forms\Components\Select::make('surat_pengantar_id')
                                ->label('Surat Pengantar Wali Korong')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(function () {
                                    return SuratPengantar::query()
                                        ->where('status', SuratPengantar::STATUS_SUBMITTED)
                                        ->whereDoesntHave('permohonanSurat')
                                        ->with('jenisSurat:id,nama_jenis')
                                        ->orderByDesc('updated_at')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (SuratPengantar $pengantar) {
                                            $label = sprintf(
                                                '%s | NIK: %s | Surat: %s',
                                                $pengantar->pemohon_nama ?? '-',
                                                $pengantar->pemohon_nik ?? '-',
                                                $pengantar->jenisSurat?->nama_jenis ?? '-'
                                            );

                                            return [$pengantar->id => $label];
                                        })
                                        ->all();
                                })
                                ->getOptionLabelUsing(function ($value): ?string {
                                    $pengantar = SuratPengantar::with('jenisSurat:id,nama_jenis')->find($value);
                                    if (! $pengantar) {
                                        return null;
                                    }

                                    return sprintf(
                                        '%s | NIK: %s | Surat: %s',
                                        $pengantar->pemohon_nama ?? '-',
                                        $pengantar->pemohon_nik ?? '-',
                                        $pengantar->jenisSurat?->nama_jenis ?? '-'
                                    );
                                })
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    if (! $state) {
                                        return;
                                    }

                                    $pengantar = SuratPengantar::with(['penduduk', 'jenisSurat'])->find($state);
                                    if (! $pengantar) {
                                        return;
                                    }

                                    $set('penduduk_id', $pengantar->penduduk_id);
                                    $set('pemohon_nik', $pengantar->pemohon_nik);
                                    $set('pemohon_nama', $pengantar->pemohon_nama);
                                    $set('pemohon_alamat', $pengantar->pemohon_alamat);
                                    $set('pemohon_alamat_domisili', $pengantar->pemohon_alamat_domisili);
                                    $set('pemohon_telepon', $pengantar->pemohon_telepon);
                                    $set('keperluan', $pengantar->keperluan);

                                    $set('jenis_surat_id', $pengantar->jenis_surat_id);

                                    if (! $pengantar->jenisSurat) {
                                        $set('pemohon_template', '');
                                        $set('pemohon_judul_surat', null);
                                        $set('tanggal_estimasi_selesai', null);
                                        $set('dynamic_fields_data', []);
                                        $set('form_data', []);

                                        return;
                                    }

                                    if ($pengantar->jenisSurat->estimasi_hari) {
                                        $set('tanggal_estimasi_selesai', now()->addDays($pengantar->jenisSurat->estimasi_hari));
                                    } else {
                                        $set('tanggal_estimasi_selesai', null);
                                    }

                                    $set('pemohon_judul_surat', $pengantar->jenisSurat->nama_jenis);
                                    $set('pemohon_template', $pengantar->jenisSurat->template ?: '');
                                    $set('form_data', []);
                                    static::updateTemplateWithFormData($pengantar->jenisSurat->template ?: '', $set, $get);
                                })
                                ->helperText('Surat pengantar wajib ada sebelum membuat surat'),
                            // Tambahkan Hidden field untuk simpan penduduk_id
                            Forms\Components\Hidden::make('penduduk_id')
                                ->dehydrated(true),

                            Forms\Components\Hidden::make('jenis_surat_id')
                                ->dehydrated(true),

                            Forms\Components\Select::make('TandaTangan')
                                ->label('Yang Bertanda Tangan')
                                ->searchable()
                                ->options(static::getPejabatOptions())
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    if (! $state) {
                                        $set('PejabatTandaTangan_nama', null);
                                        $set('PejabatTandaTangan_jabatan', null);
                                        static::updateTemplateWithFormData('', $set, $get);

                                        return;
                                    }

                                    $pejabat = User::with('jabatan')->find($state);
                                    if ($pejabat) {
                                        $set('PejabatTandaTangan_nama', $pejabat->name);
                                        $set('PejabatTandaTangan_jabatan', $pejabat->jabatan?->name ?? '');
                                    }

                                    static::updateTemplateWithFormData('', $set, $get);
                                })
                                ->live(),

                            // Hidden fields pejabat (diisi via afterStateUpdated)
                            Forms\Components\Hidden::make('PejabatTandaTangan_nama'),
                            Forms\Components\Hidden::make('PejabatTandaTangan_jabatan'),
                            // Hidden fields untuk menyimpan data dynamic
                            Forms\Components\Hidden::make('dynamic_fields_data')
                                ->dehydrated(false),

                            Forms\Components\Hidden::make('form_data')
                                ->dehydrated(true)
                                ->default([]),

                            // Hidden fields untuk data pemohon (diisi via afterStateUpdated)
                            Forms\Components\Hidden::make('pemohon_nik'),
                            Forms\Components\Hidden::make('pemohon_nama'),
                            Forms\Components\Hidden::make('pemohon_alamat'),
                            Forms\Components\Hidden::make('pemohon_alamat_domisili'),
                            Forms\Components\Hidden::make('pemohon_telepon'),
                            Forms\Components\Hidden::make('pemohon_agama'),
                            Forms\Components\Hidden::make('pemohon_jk'),
                            Forms\Components\Hidden::make('pemohon_tempat_lahir'),
                            Forms\Components\Hidden::make('pemohon_tanggal_lahir'),
                            Forms\Components\Hidden::make('pemohon_judul_surat'),
                            Forms\Components\Hidden::make('tanggal_estimasi_selesai'),
                            Forms\Components\Hidden::make('keperluan'),
                        ])->columns(2),

                    Wizard\Step::make('Isian Surat & Template')
                        ->schema([
                            Section::make('Form Isian Tambahan')
                                ->schema([
                                    Forms\Components\Placeholder::make('dynamic_form_info')
                                        ->label('')
                                        ->content('Isian tambahan akan muncul setelah memilih surat pengantar')
                                        ->visible(fn($get) => empty($get('dynamic_fields_data'))),

                                    // Container untuk dynamic fields
                                    Group::make()
                                        ->schema(function ($get) {
                                            $dynamicFields = $get('dynamic_fields_data') ?: [];
                                            $components = [];

                                            foreach ($dynamicFields as $index => $field) {
                                                $fieldKey = "form_data.{$field['kode']}";

                                                switch ($field['tipe']) {
                                                    case 'text':
                                                        $components[] = Forms\Components\TextInput::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->placeholder($field['deskripsi'])
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']}")
                                                            ->lazy()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'textarea':
                                                        $components[] = Forms\Components\Textarea::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->placeholder($field['deskripsi'])
                                                            ->required($field['required'] == '1')
                                                            ->rows(3)
                                                            ->helperText("Kode: {$field['kode']}")
                                                            ->lazy()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'number':
                                                        $components[] = Forms\Components\TextInput::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->placeholder($field['deskripsi'])
                                                            ->numeric()
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']}")
                                                            ->lazy()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'email':
                                                        $components[] = Forms\Components\TextInput::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->placeholder($field['deskripsi'])
                                                            ->email()
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']}")
                                                            ->lazy()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'date':
                                                        $components[] = Forms\Components\DatePicker::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']} - {$field['deskripsi']}")
                                                            ->lazy()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'select':
                                                        // Asumsi options ada di field atribut
                                                        $options = [];
                                                        if (! empty($field['atribut'])) {
                                                            $optionsData = json_decode($field['atribut'], true);
                                                            if (is_array($optionsData)) {
                                                                $options = $optionsData;
                                                            }
                                                        }

                                                        $components[] = Forms\Components\Select::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->options($options)
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']} - {$field['deskripsi']}")
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            }); // select tetap live karena tidak ada lazy untuk select
                                                        break;
                                                }
                                            }

                                            return $components;
                                        })
                                        ->visible(fn($get) => ! empty($get('dynamic_fields_data')))
                                        ->columns(2),
                                ])
                                ->visible(fn($get) => $get('jenis_surat_id')),
                        ]),
                    Wizard\Step::make('Preview')
                        ->schema([
                            Section::make('Template Surat')
                                ->schema([
                                    RichEditor::make('pemohon_template')
                                        ->label('Template Surat')
                                        ->columnSpanFull()
                                        ->placeholder('Template akan muncul otomatis ketika jenis surat dipilih...')
                                        ->helperText('Template otomatis terisi dari jenis surat. Data akan terupdate saat form diisi.')
                                        ->disabled(fn($get) => ! $get('jenis_surat_id'))
                                        ->visible(fn($get) => $get('jenis_surat_id'))
                                        ->dehydrated(true),
                                ])
                                ->visible(fn($get) => $get('jenis_surat_id')),
                        ]),
                    Wizard\Step::make('Selesai')
                        ->schema([
                            Section::make('Ringkasan Permohonan')
                                ->description('Pastikan data berikut sudah benar sebelum menyimpan')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Forms\Components\Placeholder::make('summary_pemohon')
                                                ->label('Pemohon')
                                                ->content(fn($get) => ($get('pemohon_nama') ?: '-') . ' (' . ($get('pemohon_nik') ?: '-') . ')'),

                                            Forms\Components\Placeholder::make('summary_jenis_surat')
                                                ->label('Jenis Surat')
                                                ->content(fn($get) => $get('pemohon_judul_surat') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_alamat')
                                                ->label('Alamat KTP')
                                                ->content(fn($get) => $get('pemohon_alamat') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_alamat_domisili')
                                                ->label('Alamat Domisili')
                                                ->content(fn($get) => $get('pemohon_alamat_domisili') ?: ($get('pemohon_alamat') ?: '-')),

                                            Forms\Components\Placeholder::make('summary_estimasi')
                                                ->label('Estimasi Selesai')
                                                ->content(fn($get) => $get('tanggal_estimasi_selesai')
                                                    ? Carbon::parse($get('tanggal_estimasi_selesai'))->translatedFormat('d F Y')
                                                    : '-'),

                                            Forms\Components\Placeholder::make('summary_keperluan')
                                                ->label('Keperluan')
                                                ->content(fn($get) => $get('keperluan') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_pejabat')
                                                ->label('Pejabat Penanda Tangan')
                                                ->content(fn($get) => $get('PejabatTandaTangan_nama')
                                                    ? $get('PejabatTandaTangan_nama') . ' (' . $get('PejabatTandaTangan_jabatan') . ')'
                                                    : '-'),
                                        ]),
                                ]),
                            // Hidden fields yang dibutuhkan database
                            Forms\Components\Hidden::make('nagari_id')
                                ->default(fn() => Auth::user()?->nagari_id),

                            Forms\Components\Hidden::make('status_id')
                                ->default(fn() => StatusSurat::where('kode_status', 'MASUK')->value('id')),

                            Forms\Components\Hidden::make('tanggal_permohonan')
                                ->default(fn() => now()->toDateTimeString()),
                        ]),
                ])->columnSpanFull(),

            ]);
    }

    protected static function getPejabatOptions(): array
    {
        static $cache = null;

        if ($cache === null) {
            $cache = User::query()
                ->where('id', '<>', 1)
                ->with('jabatan')
                ->get()
                ->mapWithKeys(function (User $user) {
                    $jabatan = $user->jabatan?->name ?? 'Tanpa Jabatan';

                    return [$user->id => "{$user->name} | {$jabatan}"];
                })
                ->all();
        }

        return $cache;
    }

    protected static function updateTemplateWithFormData($template, $set, $get)
    {
        $formData = $get('form_data') ?: [];
        $jenisSuratId = $get('jenis_surat_id');

        if (! $jenisSuratId) {
            return $template;
        }

        $jenisSurat = JenisSurat::find($jenisSuratId);
        if (! $jenisSurat || ! $jenisSurat->template) {
            return $template;
        }

        $originalTemplate = $jenisSurat->template;

        $pemohonData = [
            '[SEBUTAN_KABUPATEN]' => 'Kabupaten',
            '[NAMA_KABUPATEN]' => 'Padang Pariaman',
            '[NAMA_PROVINSI]' => 'Sumatera Barat',
            '[NAMA]' => $get('pemohon_nama') ?: null,
            '[TEMPAT_LAHIR]' => $get('pemohon_tempat_lahir') ?: null,
            '[TANGGAL_LAHIR]' => $get('pemohon_tanggal_lahir') ?: null,
            '[NIK]' => $get('pemohon_nik') ?: null,
            '[JK]' => $get('pemohon_jk') !== null
                ? ($get('pemohon_jk') ? 'Laki-laki' : 'Perempuan')
                : null,
            '[ALAMAT_LENGKAP]' => $get('pemohon_alamat') ?: null,
            '[ALAMAT_DOMISILI]' => $get('pemohon_alamat_domisili') ?: ($get('pemohon_alamat') ?: null),
            '[ALAMAT_LENGKAP_DOMISILI]' => $get('pemohon_alamat_domisili') ?: ($get('pemohon_alamat') ?: null),
            '[JABATAN]' => 'Wali Nagari',
            '[NAMA_DESA]' => 'Sikucur',
            '[NAMA_PEJABAT]' => $get('PejabatTandaTangan_nama') ?: null,
            '[ATAS_NAMA]' => function ($get) {
                $jab = $get('PejabatTandaTangan_jabatan') ?? '';

                return $jab === 'WaliNagari' ? 'Wali Nagari Sikucur' : 'An. Wali Nagari Sikucur';
            },
            '[ATAS_NAMA_JABATAN]' => function ($get) {
                $jab = $get('PejabatTandaTangan_jabatan') ?? '';

                return $jab === 'WaliNagari' ? ' ' : 'An. Wali Nagari Sikucur';
            },
            '[SEBUTAN_DESA]' => 'Nagari',
            '[TGL_SURAT]' => now()->format('d F Y'),
            '[NAMA_KECAMATAN]' => 'V Koto',
            '[KEPERLUAN]' => $get('keperluan') ?: null,
            '[KEPERLUAN_SURAT]' => $get('keperluan') ?: null,
            '[FORM_KEPERLUAN]' => $get('keperluan') ?: null,
            '[JUDUL_SURAT]' => $get('pemohon_judul_surat') ? Str::upper($get('pemohon_judul_surat')) : null,
            '[FORMAT_NOMOR_SURAT]' => PermohonanSurat::getNomorSuratLengkapAttribute(
                $get('jenis_surat_id')
            ),
        ];

        preg_match_all('/\[(.*?)\]/', $originalTemplate, $matches);
        $allPlaceholders = array_unique($matches[0]);

        $missingPlaceholders = [];
        $existingDynamicFields = $get('dynamic_fields_data') ?: [];
        $existingFieldCodes = array_column($existingDynamicFields, 'kode');

        foreach ($allPlaceholders as $ph) {
            if (array_key_exists($ph, $pemohonData)) {
                continue;
            }

            if (isset($formData[$ph])) {
                continue;
            }

            if (in_array($ph, $existingFieldCodes, true)) {
                continue;
            }

            $missingPlaceholders[] = $ph;
        }

        if ($missingPlaceholders) {
            $newDynamic = [];

            foreach ($missingPlaceholders as $ph) {
                $clean = trim($ph, '[]');
                $label = Str::title(str_replace(['FORM_', '_'], ['', ' '], strtolower($clean)));

                $newDynamic[] = [
                    'tipe' => 'text',
                    'kode' => $ph,
                    'nama' => $label,
                    'deskripsi' => "Masukkan {$label}",
                    'atribut' => '',
                    'required' => '0',
                ];
            }

            $set('dynamic_fields_data', array_merge($existingDynamicFields, $newDynamic));
        }

        foreach ($formData as $kode => $value) {
            if ($value !== null && $value !== '') {
                $originalTemplate = str_replace($kode, (string) $value, $originalTemplate);
            }
        }

        foreach ($pemohonData as $placeholder => $value) {
            $resolved = is_callable($value) ? $value($get) : $value;

            if ($resolved !== null && $resolved !== '') {
                $originalTemplate = str_replace($placeholder, (string) $resolved, $originalTemplate);
            }
        }

        $set('pemohon_template', $originalTemplate);

        return $originalTemplate;
    }
}
