<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Taggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class TagServices
{
    public function allByObject(Taggable $taggable): Collection|Model
    {
        $tags = $taggable->tags;
        NotFound($tags, 'tags');

        return $tags;
    }

    public function create(Taggable $taggable, array $data)
    {
        Required($data, 'data');
        $tags = $this->formateTags($data['tags'], $taggable);

        return $taggable->tags()->attach($tags);
    }

    public function delete(Taggable $taggable, array $data)
    {
        Required($data, 'data');

        return $taggable->tags()->detach($data['tags']);
    }

    private function formateTags($tags, $taggable): array
    {
        return array_diff($tags, $taggable->tags()->pluck('tag_id')->toArray());
    }
}
