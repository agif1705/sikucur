<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TvInformasiResource\Pages;
use App\Models\TvInformasi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TvInformasiResource extends Resource
{
    protected static ?string $model = TvInformasi::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tv Informasi';

    protected static ?string $navigationLabel = 'Data Tv Informasi';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-c-tv';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Informasi')
                    ->description('Data Tv Informasi ini untuk di layar TV yang akan di tampilkan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Judul Video Youtube')
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('bupati')
                            ->label('Nama bupati Nagari')
                            ->required()->columnSpan(1),
                        Forms\Components\FileUpload::make('bupati_image')
                            ->label('Foto bupati Nagari')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\TextInput::make('wakil_bupati')
                            ->label('Nama wakil_bupati Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('wakil_bupati_image')
                            ->label('Foto wakil_bupati Nagari')
                            ->required(),
                        Forms\Components\TextInput::make('wali_nagari')
                            ->label('Nama wali_nagari Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('wali_nagari_image')
                            ->label('Foto wali_nagari Nagari')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\TextInput::make('bamus')
                            ->label('Nama Bamus Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('bamus_image')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->label('Foto Bamus Nagari')
                            ->required(),
                        Forms\Components\TextInput::make('babinsa')
                            ->label('Nama Babinsa Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('babinsa_image')
                            ->label('Foto Babinsa Nagari')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\RichEditor::make('running_text')
                            ->label('running_text')
                            ->required()->columns(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bamus')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('bamus_image')
                    ->square(),
                Tables\Columns\TextColumn::make('babinsa')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('babinsa_image')
                    ->square(),

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListTvInformasis::route('/'),
            'create' => Pages\CreateTvInformasi::route('/create'),
            'edit' => Pages\EditTvInformasi::route('/{record}/edit'),
        ];
    }
}
