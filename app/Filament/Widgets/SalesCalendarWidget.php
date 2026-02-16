<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class SalesCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.sales-calendar-widget';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public ?string $selectedDate = null;
    public ?string $selectedMonth = null;
    public ?string $selectedYear = null;
    public string $viewMode = 'daily'; // daily, monthly, yearly

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->selectedMonth = now()->format('Y-m');
        $this->selectedYear = now()->format('Y');
    }

    public function getSalesData(): array
    {
        $sales = 0;
        $orders = 0;
        $label = '';

        switch ($this->viewMode) {
            case 'daily':
                if ($this->selectedDate) {
                    $date = Carbon::parse($this->selectedDate);
                    $query = Order::whereDate('created_at', $date)
                        ->where('payment_status', 'paid');
                    $sales = $query->sum('grand_total');
                    $orders = $query->count();
                    $label = $date->format('d F Y');
                }
                break;

            case 'monthly':
                if ($this->selectedMonth) {
                    $date = Carbon::parse($this->selectedMonth . '-01');
                    $query = Order::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where('payment_status', 'paid');
                    $sales = $query->sum('grand_total');
                    $orders = $query->count();
                    $label = $date->format('F Y');
                }
                break;

            case 'yearly':
                if ($this->selectedYear) {
                    $query = Order::whereYear('created_at', $this->selectedYear)
                        ->where('payment_status', 'paid');
                    $sales = $query->sum('grand_total');
                    $orders = $query->count();
                    $label = 'Tahun ' . $this->selectedYear;
                }
                break;
        }

        return [
            'sales' => $sales,
            'orders' => $orders,
            'label' => $label,
        ];
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function getYearOptions(): array
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = $i;
        }
        return $years;
    }
}
