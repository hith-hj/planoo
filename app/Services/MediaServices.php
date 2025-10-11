<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class MediaServices
{
    public function allByObject(object $mediable): Collection|Model
    {
        Required($mediable, 'mediable');
        Truthy(! method_exists($mediable, 'medias'), 'object missing medias()');
        $medias = $mediable->medias;
        NotFound($medias, 'medias');

        return $medias;
    }

    public function findByObject(object $mediable, int $id): Media
    {
        Required($mediable, 'mediable');
        Truthy(! method_exists($mediable, 'medias'), 'object missing medias()');
        $media = $mediable->medias()->whereId($id)->first();
        NotFound($media, 'media');

        return $media;
    }

    public function create(object $mediable, array $data): Collection|Media
    {
        Required($mediable, 'mediable');
        Truthy(! method_exists($mediable, 'medias'), 'object missing medias()');
        Required($data, 'media data');
        $media = $mediable->multible($data);

        return $media;
    }

    public function delete(Media $media): bool
    {
        NotFound($media, 'Media');
        Storage::disk('public')->delete($media->url);
        return $media->delete();
    }
}
