<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Enums\AdminsRoles;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->required(),
                Select::make('role')
                    ->required()
                    ->options(fn()=>AdminsRoles::names()),
            ]);
    }
}
