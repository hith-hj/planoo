<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\AccountStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => AccountStatus::from($state)->name),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_notifiable')
                    ->boolean(),
                TextEntry::make('verified_by')
                    ->placeholder('-'),
                TextEntry::make('verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ])
            ->columns(4);
    }
}
