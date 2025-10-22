<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
