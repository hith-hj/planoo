<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\AccountStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('account_type'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('phone'),
                TextEntry::make('rate')
                    ->numeric(),
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
                TextEntry::make('description')
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }
}
