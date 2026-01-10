<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\User;
use App\Traits\Filters;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class ActivityServices
{
    use Filters;

    public function allByFilter(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ) {
        $query = Activity::query();
        $query->with($this->toBeLoaded());

        $this->applyFilters($query, $filters, [
            'is_active' => [true, false],
            'session_duration' => SessionDuration::values(),
            'category_id' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['rate', 'price']);

        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        NotFound($activities->items(), 'activities');

        return $activities;
    }

    public function allByUser(User $user): Collection|Model
    {
        Required($user, 'user');
        $activities = $user->activities;
        NotFound($activities, 'activities');

        return $activities->load($this->toBeLoaded());
    }

    public function findByUser(User $user, int $id): Activity
    {
        Required($user, 'user');
        $activity = $user->activities()->whereId($id)->first();
        NotFound($activity, 'activity');

        return $activity->load($this->toBeLoaded());
    }

    public function find(int $id): Activity
    {
        Required($id, 'id');
        $activity = Activity::whereId($id)->first();
        NotFound($activity, 'activity');

        return $activity->load([...$this->toBeLoaded(), 'isFavorite']);
    }

    public function create(User $user, array $data): Activity
    {
        Required($user, 'user');
        Required($data, 'activity data');
        $activity = $user->activities()->create($data);

        return $activity->load($this->toBeLoaded());
    }

    public function update(User $user, Activity $activity, array $data): Activity
    {
        Required($user, 'user');
        Required($data, 'activity data');
        $activity->update($data);

        return $activity->load($this->toBeLoaded());
    }

    public function delete(Activity $activity): bool
    {
        return $activity->delete();
    }

    public function toggleActivation(Activity $activity): bool
    {
        return $activity->update(['is_active' => ! $activity->is_active]);
    }

    private function toBeLoaded()
    {
        return ['days', 'location', 'tags', 'medias', 'category', 'reviews'];
    }
}
