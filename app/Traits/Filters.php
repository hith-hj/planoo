<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filters
{
    private function applyFilters(object $query, array $filters = [], array $allowedFilters = []): object
    {
        return $query->when(
            ! empty($filters),
            function (Builder $filter) use ($filters, $allowedFilters) {
                foreach ($filters as $key => $value) {
                    if (! in_array($key, array_keys($allowedFilters))) {
                        return;
                    }
                    if (is_numeric($value)) {
                        $value = (int) $value;
                    }
                    if (in_array($value, $allowedFilters[$key]) || empty($allowedFilters[$key])) {
                        $filter->where($key, $value);
                    }
                }
            }
        );
    }

    private function applyOrderBy(object $query, array $orderBy = [], array $allowedOrderBy = []): object
    {
        return $query->when(
            ! empty($orderBy) && count($orderBy) === 1,
            function (Builder $query) use ($orderBy, $allowedOrderBy) {
                $key = array_key_first($orderBy);
                $value = array_pop($orderBy);
                if (in_array($key, $allowedOrderBy) && in_array($value, ['asc', 'desc'])) {
                    $query->orderBy($key, $value);
                }
            }
        );
    }
}
