<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_notifiable')
                    ->required(),
                TextInput::make('verified_by'),
                DateTimePicker::make('verified_at'),
            ]);
    }
}
