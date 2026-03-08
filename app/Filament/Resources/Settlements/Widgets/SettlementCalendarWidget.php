<?php

namespace App\Filament\Resources\Settlements\Widgets;

use App\Models\Settlement;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SettlementCalendarWidget extends Widget
{
    protected string $view = 'filament.resources.settlements.widgets.settlement-calendar-widget';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '5s';

    public $currentMonth;

    public $currentYear;

    public $selectedDayData = null;

    public $selectedDate = null;

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedDayData = null;
        $this->selectedDate = null;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedDayData = null;
        $this->selectedDate = null;
    }

    public function selectDay($dateString)
    {
        $this->selectedDate = $dateString;
        $this->selectedDayData = Settlement::with('user')
            ->whereDate('closed_at', $dateString)
            ->get()
            ->map(function ($s) {
                return [
                    'cashier' => $s->user->name,
                    'total_sales' => $s->total_sales,
                    'id' => $s->id,
                ];
            });
    }

    protected function getViewData(): array
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $date->daysInMonth;
        $firstDayOfMonth = $date->dayOfWeek;

        $settlements = Settlement::whereMonth('closed_at', $this->currentMonth)
            ->whereYear('closed_at', $this->currentYear)
            ->select(
                DB::raw('DATE(closed_at) as date'),
                DB::raw('SUM(total_sales) as total'),
                DB::raw('COUNT(DISTINCT user_id) as cashier_count')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date')
            ->toArray();

        return [
            'daysInMonth' => $daysInMonth,
            'firstDayOfMonth' => $firstDayOfMonth,
            'settlements' => $settlements,
            'monthName' => $date->format('F'),
            'year' => $this->currentYear,
        ];
    }
}
