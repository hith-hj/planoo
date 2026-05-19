<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\AdminsRoles;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Schemas\SettingsForm;
use App\Filament\Resources\Settings\Schemas\SettingsInfolist;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

final class SettingsResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'static';

    public static function canAccess(): bool
    {
        return Auth::user()->role === AdminsRoles::super->value;
    }

    public static function form(Schema $schema): Schema
    {
        return SettingsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SettingsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            // 'create' => CreateSettings::route('/create'),
            // 'view' => ViewSettings::route('/{record}'),
            // 'edit' => EditSettings::route('/{record}/edit'),
        ];
    }
}
