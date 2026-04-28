<?php

namespace App\Filament\Resources\PermohonanSurats;

use App\Filament\Resources\PermohonanSurats\Schemas\PermohonanSuratForm;
use App\Filament\Resources\PermohonanSurats\Tables\PermohonanSuratsTable;
use App\Models\JenisSurat;
use App\Models\PermohonanSurat;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PermohonanSuratResource extends Resource
{
    protected static ?string $model = PermohonanSurat::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Permohonan Surat';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    protected static ?int $navigationSort = 1;

    public $kecamatan = 'V Koto';

    public static function canCreate(): bool
    {
        return static::canManagePelayanan();
    }

    public static function form(Schema $form): Schema
    {
        return PermohonanSuratForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PermohonanSuratsTable::configure($table);
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

    protected static function getPejabatOptions(): array
    {
        static $cache = null;
        if ($cache === null) {
            $cache = User::query()
                ->where('id', '<>', 1)
                ->with('jabatan')
                ->get()
                ->mapWithKeys(function ($user) {
                    $jabatan = $user->jabatan?->name ?? 'Tanpa Jabatan';

                    return [$user->id => "{$user->name} | {$jabatan}"];
                })
                ->all();
        }

        return $cache;
    }

    private static function canManagePelayanan(): bool
    {
        $user = Auth::user();

        return $user?->hasAnyRole(['super_admin', 'Kasi Pelayanan', 'Staf Pelayanan']) === true;
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

        // Extract semua placeholder dari template
        preg_match_all('/\[(.*?)\]/', $originalTemplate, $matches);
        $allPlaceholders = array_unique($matches[0]);

        // Cek placeholder yang belum tersedia di pemohonData atau formData
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
