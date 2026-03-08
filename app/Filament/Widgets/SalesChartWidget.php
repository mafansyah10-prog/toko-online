<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Pendapatan Bulanan';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    public function mount(): void
    {
        $this->filter = (string) now()->year;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?: (string) now()->year;
        $start = Carbon::parse($activeFilter.'-01-01')->startOfYear();
        $end = Carbon::parse($activeFilter.'-01-01')->endOfYear();

        $data = \Flowframe\Trend\Trend::query(
            Order::where('payment_status', 'paid')
        )
            ->between(
                start: $start,
                end: $end,
            )
            ->perMonth()
            ->sum('grand_total');

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan Bulanan ('.$activeFilter.')',
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

    protected function getFilters(): ?array
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[(string) $i] = (string) $i;
        }

        return $years;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
