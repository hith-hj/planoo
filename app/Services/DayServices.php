<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Dayable;
use App\Models\Day;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class DayServices
{
    public function allByObject(Dayable $dayable): Collection|Model
    {
        $days = $dayable->days;
        NotFound($days, 'Days');

        return $days;
    }

    public function findByObject(Dayable $dayable, int $id): Day
    {
        $day = $dayable->days()->whereId($id)->first();
        NotFound($day, 'days');

        return $day;
    }

    public function find(int $id): Day
    {
        Required($id, 'id');
        $day = Day::whereId($id)->first();
        NotFound($day, 'day');

        return $day;
    }

    public function create(Dayable $dayable, array $data): Day
    {
        Required($data, 'day data');
        $this->checkCanCreateDay($dayable);
        $oldDays = $dayable->days->toArray();
        $this->checkIfAvailable($data, $oldDays);
        $day = $dayable->days()->create($data);

        return $day;
    }

    public function createMany(Dayable $dayable, array $data): Collection
    {
        $days = Collection::empty();
        $conflicts = [];
        foreach ($data['days'] as $item) {
            try {
                $days->push($this->create($dayable, $item));
            } catch (Exception $e) {
                $conflicts[$item['day']] = "Error {$e->getMessage()}";
            }
        }
        Falsy(empty($conflicts), 'Conflict: '.implode(', ', $conflicts));

        return $days;
    }

    public function update(Dayable $dayable, Day $day, array $data): Day
    {
        Required($data, 'day data');
        $oldDays = $dayable->days->toArray();
        $data = $this->formatData($data, $day);
        $this->checkIfAvailable($data, $oldDays, true);
        $day->update($data);

        return $day;
    }

    public function delete(Day $day): bool
    {
        return $day->delete();
    }

    public function toggleActivation(Day $day): bool
    {
        return $day->update(['is_active' => ! $day->is_active]);
    }

    public function checkIfAvailable(array $newDay, array $oldDays, bool $updateing = false)
    {
        foreach ($oldDays as $od) {
            // break out immediately
            if ($od['day'] === $newDay['day'] && ! $updateing) {
                throw new Exception("{$newDay['day']} exists");
            }
            if ($od['day'] === $newDay['day']) {
                if ($newDay['start'] === $od['start'] && $newDay['end'] === $od['end']) {
                    throw new Exception("Duplicated Day {$od['day']} at {$od['start']}"); // dublication
                }
                if ($updateing && isset($newDay['id']) && $newDay['id'] === $od['id']) {
                    continue;
                }
                if ($newDay['start'] < $od['end'] && $newDay['end'] > $od['start']) {
                    throw new Exception("Conflict with {$od['day']} at {$od['start']}"); // intersecting
                }
            }
        }

        return true; // avaiable
    }

    private function formatData(array $data, ?Day $day = null)
    {
        if ($day !== null) {
            $data['id'] = $day->id;
            $data['day'] = mb_strtolower($day->day);
        }

        return $data;
    }

    private function checkCanCreateDay(Dayable $dayable)
    {
        Truthy($dayable->days()->count() >= 7, 'Only 7 days allowed');
    }
}
