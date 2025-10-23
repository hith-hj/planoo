<?php

namespace App\Filament\Widgets;

use App\Enums\AccountStatus;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Admins\AdminResource;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Activity;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class GeneralStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $customers = Trend::query(Customer::where('status',AccountStatus::fresh->value))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        $users = Trend::query(User::where('status',AccountStatus::fresh->value))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        $appointments = Trend::model(Appointment::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            Stat::make('Users', User::count())
            ->color('success')
            ->description('New Users this month')
            ->chart($users->map(fn(TrendValue $value) => $value->aggregate))
            ->url(UserResource::getUrl()),

            Stat::make('Customers', Customer::count())
            ->color('success')
            ->description('New Customer this month')
            ->chart($customers->map(fn(TrendValue $value) => $value->aggregate))
            ->url(CustomerResource::getUrl()),

            Stat::make('Appointments', Appointment::count())
            ->color('success')
            ->description('New Appointments this month')
            ->chart($appointments->map(fn(TrendValue $value) => $value->aggregate))
            ->url(AppointmentResource::getUrl()),

            Stat::make('Activities', Activity::count())
            ->color('success')
            ->url(ActivityResource::getUrl()),

            Stat::make('Courses', Course::count())
            ->color('success')
            ->url(CourseResource::getUrl()),
        ];
    }
}
