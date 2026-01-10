<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Customer;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait FavoriteHandler
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavorite()
    {
        return $this->favorites()->where('customer_id', Auth::id());
    }
}
