<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

final class SettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique('settings', 'name')
                    ->disabledOn(Operation::Edit),
                TextInput::make('value'),
                Textarea::make('description'),
            ]);
    }
}
