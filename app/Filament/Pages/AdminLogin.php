<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Schema;

final class AdminLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                // $this->getRememberFormComponent(),
            ]);
    }
}
