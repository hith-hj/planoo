<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CourseStatus;
use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class ResourceConflictDetector extends Command
{
    protected $signature = 'resource:detect-conflict {--resource-id=} {--resource-type=}';

    protected $description = 'Check if a resource has any schedule conflict for the same court';

    public function handle(): int
    {
        $this->info('Detector started...');

        $resourceId = $this->option('resource-id');
        $resourceType = $this->option('resource-type');

        if ($resourceId && $resourceType) {
            $resource = getModelGlobal($resourceType, $resourceId);
            if (! $resource) {
                $this->error('Target resource not found.');

                return self::FAILURE;
            }
            $this->info("Checking specific resource Type: {$resourceType} (Name: {$resource->name})");
            $resource->load(['court.activities.days', 'court.courses.days', 'court.events.days', 'days']);
            $conflicts = $this->checkConflict($resource);
            $this->outputConflicts($resource, $conflicts);

            return self::SUCCESS;
        }

        $this->handleDatabaseRecords();

        return self::SUCCESS;
    }

    /**
     * Processes all pending courses and events in bulk.
     */
    private function handleDatabaseRecords(): void
    {
        $this->comment('Fetching pending courses...');
        $courses = Course::with(['court.activities.days', 'court.courses.days', 'court.events.days', 'days'])
            ->where('status', CourseStatus::pending->value)
            ->get();

        foreach ($courses as $course) {
            $conflicts = $this->checkConflict($course);
            $this->outputConflicts($course, $conflicts);
        }

        $this->comment('Fetching pending events...');
        $events = Event::with(['court.activities.days', 'court.courses.days', 'court.events.days', 'days'])
            ->where('status', EventStatus::pending->value)
            ->get();

        foreach ($events as $event) {
            $conflicts = $this->checkConflict($event);
            $this->outputConflicts($event, $conflicts);
        }
    }

    /**
     * Inspects a resource against court schedules.
     */
    private function checkConflict(object $resource): array
    {
        $days = $resource->days;
        $court = $resource->court;

        if (! $court) {
            return [];
        }

        $activities = $court->activities;
        $courses = $court->courses;
        $events = $court->events;

        $conflicts = [];

        foreach ($days as $day) {
            // Convert to array if $day is a Model to use array syntax $day['day']
            $dayArray = is_array($day) ? $day : $day->toArray();

            // 1. Check against court activities
            foreach ($activities as $activity) {
                // Exclude the original resource from checking against itself
                if (get_class($resource) === get_class($activity) && $resource->id === $activity->id) {
                    continue;
                }
                if ($result = $this->daysConflicting($dayArray, $activity->days, $activity)) {
                    $conflicts['activities'][] = $result;
                }
            }

            // 2. Check against court courses
            foreach ($courses as $course) {
                if (get_class($resource) === get_class($course) && $resource->id === $course->id) {
                    continue;
                }
                if ($result = $this->daysConflicting($dayArray, $course->days, $course)) {
                    $conflicts['courses'][] = $result;
                }
            }

            // 3. Check against court events
            foreach ($events as $event) {
                if (get_class($resource) === get_class($event) && $resource->id === $event->id) {
                    continue;
                }
                if ($result = $this->daysConflicting($dayArray, $event->days, $event)) {
                    $conflicts['events'][] = $result;
                }
            }
        }

        return $conflicts;
    }

    /**
     * Logic to determine time/day overlaps.
     */
    private function daysConflicting(array $newDay, array|Collection $oldDays, object $owner): array
    {
        $conflicts = [];

        foreach ($oldDays as $od) {
            $odDay = is_array($od) ? $od['day'] : $od->day;
            $odStart = is_array($od) ? $od['start'] : $od->start;
            $odEnd = is_array($od) ? $od['end'] : $od->end;

            if ($odDay !== $newDay['day']) {
                continue;
            }

            // 1. Check for duplicate configurations
            if ($newDay['start'] === $odStart && $newDay['end'] === $odEnd) {
                $conflicts['duplication'][] = [
                    'id' => $owner->id ?? 'Unknown',
                    'owner' => $owner->name ?? 'Unknown',
                    'message' => "Duplicated Day {$odDay} at {$odStart}",
                ];

                continue;
            }

            // 2. Check for time window overlapping intersection
            if ($newDay['start'] < $odEnd && $newDay['end'] > $odStart) {
                $conflicts['intersection'][] = [
                    'id' => $owner->id ?? 'Unknown',
                    'owner' => $owner->name ?? 'Unknown',
                    'message' => "Conflict with {$odDay} from {$odStart} to {$odEnd}",
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Outputs findings to command interface terminal.
     */
    private function outputConflicts(object $resource, array $conflicts): void
    {
        $resourceName = $resource->name ?? 'Resource ID: '.$resource->id;

        if (empty($conflicts)) {
            $this->info("No conflicts found for [{$resourceName}]");
            // $this->activateResource($resource);
            $this->notifyResourceOwner($resource);

            return;
        }

        $this->warn("Conflicts detected for [{$resourceName}]:");
        $this->line(json_encode($conflicts, JSON_PRETTY_PRINT));
        $this->notifyResourceOwner($resource, $conflicts);
    }

    private function activateResource(object $resource)
    {
        if ($resource::class === Activity::class) {
            $resource->update(['is_active' => true]);
        } else {
            $resource->update(['status' => 1]);
        }
    }

    private function notifyResourceOwner($resource, $conflicts = null)
    {
        /** @var User $user */
        $user = $resource->user;
        if ($conflicts === null) {
            return $user->notify(
                'Schedule passed',
                'You have no schedule conflicts with the new resource',
                [
                    'type' => NotificationTypes::normal->value,
                ],
            );
        }
        $user->notify(
            'Schedule error',
            'You have schedule conflicts',
            [
                'type' => NotificationTypes::normal->value,
                'conflicts' => $conflicts,
            ],
        );
    }
}
