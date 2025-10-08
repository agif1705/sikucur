<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratKepala;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratKepalaResource\Pages;
use App\Filament\Resources\SuratKepalaResource\RelationManagers;

class SuratKepalaResource extends Resource
{
    protected static ?string $model = SuratKepala::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data Surat';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nagari_id')
                    ->label('Nagari')
                    ->relationship('nagari', 'name')
                    ->required(),
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo Nagari')
                    ->image()
                    ->directory('copSurat')
                    ->maxSize(1024)
                    ->required()
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        // Contoh: nama file = timestamp_namaasli.ext
                        return "Logo-kop-surat" . time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    }),
                Forms\Components\RichEditor::make('kop_surat')
                    ->label('Kop Surat (Header)')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratKepalas::route('/'),
            'create' => Pages\CreateSuratKepala::route('/create'),
            'edit' => Pages\EditSuratKepala::route('/{record}/edit'),
        ];
    }
}
