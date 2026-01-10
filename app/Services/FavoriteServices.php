<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class FavoriteServices
{
    public function get(Customer $customer): Collection
    {
        $favorites = $customer->favorites;
        NotFound($favorites, 'favorites');

        return $favorites->load(['holder.medias']);
    }

    public function find(Customer $customer, int $id): Favorite
    {
        $favorites = $customer->favorites()->where('id', $id)->first();
        NotFound($favorites, 'favorites');

        return $favorites;
    }

    public function create(Customer $customer, Model $owner): Favorite
    {
        Required($owner, 'owner');

        return $customer->favorites()->create([
            'favoritable_type' => $owner::class,
            'favoritable_id' => $owner->id,
        ]);
    }

    public function delete(int $id): bool|int
    {
        // return $customer->favorites()->detach($owner);
        return Favorite::find($id)->delete();
    }

    public function favoriteExists(Customer $customer, Model $model): bool
    {
        return $customer->favorites()->where([
            ['favoritable_type', $model::class],
            ['favoritable_id', $model->id],
        ])->exists();
    }
}
