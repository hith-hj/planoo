<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AppointmentsChart extends ChartWidget
{
    protected ?string $heading = 'Appointments charts';

    protected static ?int $sort = 2;
    protected bool $isCollapsible = true;
    protected int | string | array $columnSpan = 'full';


    protected function getData(): array
    {
        $data = Trend::model(Appointment::class)
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
                    'label' => 'Appointment Counts',
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
