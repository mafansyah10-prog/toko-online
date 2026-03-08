<?php

namespace App\Filament\Resources\Settlements\Widgets;

use App\Models\Settlement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SettlementOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Settlement::sum('total_sales');
        $totalDiff = Settlement::query()->selectRaw('SUM(actual_cash_amount - cash_sales) as diff')->value('diff') ?? 0;
        $activeShifts = Settlement::whereNull('closed_at')->count();

        return [
            Stat::make('Total Pendapatan (Settle)', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description('Total dari semua sesi yang ditutup')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Total Selisih Kas', 'Rp '.number_format($totalDiff, 0, ',', '.'))
                ->description('Akumulasi selisih kas (Actual vs Expected)')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color($totalDiff < 0 ? 'danger' : 'warning'),
            Stat::make('Shift Aktif', $activeShifts)
                ->description('Jumlah kasir yang sedang bertugas')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
