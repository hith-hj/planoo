<?php

declare(strict_types=1);

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;
}
