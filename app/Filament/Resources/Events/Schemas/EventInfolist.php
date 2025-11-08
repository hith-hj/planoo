<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_full')
                    ->boolean(),
                TextEntry::make('event_duration')
                    ->numeric(),
                TextEntry::make('capacity')
                    ->numeric(),
                TextEntry::make('rate')
                    ->numeric(),
                TextEntry::make('admission_fee')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('withdrawal_fee')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->numeric(),
                TextEntry::make('start_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
