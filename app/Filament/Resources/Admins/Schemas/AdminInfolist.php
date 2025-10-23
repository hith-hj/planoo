<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admins\Schemas;

use App\Enums\AdminsRoles;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class AdminInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('role')
                    ->formatStateUsing(fn (string $state): string => AdminsRoles::from($state)->name),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
