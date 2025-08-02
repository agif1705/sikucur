<?php

namespace App\Filament\Pages;

use App\Models\Coments;
use Filament\Tables;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class ComentsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Pengaturan Pegawai';
    protected static string $view = 'filament.pages.coments-page';
    protected static ?string $navigationLabel = 'Kritik dan Saran';
    protected static ?string $title = 'Kritik dan Saran Masyarakat';

    public function mount(): void {}
    public function table(Table $table): Table
    {
        return $table
            ->query(Coments::query()->with('jabatan')->latest())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('coment')
                    ->label('Kritis dan Saran')
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('jabatan.name')
                    ->label('Tujuan'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),

            ])
            ->paginated(15)
            ->striped()
            ->defaultSort('created_at', 'desc')

            ->filters([
                // Filter bulan dan tahun
            ])
            ->actions([
                // Actions jika diperlukan

            ])
            ->bulkActions([
                // Bulk actions jika diperlukan
            ]);
    }
}
