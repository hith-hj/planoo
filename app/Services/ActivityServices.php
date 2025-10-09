<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class ActivityServices
{
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

        return $activity->load($this->toBeLoaded());
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
        return ['days', 'location', 'tags', 'medias', 'category'];
    }
}
