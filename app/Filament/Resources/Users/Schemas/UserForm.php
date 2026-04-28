<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $form): Schema
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
                            ->disabled(fn (): bool => ! Auth::user()->hasRole('super_admin'))
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
                            ->disabled(fn (): bool => ! Auth::user()->hasRole('super_admin'))
                            ->required(),
                        Forms\Components\TextInput::make('emp_id')
                            ->label('Finggerprint ID')
                            ->maxLength(255)
                            ->disabled(fn (): bool => ! Auth::user()->hasRole('super_admin')),
                        Forms\Components\Select::make('nagari_id')
                            ->label('Nama Nagari')
                            ->relationship('nagari', 'name')
                            ->disabled(fn (): bool => ! Auth::user()->hasRole('super_admin'))
                            ->required(),

                    ])->columns(2)
                    ->collapsed()
                    ->persistCollapsed(),
            ]);
    }
}
