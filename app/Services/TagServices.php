<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class TagServices
{
    public function allByObject(object $taggable): Collection|Model
    {
        Required($taggable, 'taggable');
        Truthy(! method_exists($taggable, 'tags'), 'object missing tags()');
        $tags = $taggable->tags;
        NotFound($tags, 'tags');

        return $tags;
    }

    public function create(object $taggable, array $data)
    {
        Required($data, 'data');
        Truthy(! method_exists($taggable, 'tags'), 'missing tags() method');
        $tags = $this->formateTags($data['tags'], $taggable);

        return $taggable->tags()->attach($tags);
    }

    public function delete(object $taggable, array $data)
    {
        Required($data, 'data');
        Truthy(! method_exists($taggable, 'tags'), 'missing tags() method');

        return $taggable->tags()->detach($data['tags']);
    }

    private function formateTags($tags, $taggable): array
    {
        return array_diff($tags, $taggable->tags()->pluck('tag_id')->toArray());
    }
}
