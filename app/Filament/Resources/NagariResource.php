<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NagariResource\Pages;
use App\Filament\Resources\NagariResource\RelationManagers;
use App\Models\Nagari;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NagariResource extends Resource
{
    protected static ?string $model = Nagari::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Pengaturan Pegawai';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                section::make('Data Nagari ')->schema([

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('alamat')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('logo')
                        ->label('Logo')
                        ->image(),
                    Forms\Components\CheckboxList::make('working_days')
                        ->label('Pilih Hari Kerja')
                        ->options([
                            'monday' => 'Senin',
                            'tuesday' => 'Selasa',
                            'wednesday' => 'Rabu',
                            'thursday' => 'Kamis',
                            'friday' => 'Jumat',
                            'saturday' => 'Sabtu',
                            'sunday' => 'Minggu',
                        ])
                        ->live()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record) {
                                $selectedDays = $record->workDays()
                                    ->where('is_working_day', true)
                                    ->pluck('day')
                                    ->toArray();
                                $component->state($selectedDays);
                            }
                        })->saveRelationshipsUsing(function ($record, $state) {
                            $record->workDays()->update(['is_working_day' => false]);
                            foreach ($state as $day) {
                                $record->workDays()->updateOrCreate(
                                    ['day' => $day],
                                    ['is_working_day' => true]
                                );
                            }
                        })
                        ->required()
                        ->columns(3),

                    Forms\Components\Grid::make([
                        'default' => 4,
                    ])->schema([
                        Forms\Components\TextInput::make('longitude')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('latitude')
                            ->required()
                            ->numeric(),
                    ]),
                ])->columns(2),
                Section::make('Kop Surat')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('kop_surat')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListNagaris::route('/'),
            'create' => Pages\CreateNagari::route('/create'),
            'edit' => Pages\EditNagari::route('/{record}/edit'),
        ];
    }
}
