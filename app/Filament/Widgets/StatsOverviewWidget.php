<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Today's sales
        $todaySalesQuery = Order::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid');
        $todaySales = $todaySalesQuery->sum('grand_total');
        $todayOrdersCount = $todaySalesQuery->count();

        // This month's sales
        $monthSalesQuery = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid');
        $monthSales = $monthSalesQuery->sum('grand_total');

        // This year's sales
        $yearSales = Order::whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        // Total orders & pending
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'new')->count();

        // Chart Data (Trends)
        $last7Days = \Flowframe\Trend\Trend::model(Order::class)
            ->between(start: now()->subDays(6), end: now())
            ->perDay()
            ->sum('grand_total')
            ->map(fn ($value) => $value->aggregate)
            ->toArray();

        $last30Days = \Flowframe\Trend\Trend::model(Order::class)
            ->between(start: now()->subDays(29), end: now())
            ->perDay()
            ->sum('grand_total')
            ->map(fn ($value) => $value->aggregate)
            ->toArray();

        $thisYearMonthly = \Flowframe\Trend\Trend::model(Order::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->sum('grand_total')
            ->map(fn ($value) => $value->aggregate)
            ->toArray();

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaySales, 0, ',', '.'))
                ->description($todayOrdersCount . ' pesanan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($last7Days),
            
            Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($monthSales, 0, ',', '.'))
                ->description(Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart($last30Days),
            
            Stat::make('Penjualan Tahun Ini', 'Rp ' . number_format($yearSales, 0, ',', '.'))
                ->description('Tahun ' . Carbon::now()->year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning')
                ->chart($thisYearMonthly),
            
            Stat::make('Total Pesanan', $totalOrders)
                ->description($pendingOrders . ' pesanan pending')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
        ];
    }
}
