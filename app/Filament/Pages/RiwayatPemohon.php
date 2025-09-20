<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RiwayatPemohon extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false; // Hide from navigation

    protected static string $view = 'filament.pages.riwayat-pemohon';
}
