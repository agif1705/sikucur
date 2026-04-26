<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class SuratSetting extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.surat-setting';

    public function mount(): void
    {
        // Halaman ini hanya menampilkan daftar template surat.
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(JenisSurat::query())
            ->columns([
                TextColumn::make('nama_jenis')->label('Nama Jenis Surat')->sortable()->searchable(),
                TextColumn::make('template_desa')->label('template')->sortable()->searchable()->wrap(),
            ]);
    }
}
