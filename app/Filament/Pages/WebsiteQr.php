<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class WebsiteQr extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Website QR';

    protected static ?string $title = 'Website QR Code';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.website-qr';
}
