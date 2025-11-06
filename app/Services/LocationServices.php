<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Locatable;
use App\Models\Location;

final class LocationServices
{
    public function get(Locatable $locatable)
    {
        $location = $locatable->location;
        NotFound($location, 'location');

        return $location;
    }

    public function create(Locatable $locatable, array $data): Location
    {
        Required($data, 'data');
        $data = checkAndCastData($data, [
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

    public function update(Locatable $locatable, array $data): Location
    {
        $data = checkAndCastData($data, [
            'long' => 'float',
            'lat' => 'float',
            'name' => 'string|name',
        ]);
        NotFound($locatable->location, 'location is not found');
        $locatable->location->update([
            'long' => round($data['long'], 8),
            'lat' => round($data['lat'], 8),
            'name' => $data['name'],
        ]);

        return $locatable->location;
    }

    public function delete(Locatable $locatable): int
    {
        Truthy($locatable->location === null, 'location not set');

        return $locatable->location()->delete();
    }

    public function checkLocationExists(Locatable $locatable): bool
    {
        return $locatable->location !== null;
    }
}
