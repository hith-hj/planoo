<?php

namespace App\Filament\Widgets;

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

class GeneralStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Admins', Admin::count())->url(AdminResource::getUrl()),
            Stat::make('Users', User::count())->url(UserResource::getUrl()),
            Stat::make('Customers', Customer::count())->url(CustomerResource::getUrl()),
            Stat::make('Activities', Activity::count())->url(ActivityResource::getUrl()),
            Stat::make('Courses', Course::count())->url(CourseResource::getUrl()),
            Stat::make('Appointments', Appointment::count())->url(AppointmentResource::getUrl()),
        ];
    }
}
