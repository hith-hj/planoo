<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\User;
use App\Support\Pagination;
use App\Traits\Filters;
use Illuminate\Database\Eloquent\Collection;

final class ActivityServices
{
    use Filters;

    public function allByFilter(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ) {
        Pagination::validate($page, $perPage);
        $query = Activity::query();
        $query->with($this->toBeLoaded(true));

        $this->applyFilters($query, $filters, [
            'is_active' => [true, false],
            'session_duration' => SessionDuration::values(),
            'category_id' => [],
            'court_id' => [],
            'name' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['rate', 'price']);

        $activities = $query->withCount(['reviews'])->paginate($perPage, ['*'], 'page', $page);

        NotFound($activities->items(), 'activities');

        return $activities;
    }

    public function allByUser(User $user, int $page = 1, ?int $perPage = null): Collection
    {
        Required($user, 'user');
        $query = $user->activities()->orderBy('activities.id');
        if ($perPage === null) {
            $items = Pagination::bounded($query);
            NotFound($items, 'activities');

            return $items->load($this->toBeLoaded());
        }
        Pagination::validate($page, $perPage);
        $items = $query->with($this->toBeLoaded(true))->withCount(['reviews'])
            ->forPage($page, $perPage)->get();

        return $items;
    }

    public function findByUser(User $user, mixed $id): Activity
    {
        Required($user, 'user');
        $activity = $user->activities()->whereId($id)->first();
        NotFound($activity, 'activity');

        return $activity->load($this->toBeLoaded());
    }

    public function find(mixed $id): Activity
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

    private function toBeLoaded(bool $summary = false): array
    {
        return $summary ? ['court', 'days', 'location', 'tags', 'medias', 'category'] : ['court', 'days', 'location', 'tags', 'medias', 'category', 'reviews'];
    }
}
