<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;

final class LocationServices
{
    public function get(object $locatable)
    {
        Required($locatable, 'locatable');
        Truthy(! method_exists($locatable, 'location'), 'missing location method');
        $location = $locatable->location;
        NotFound($location, 'location');

        return $location;
    }

    public function create(object $locatable, array $data): Location
    {
        Required($data, 'data');
        Truthy(! method_exists($locatable, 'location'), 'missing location method');
        $data = $this->checkAndCastData($data, [
            'long' => 'float',
            'lat' => 'float',
            'name' => 'string|name',
        ]);

        return $locatable->location()->create([
            'long' => round($data['long'], 8),
            'lat' => round($data['lat'], 8),
            'name' => $data['name'],
        ]);
    }

    public function update(object $locatable, array $data): Location
    {
        Truthy(! method_exists($locatable, 'location'), 'object missing location()');
        $data = $this->checkAndCastData($data, [
            'long' => 'float',
            'lat' => 'float',
            'name' => 'string|name',
        ]);

        $locatable->location->update([
            'long' => round($data['long'], 8),
            'lat' => round($data['lat'], 8),
            'name' => $data['name'],
        ]);

        return $locatable->location;
    }

    public function delete(object $locatable): int
    {
        Truthy(! method_exists($locatable, 'location'), 'missing location method');
        Truthy($locatable->location === null, 'location not set');

        return $locatable->location()->delete();
    }

    public function checkLocationExists(object $locatable): bool
    {
        Truthy(! method_exists($locatable, 'location'), 'missing location method');

        return $locatable->location !== null;
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = [];
        foreach ($requiredFields as $key => $value) {
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
        Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));

        return $data;
    }
}
