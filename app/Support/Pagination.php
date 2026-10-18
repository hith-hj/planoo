<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class Pagination
{
    public const MAX_PER_PAGE = 100;

    public const MAX_UNPAGINATED = 200;

    /** @return array{int, ?int} */
    public static function fromRequest(Request $request): array
    {
        if (! $request->hasAny(['page', 'perPage'])) {
            return [1, null];
        }
        $data = Validator::make([
            'page' => $request->input('page', 1),
            'perPage' => $request->input('perPage', 10),
        ], [
            'page' => ['required', 'integer', 'min:1'],
            'perPage' => ['required', 'integer', 'min:1', 'max:'.self::MAX_PER_PAGE],
        ])->validate();

        return [(int) $data['page'], (int) $data['perPage']];
    }

    public static function validate(int $page, int $perPage): void
    {
        Validator::make(compact('page', 'perPage'), [
            'page' => ['required', 'integer', 'min:1'],
            'perPage' => ['required', 'integer', 'min:1', 'max:'.self::MAX_PER_PAGE],
        ])->validate();
    }

    public static function bounded(Builder|Relation $query): Collection
    {
        $items = $query->limit(self::MAX_UNPAGINATED + 1)->get();
        if ($items->count() > self::MAX_UNPAGINATED) {
            throw ValidationException::withMessages(['paginate' => 'Too many results. Use pagination or narrower filters.']);
        }

        return $items;
    }
}
