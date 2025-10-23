<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaJenisSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all meta as array for KeyValue default
     */
    public static function getMetaArray(): array
    {
        return static::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->pluck('description', 'name')
            ->toArray();
    }

    /**
     * Sync meta data from array
     */
    public static function syncMeta(array $metaData): void
    {
        foreach ($metaData as $name => $description) {
            static::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'category' => static::detectCategory($name),
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Detect category from name
     */
    private static function detectCategory(string $name): string
    {
        $name = strtolower($name);

        if (str_contains($name, 'form_')) {
            return 'form';
        } elseif (str_contains($name, 'pejabat') || str_contains($name, 'jabatan') || str_contains($name, 'nip')) {
            return 'pejabat';
        } elseif (str_contains($name, 'desa') || str_contains($name, 'kecamatan') || str_contains($name, 'kabupaten')) {
            return 'nagari';
        } elseif (str_contains($name, 'nomor') || str_contains($name, 'tanggal') || str_contains($name, 'keperluan')) {
            return 'surat';
        } else {
            return 'pemohon';
        }
    }
}
