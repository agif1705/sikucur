<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Nagari;
use Filament\Forms\Get;
use App\Models\Penduduk;
use Filament\Forms\Form;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use App\Models\StatusSurat;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use App\Models\PermohonanSurat;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use FilamentTiptapEditor\Enums\TiptapOutput;
use App\Filament\Resources\PermohonanSuratResource\Pages;

class PermohonanSuratResource extends Resource
{
    protected static ?string $model = PermohonanSurat::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Permohonan Surat';
    protected static ?string $navigationGroup = 'Manajemen Surat';
    protected static ?int $navigationSort = 1;
    public $kecamatan = "V Koto";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Penduduk & Jenis Surat')
                        ->schema([
                            Forms\Components\Select::make('selected_penduduk')
                                ->label('Pilih Penduduk')
                                ->searchable()
                                ->preload(false)
                                ->placeholder('Ketik minimal 3 karakter untuk mencari...')
                                ->options(fn() => [])
                                ->getSearchResultsUsing(function (string $search) {
                                    if (strlen($search) < 3) {
                                        return [];
                                    }
                                    return \App\Models\Penduduk::query()
                                        ->where('nik', '<>', '')
                                        ->where(function ($query) use ($search) {
                                            $query->where('nik', 'like', "%{$search}%")
                                                ->orWhere('name', 'ilike', "%{$search}%")
                                                ->orWhere('alamat', 'ilike', "%{$search}%");
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
                                    if (!$state) return;

                                    $penduduk = \App\Models\Penduduk::find($state);
                                    if ($penduduk) {
                                        // SIMPAN PENDUDUK_ID untuk relasi
                                        $set('penduduk_id', $penduduk->id);

                                        // Auto isi field lain
                                        $set('pemohon_nik', $penduduk->nik);
                                        $set('pemohon_nama', $penduduk->name);
                                        $set('pemohon_alamat', $penduduk->alamat);
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

                            // Hidden fields untuk menyimpan data dynamic
                            Forms\Components\Hidden::make('dynamic_fields_data')
                                ->dehydrated(false),

                            Forms\Components\Hidden::make('form_data')
                                ->dehydrated(true)
                                ->default(function ($get) {
                                    return [];
                                }),
                        ])->columns(2),
                    Wizard\Step::make('tanda Tangan Berkas')
                        ->schema([
                            Forms\Components\Select::make('TandaTangan')
                                ->label('Yang Bertanda Tangan')
                                ->searchable()
                                ->options(
                                    \App\Models\User::all()->except(1)->mapWithKeys(function ($user) {
                                        return [
                                            $user->id => "{$user->name} | {$user->jabatan->name}",
                                        ];
                                    })
                                )->afterStateUpdated(function ($state, callable $set, $get) {
                                    if (!$state) {
                                        // Kosongkan nilai pejabat dan refresh preview
                                        $set('PejabatTandaTangan_nama', null);
                                        $set('PejabatTandaTangan_jabatan', null);
                                        static::updateTemplateWithFormData('', $set, $get);
                                        return;
                                    }

                                    $PejabatTandaTangan = \App\Models\User::find($state);
                                    if ($PejabatTandaTangan) {
                                        // Auto isi field pejabat penanda tangan
                                        $set('PejabatTandaTangan_nama', $PejabatTandaTangan->name);
                                        $set('PejabatTandaTangan_jabatan', $PejabatTandaTangan->jabatan->name);
                                    }

                                    // Paksa refresh template preview ketika tanda tangan berubah
                                    static::updateTemplateWithFormData('', $set, $get);
                                })
                                ->reactive(),
                        ]),
                    Wizard\Step::make('Isian Surat & Template')
                        ->schema([
                            Section::make('Form Isian Tambahan')
                                ->schema([
                                    Forms\Components\Placeholder::make('dynamic_form_info')
                                        ->label('')
                                        ->content('Isian tambahan akan muncul setelah memilih jenis surat')
                                        ->visible(fn($get) => empty($get('dynamic_fields_data'))),

                                    // Container untuk dynamic fields
                                    Forms\Components\Group::make()
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
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                // Update template setiap field berubah
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
                                                            ->live()
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
                                                            ->live()
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
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'date':
                                                        $components[] = Forms\Components\DatePicker::make($fieldKey)
                                                            ->label($field['nama'])
                                                            ->required($field['required'] == '1')
                                                            ->helperText("Kode: {$field['kode']} - {$field['deskripsi']}")
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                                static::updateTemplateWithFormData('', $set, $get);
                                                            });
                                                        break;

                                                    case 'select':
                                                        // Asumsi options ada di field atribut
                                                        $options = [];
                                                        if (!empty($field['atribut'])) {
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
                                                            });
                                                        break;
                                                }
                                            }

                                            return $components;
                                        })
                                        ->visible(fn($get) => !empty($get('dynamic_fields_data')))
                                        ->columns(2),
                                ])
                                ->visible(fn($get) => $get('jenis_surat_id')),
                        ]),
                    Wizard\Step::make('Preview')
                        ->schema([
                            Section::make('Template Surat')
                                ->schema([
                                    TiptapEditor::make('pemohon_template')
                                        ->label('Template Surat')
                                        ->columnSpanFull()
                                        ->output(TiptapOutput::Html)
                                        ->profile('full')
                                        ->tools([
                                            'heading',
                                            'bullet-list',
                                            'ordered-list',
                                            'bold',
                                            'italic',
                                            'strike',
                                            'underline',
                                            'align-left',
                                            'align-center',
                                            'align-right',
                                            'align-justify',
                                            'table',
                                            'source',
                                        ])
                                        ->placeholder('Template akan muncul otomatis ketika jenis surat dipilih...')
                                        ->helperText('Template otomatis terisi dari jenis surat. Data akan terupdate saat form diisi.')
                                        ->disabled(fn($get) => !$get('jenis_surat_id'))
                                        ->visible(fn($get) => $get('jenis_surat_id')) // Hanya muncul jika jenis surat dipilih
                                        ->dehydrated(true)
                                        ->reactive(), // Tambahkan reactive
                                ])
                                ->visible(fn($get) => $get('jenis_surat_id')),
                        ]),
                    Wizard\Step::make('Selesai')
                        ->schema([
                            // ...
                        ]),
                ])->columnSpanFull(),


                // Section::make('Detail Permohonan')
                //     ->schema([
                //         Forms\Components\Textarea::make('keperluan')
                //             ->label('Keperluan/Tujuan')
                //             ->required()
                //             ->rows(3)
                //             ->placeholder('Jelaskan untuk keperluan apa surat ini digunakan'),

                //         Grid::make(2)
                //             ->schema([
                //                 Forms\Components\Select::make('status_id')
                //                     ->label('Status')
                //                     ->relationship('status', 'nama_status')
                //                     ->default(1), // Default status pertama

                //                 Forms\Components\Select::make('petugas_id')
                //                     ->label('Petugas')
                //                     ->relationship('petugas', 'name')
                //                     ->searchable()
                //                     ->preload()
                //                     ->placeholder('Pilih petugas yang menangani'),
                //             ]),

                //         Forms\Components\DateTimePicker::make('tanggal_estimasi_selesai')
                //             ->label('Estimasi Selesai')
                //             ->native(false),

                //         Forms\Components\Textarea::make('catatan_petugas')
                //             ->label('Catatan Petugas')
                //             ->rows(3)
                //             ->placeholder('Catatan dari petugas'),
                //     ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor permohonan disalin!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('jenisSurat.nama_jenis')
                    ->label('Jenis Surat')
                    ->searchable()
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('pemohon_nama')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('status.nama_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(PermohonanSurat $record) => $record->status->warna_status)
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->label('Tgl Permohonan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_estimasi_selesai')
                    ->label('Estimasi Selesai')
                    ->date('d M Y')
                    ->color(function (PermohonanSurat $record) {
                        if (!$record->tanggal_estimasi_selesai) return 'gray';
                        return $record->tanggal_estimasi_selesai->isPast() ? 'danger' : 'success';
                    }),

                Tables\Columns\TextColumn::make('petugas.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->limit(20),

                Tables\Columns\TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_surat_id')
                    ->label('Jenis Surat')
                    ->relationship('jenisSurat', 'nama_jenis')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'nama_status')
                    ->preload(),

                Tables\Filters\SelectFilter::make('nagari_id')
                    ->label('Nagari')
                    ->relationship('nagari', 'name')
                    ->preload(),

                Tables\Filters\Filter::make('tanggal_permohonan')
                    ->label('Periode Permohonan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status_id')
                            ->label('Status Baru')
                            ->options(StatusSurat::pluck('nama_status', 'id'))
                            ->required(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (PermohonanSurat $record, array $data): void {
                        $record->update([
                            'status_id' => $data['status_id'],
                            'catatan_petugas' => $data['catatan'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_permohonan', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanSurats::route('/'),
            'create' => Pages\CreatePermohonanSurat::route('/create'),
            // 'view' => Pages\ViewPermohonanSurat::route('/{record}'),
            'edit' => Pages\EditPermohonanSurat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('status', function ($query) {
            $query->where('kode_status', 'MASUK');
        })->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    protected static function updateTemplateWithFormData($template, $set, $get)
    {
        $formData = $get('form_data') ?: [];
        $jenisSuratId = $get('jenis_surat_id');

        if (!$jenisSuratId) return $template;

        $jenisSurat = JenisSurat::find($jenisSuratId);
        if (!$jenisSurat || !$jenisSurat->template) return $template;

        $originalTemplate = $jenisSurat->template;

        // Data pemohon yang sudah tersedia di form utama
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
            '[JUDUL_SURAT]' => $get('pemohon_judul_surat') ? Str::upper($get('pemohon_judul_surat')) : null,
            '[FORMAT_NOMOR_SURAT]' => \App\Models\PermohonanSurat::getNomorSuratLengkapAttribute(
                $get('jenis_surat_id')
            ),
        ];

        // Extract semua placeholder dari template
        preg_match_all('/\[(.*?)\]/', $originalTemplate, $matches);
        $allPlaceholders = array_unique($matches[0]);

        // Cek placeholder yang belum tersedia di pemohonData atau formData
        $missingPlaceholders = [];
        $existingDynamicFields = $get('dynamic_fields_data') ?: [];
        $existingFieldCodes = array_column($existingDynamicFields, 'kode');

        foreach ($allPlaceholders as $ph) {
            if (array_key_exists($ph, $pemohonData)) continue;
            if (isset($formData[$ph])) continue;
            if (in_array($ph, $existingFieldCodes, true)) continue;
            $missingPlaceholders[] = $ph;
        }

        // Generate dynamic fields untuk missing placeholders
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

        // Replace dengan data pemohon (hanya yang ada valuenya)
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
