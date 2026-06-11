<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class CourtServices
{
    public function allByUser(User $user): Collection|Model
    {
        $courts = $user->courts()->with($this->toBeLoaded())->get();
        NotFound($courts, 'courts');

        return $courts;
    }

    public function findByUser(User $user, int $id): Court
    {
        $court = $user->courts()->whereId($id)->first();
        NotFound($court, 'court');

        return $court->load($this->toBeLoaded());
    }

    public function find(int $id): Court
    {
        Required($id, 'id');
        $court = Court::whereId($id)->first();
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

    private function toBeLoaded(): array
    {
        return ['activities', 'courses', 'events'];
    }
}
