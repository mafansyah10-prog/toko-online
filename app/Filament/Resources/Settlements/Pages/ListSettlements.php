<?php

namespace App\Filament\Resources\Settlements\Pages;

use App\Filament\Resources\Settlements\SettlementResource;
use App\Filament\Resources\Settlements\Widgets\SettlementCalendarWidget;
use App\Filament\Resources\Settlements\Widgets\SettlementOverview;
use Filament\Resources\Pages\ListRecords;

class ListSettlements extends ListRecords
{
    protected static string $resource = SettlementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SettlementOverview::class,
            SettlementCalendarWidget::class,
        ];
    }
}
