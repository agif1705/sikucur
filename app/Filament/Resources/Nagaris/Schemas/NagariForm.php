<?php

namespace App\Filament\Resources\Nagaris\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NagariForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Nagari ')->schema([

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

                    Grid::make([
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
                    ]),
            ]);
    }
}
