<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

final class AcceptedAppointmentChart extends ChartWidget
{
    protected ?string $heading = 'Accepted Appointment Chart';

    protected static ?int $sort = 3;

    protected bool $isCollapsible = true;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $data = Trend::query(Appointment::where('status', AppointmentStatus::accepted->value))
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
                    'label' => 'Accepted Appointment Counts',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
