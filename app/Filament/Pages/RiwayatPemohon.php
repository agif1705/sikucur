<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RiwayatPemohon extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false; // Hide from navigation

    protected string $view = 'filament.pages.riwayat-pemohon';
}
