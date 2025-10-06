<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\Appointment;
use Carbon\Carbon;

final class AppointmentServices
{
    public function checkAvailableSlots(array $data): array
    {
        $duration = SessionDuration::from($data['session_duration'])->value;
        $activity = app(ActivityServices::class)->find($data['activity_id']);
        $day = app(DayServices::class)
            ->findByObject($activity, $data['day_id'])
            ->only(['day', 'start', 'end']);
        $appointments = Appointment::owner($activity::class, $activity->id)
            ->where('date', $data['date'])
            ->get(['date', 'time', 'session_duration']);

        $start = Carbon::parse($day['start']);
        $end = Carbon::parse($day['end']);

        $busySessions = collect($appointments)->map(function ($appt) {
            $start = Carbon::parse($appt['time']);
            $end = $start->copy()->addMinutes($appt['session_duration']);
            return ['start' => $start, 'end' => $end];
        })->sortBy('start')->values();

        $slots = [];
        $previousEnd = $start;
        // add breack here $break = config('break_in_minute',10)
        foreach ($busySessions as $session) {
            $sessionStart = $previousEnd;
            $sessionEnd = $session['start'];
            $sessionMinutes = $sessionStart->diffInMinutes($sessionEnd); // + $break;
            $slots = [...$this->checkAllowedDurations($sessionStart, $sessionMinutes, $duration)];
            $previousEnd = max($previousEnd, $session['end']);
        }

        if ($previousEnd->lt($end)) {
            $sessionStart = $previousEnd;
            $sessionEnd = $end;
            $sessionMinutes = $sessionStart->diffInMinutes($sessionEnd);
            $slots = [...$this->checkAllowedDurations($sessionStart, $sessionMinutes, $duration)];
        }
        return ['day' => $day['day'], 'date' => $data['date'], 'slots' => $slots];
    }

    public function create(object $owner, array $data): Appointment
    {
        Truthy(!method_exists($owner, 'appointments'), 'missing appointments() method');
        return $owner->appointments()->create([
            'date' => $data['date'],
            'time' => $data['time'],
            'session_duration' => $data['session_duration'],
            'status' => AppointmentStatus::accepted,
            'price' => $owner->price,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function checkAppointmentExists(array $data): bool
    {
        $data = $this->checkAndCastData($data, [
            'activity_id' => 'int',
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string'
        ]);
        $activity = app(ActivityServices::class)->find($data['activity_id']);
        Truthy(!$activity || !method_exists($activity, 'appointments'),'invalid activity');
        return $activity->appointments()->where([
            ['date', $data['date']],
            ['time', $data['time']],
            ['session_duration', $data['session_duration']],
            ['status', '!=', AppointmentStatus::canceled],
        ])->exists();
    }

    private function checkAllowedDurations($gapStart, $gapMinutes, $duration): array
    {
        $slots = [];
        foreach (SessionDuration::values() as $sd) {
            if ($sd == $duration && $gapMinutes >= $sd) {
                $slots[] = [
                    'start' => $gapStart->format('H:i'),
                    'end' => $gapStart->copy()->addMinutes($sd)->format('H:i'),
                    'sd' => $sd
                ];
            }
        }
        return $slots;
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = [];
        foreach ($requiredFields as $key => $value) {
            $value = trim($value);
            if (str_contains($value, '|')) {
                [$type, $default] = explode('|', $value);
                $value = $type;
                if (! isset($data[$key])) {
                    $data[$key] = $default;
                }
            }

            if (str_contains($key, '.')) {
                [$name, $sub] = explode('.', $key);
                if (! isset($data[$name][$sub])) {
                    $missing[] = $key;

                    continue;
                }
                settype($data[$name][$sub], $value);

                continue;
            }
            if (! isset($data[$key])) {
                $missing[] = $key;

                continue;
            }
            settype($data[$key], $value);
        }
        Falsy(empty($missing), 'fields missing: ' . implode(', ', $missing));

        return $data;
    }
}
