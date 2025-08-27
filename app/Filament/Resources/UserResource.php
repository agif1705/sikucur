<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Data Pegawai';
    protected static ?string $navigationGroup = 'Pengaturan Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pegawai')
                    ->description('Berikan Informasi lengkap Pegawai, jika ada perubahan mohon memasukan Password agar tersimpan dengan benar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ini adalah username untuk login')
                            ->maxLength(50),
                        Forms\Components\Select::make('jabatan_id')
                            ->disabled(fn(): bool => !auth()->user()->hasRole('super_admin'))
                            ->label('Jabatan')
                            ->relationship('jabatan', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->numeric(),
                        Forms\Components\TextInput::make('no_ktp')
                            ->numeric(),
                        Forms\Components\TextInput::make('no_bpjs')
                            ->numeric(),
                        Forms\Components\TextInput::make('alamat')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->numeric(),
                        Forms\Components\TextInput::make('no_ktp')
                            ->numeric(),
                        Forms\Components\TextInput::make('no_bpjs')
                            ->numeric(),
                        Forms\Components\TextInput::make('alamat')
                            ->maxLength(255),

                        Section::make('Foto Pegawai')
                            ->description('Foto Pegawai yang akan di tampilkan di halaman utama')
                            ->aside()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->image(),
                            ]),
                    ])->columns(3),
                Section::make('Password Pegawai')
                    ->description('Mohon di ingat password pegawai')

                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('aktif')
                            ->label('Status Pegawai')
                            ->required(),
                        Forms\Components\Select::make('role_id')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->disabled(fn(): bool => !auth()->user()->hasRole('super_admin'))
                            ->required(),
                        Forms\Components\TextInput::make('emp_id')
                            ->label('Finggerprint ID')
                            ->maxLength(255)
                            ->disabled(fn(): bool => !auth()->user()->hasRole('super_admin')),
                        Forms\Components\Select::make('nagari_id')
                            ->label('Nama Nagari')
                            ->relationship('nagari', 'name')
                            ->disabled(fn(): bool => !auth()->user()->hasRole('super_admin'))
                            ->required(),

                    ])->columns(2)
                    ->collapsed()
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');
                if (!$is_super_admin) {
                    $query->where('id', Auth::user()->id);
                }
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('jabatan.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_bpjs')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('aktif')
                    ->onColor('success')
                    ->offColor('danger'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
