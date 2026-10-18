<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Court;
use App\Models\User;
use App\Support\Pagination;
use Illuminate\Database\Eloquent\Collection;

final class CourtServices
{
    public function allByUser(User $user, int $page = 1, ?int $perPage = null): Collection
    {
        Required($user, 'user');
        $query = $user->courts()->orderBy('courts.id');
        if ($perPage === null) {
            $items = Pagination::bounded($query);
            NotFound($items, 'courts');

            return $items->load($this->toBeLoaded());
        }
        Pagination::validate($page, $perPage);
        $items = $query->with($this->toBeLoaded(true))->withCount(['activities', 'courses', 'events'])
            ->forPage($page, $perPage)->get();

        return $items;
    }

    public function findByUser(User $user, mixed $id): Court
    {
        $court = $user->courts()->whereId($id)->first();
        NotFound($court, 'court');

        return $court->load($this->toBeLoaded());
    }

    public function find(mixed $id): Court
    {
        Required($id, 'id');
        $court = Court::whereId($id)->first();
        NotFound($court, 'court');

        return $court->load($this->toBeLoaded());
    }

    public function all(int $page = 1, int $perPage = 10)
    {
        Pagination::validate($page, $perPage);
        $query = Court::with($this->toBeLoaded(true))->withCount(['activities', 'courses', 'events']);
        $courts = $query->paginate($perPage, ['*'], 'page', $page);
        NotFound($courts, 'court');

        return $courts;
    }

    public function search(string $name, int $page = 1, ?int $perPage = null): Collection
    {
        Required($name, 'name');
        $query = Court::whereLike('name', $name)->orderBy('id');
        if ($perPage !== null) {
            Pagination::validate($page, $perPage);

            return $query->withCount(['activities', 'courses', 'events'])->forPage($page, $perPage)->get();
        }
        $court = Pagination::bounded($query);
        NotFound($court, 'court');

        return $court->load($this->toBeLoaded());
    }

    public function create(User $user, array $data): Court
    {
        Required($data, 'court data');
        $court = $user->courts()->create($data);

        return $court->load($this->toBeLoaded());
    }

    public function update(Court $court, array $data): Court
    {
        Required($data, 'court data');
        $court->update($data);

        return $court->load($this->toBeLoaded());
    }

    public function delete(Court $court): bool
    {
        return $court->delete();
    }

    public function toggleActivation(Court $court): bool
    {
        return $court->update(['is_active' => ! $court->is_active]);
    }

    public function hasCourt(User $user): bool
    {
        return $user->courts()->exists();
    }

    private function toBeLoaded(bool $summary = false): array
    {
        return $summary ? [] : ['activities', 'courses', 'events'];
    }
}
