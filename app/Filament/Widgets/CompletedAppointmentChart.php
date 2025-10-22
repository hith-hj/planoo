<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CompletedAppointmentChart extends ChartWidget
{
    protected ?string $heading = 'Completed Appointment Chart';
    protected static ?int $sort = 4;
    protected bool $isCollapsible = true;
    protected int | string | array $columnSpan = 2;
    protected function getData(): array
    {
        $data = Trend::query(Appointment::where('status',AppointmentStatus::completed->value))
            ->dateColumn('date')
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'canceled Appointment Counts',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
