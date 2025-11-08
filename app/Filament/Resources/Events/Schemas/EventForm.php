<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_full')
                    ->required(),
                TextInput::make('event_duration')
                    ->required()
                    ->numeric(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('admission_fee')
                    ->numeric(),
                TextInput::make('withdrawal_fee')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
            ]);
    }
}
