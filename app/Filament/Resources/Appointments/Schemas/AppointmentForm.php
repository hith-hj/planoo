<?php

declare(strict_types=1);

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

final class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('appointable_type')
                    ->required(),
                TextInput::make('appointable_id')
                    ->required()
                    ->numeric(),
                Select::make('customer_id')
                    ->relationship('customer', 'name'),
                DatePicker::make('date')
                    ->required(),
                TimePicker::make('time')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('session_duration')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->numeric(),
                TextInput::make('notes'),
                TextInput::make('canceled_by'),
            ]);
    }
}
