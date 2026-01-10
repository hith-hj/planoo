<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\SectionsTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait Filters
{
    /**
     * @param  string  $model  Model to call
     * @param  array  $callable  array of methods to call on the model
     * @param  string  $getter  type of getter get(), first(), ...
     * @param  array  $columns  array of columns to get
     * @return Collection|Model the returned value deppend on the getter
     *
     * @throws Exception if result not found
     **/
    public function getter(
        string $model,
        array $callable = [],
        string $getter = 'get',
        array $columns = ['*'],
        bool $sql = false,
    ): Collection|Model|null {
        Truthy(! in_array($model, SectionsTypes::names()), "Invalid model type: {$model}");
        $model = '\App\Models\\'.ucfirst(trim($model));
        $class = class_basename($model);
        Truthy(! class_exists($model), "class $class not found");

        $query = $model::query();
        if (! empty($callable)) {
            foreach ($callable as $key => $value) {
                if (is_array($value)) {
                    $query->$key(...$value);
                } else {
                    $query->$key($value);
                }
            }
        }

        Truthy($sql, $query->toRawSql());
        $result = $query->$getter($columns);

        NotFound($result, $class);

        return $result;
    }

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
