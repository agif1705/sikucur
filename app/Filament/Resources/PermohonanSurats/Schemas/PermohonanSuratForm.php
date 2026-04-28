<?php

namespace App\Filament\Resources\PermohonanSurats\Schemas;

use App\Models\JenisSurat;
use App\Models\Penduduk;
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

class PermohonanSuratForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Penduduk & Jenis Surat')
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
                                        ->orderByDesc('updated_at')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (SuratPengantar $pengantar) {
                                            $label = sprintf(
                                                '%s | %s | %s',
                                                $pengantar->pemohon_nama ?? '-',
                                                $pengantar->korong ?? '-',
                                                optional($pengantar->tanggal_pengantar)->format('d-m-Y')
                                            );

                                            return [$pengantar->id => $label];
                                        })
                                        ->all();
                                })
                                ->getOptionLabelUsing(function ($value): ?string {
                                    $pengantar = SuratPengantar::find($value);
                                    if (! $pengantar) {
                                        return null;
                                    }

                                    return sprintf(
                                        '%s | %s | %s',
                                        $pengantar->pemohon_nama ?? '-',
                                        $pengantar->korong ?? '-',
                                        optional($pengantar->tanggal_pengantar)->format('d-m-Y')
                                    );
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $pengantar = SuratPengantar::with('penduduk')->find($state);
                                    if (! $pengantar) {
                                        return;
                                    }

                                    $set('penduduk_id', $pengantar->penduduk_id);
                                    $set('selected_penduduk', $pengantar->penduduk_id);
                                    $set('pemohon_nik', $pengantar->pemohon_nik);
                                    $set('pemohon_nama', $pengantar->pemohon_nama);
                                    $set('pemohon_alamat', $pengantar->pemohon_alamat);
                                    $set('pemohon_alamat_domisili', $pengantar->pemohon_alamat_domisili);
                                    $set('pemohon_telepon', $pengantar->pemohon_telepon);
                                    $set('keperluan', $pengantar->keperluan);
                                })
                                ->helperText('Surat pengantar wajib ada sebelum membuat surat'),

                            Forms\Components\Select::make('selected_penduduk')
                                ->label('Pilih Penduduk')
                                ->searchable()
                                ->preload(false)
                                ->placeholder('Ketik minimal 3 karakter untuk mencari...')
                                ->options(fn () => [])
                                ->getSearchResultsUsing(function (string $search) {
                                    if (strlen($search) < 3) {
                                        return [];
                                    }

                                    return Penduduk::query()
                                        ->where('nik', '<>', '')
                                        ->where(function ($query) use ($search) {
                                            $query->where('nik', 'like', "%{$search}%")
                                                ->orWhere('name', 'ilike', "%{$search}%")
                                                ->orWhere('alamat', 'ilike', "%{$search}%")
                                                ->orWhere('alamat_domisili', 'ilike', "%{$search}%");
                                        })
                                        ->orderBy('name')
                                        ->limit(20)
                                        ->get()
                                        ->mapWithKeys(function ($penduduk) {
                                            return [
                                                $penduduk->id => sprintf(
                                                    '📋 %s | 👤 %s | 🗓️ %s',
                                                    $penduduk->nik,
                                                    $penduduk->name,
                                                    $penduduk->tanggal_lahir ?? 'Tgl lahir kosong'
                                                ),
                                            ];
                                        });
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $penduduk = Penduduk::find($state);
                                    if ($penduduk) {
                                        // SIMPAN PENDUDUK_ID untuk relasi
                                        $set('penduduk_id', $penduduk->id);

                                        // Auto isi field lain
                                        $set('pemohon_nik', $penduduk->nik);
                                        $set('pemohon_nama', $penduduk->name);
                                        $set('pemohon_alamat', $penduduk->alamat);
                                        $set('pemohon_alamat_domisili', $penduduk->alamat_domisili);
                                        $set('pemohon_telepon', $penduduk->no_hp);
                                        $set('pemohon_agama', $penduduk->agama);
                                        $set('pemohon_jk', $penduduk->jk);
                                        $set('pemohon_tempat_lahir', $penduduk->tempat_lahir);
                                        $set('pemohon_tanggal_lahir', $penduduk->tanggal_lahir);
                                    }
                                })
                                ->live()
                                ->dehydrated(false)
                                ->helperText('Ketik NIK, nama, atau alamat untuk mencari penduduk (minimal 3 karakter).'),
                            // Tambahkan Hidden field untuk simpan penduduk_id
                            Forms\Components\Hidden::make('penduduk_id')
                                ->dehydrated(true),

                            Forms\Components\Select::make('jenis_surat_id')
                                ->label('Pilih Jenis Surat')
                                ->relationship('jenisSurat', 'nama_jenis')
                                ->searchable()
                                ->required()
                                ->preload()
                                ->getOptionLabelFromRecordUsing(function ($record) {
                                    return sprintf(
                                        '%s - %s (%d hari)',
                                        $record->kode_surat,
                                        $record->nama_jenis,
                                        $record->estimasi_hari ?? 0
                                    );
                                })
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state) {
                                        $jenisSurat = JenisSurat::find($state);
                                        if ($jenisSurat) {
                                            // Auto-fill estimasi selesai
                                            if ($jenisSurat->estimasi_hari) {
                                                $estimasiSelesai = now()->addDays($jenisSurat->estimasi_hari);
                                                $set('tanggal_estimasi_selesai', $estimasiSelesai);
                                            }

                                            // Auto-fill template dari database
                                            if ($jenisSurat->template) {
                                                $set('pemohon_template', $jenisSurat->template);
                                                $set('pemohon_judul_surat', $jenisSurat->nama_jenis);
                                            }

                                            // Set dynamic fields data untuk reactivity

                                            // Reset form data untuk dynamic fields
                                            $set('form_data', []);

                                            // Force update template
                                            static::updateTemplateWithFormData($jenisSurat->template, $set, $get);
                                        }
                                    } else {
                                        // Clear data jika jenis surat dihapus
                                        $set('pemohon_template', '');
                                        $set('dynamic_fields_data', []);
                                        $set('form_data', []);
                                    }
                                })
                                ->live()
                                ->helperText('Pilih jenis surat yang akan diajukan'),

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
                                        ->content('Isian tambahan akan muncul setelah memilih jenis surat')
                                        ->visible(fn ($get) => empty($get('dynamic_fields_data'))),

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
                                        ->visible(fn ($get) => ! empty($get('dynamic_fields_data')))
                                        ->columns(2),
                                ])
                                ->visible(fn ($get) => $get('jenis_surat_id')),
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
                                        ->disabled(fn ($get) => ! $get('jenis_surat_id'))
                                        ->visible(fn ($get) => $get('jenis_surat_id'))
                                        ->dehydrated(true),
                                       ])
                                       ->visible(fn ($get) => $get('jenis_surat_id')),
                               ]),
                    Wizard\Step::make('Selesai')
                        ->schema([
                                   Section::make('�
        Ringkasan Permohonan')
                                       ->description('Pastikan data berikut sudah benar sebelum menyimpan')
                                       ->schema([
                                           Grid::make(2)
                                        ->schema([
                                            Forms\Components\Placeholder::make('summary_pemohon')
                                                ->label('Pemohon')
                                                ->content(fn ($get) => ($get('pemohon_nama') ?: '-').' ('.($get('pemohon_nik') ?: '-').')'),

                                            Forms\Components\Placeholder::make('summary_jenis_surat')
                                                ->label('Jenis Surat')
                                                ->content(fn ($get) => $get('pemohon_judul_surat') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_alamat')
                                                ->label('Alamat KTP')
                                                ->content(fn ($get) => $get('pemohon_alamat') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_alamat_domisili')
                                                ->label('Alamat Domisili')
                                                ->content(fn ($get) => $get('pemohon_alamat_domisili') ?: ($get('pemohon_alamat') ?: '-')),

                                            Forms\Components\Placeholder::make('summary_estimasi')
                                                ->label('Estimasi Selesai')
                                                ->content(fn ($get) => $get('tanggal_estimasi_selesai')
                                                    ? Carbon::parse($get('tanggal_estimasi_selesai'))->translatedFormat('d F Y')
                                                    : '-'),

                                            Forms\Components\Placeholder::make('summary_keperluan')
                                                ->label('Keperluan')
                                                ->content(fn ($get) => $get('keperluan') ?: '-'),

                                            Forms\Components\Placeholder::make('summary_pejabat')
                                                ->label('Pejabat Penanda Tangan')
                                                ->content(fn ($get) => $get('PejabatTandaTangan_nama')
                                                    ? $get('PejabatTandaTangan_nama').' ('.$get('PejabatTandaTangan_jabatan').')'
                                                    : '-'),
                                        ]),
                                       ]),
                                   // Hidden fields yang dibutuhkan database
                                   Forms\Components\Hidden::make('nagari_id')
                                       ->default(fn () => Auth::user()?->nagari_id),

                                   Forms\Components\Hidden::make('status_id')
                                       ->default(fn () => StatusSurat::where('kode_status', 'MASUK')->value('id')),

                                   Forms\Components\Hidden::make('tanggal_permohonan')
                                       ->default(fn () => now()->toDateTimeString()),
                               ]),
                ])->columnSpanFull(),

            ]);

    }
}
