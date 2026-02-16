<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan Bulanan';
    
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = \Flowframe\Trend\Trend::model(Order::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('grand_total');

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan Bulanan',
                    'data' => $data->map(fn (\Flowframe\Trend\TrendValue $value) => $value->aggregate),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                    'borderColor' => 'rgb(244, 63, 94)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->map(fn (\Flowframe\Trend\TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
