<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

final class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique('categories', 'name'),
                FileUpload::make('icon')
                    ->label('Upload Category Icon')
                    ->acceptedFileTypes(['image/png'])
                    ->imageEditor()
                    ->maxSize(1000)
                    ->rule(Rule::dimensions()->maxWidth(50)->maxHeight(50))
                    ->disk('public')
                    // Keep existing mirrored icons when the source file is not local.
                    ->fetchFileInformation(false)
                    ->directory('uploads/categories_icons'),
            ])
            ->columns(1);
    }
}
