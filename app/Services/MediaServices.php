<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Mediable;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class MediaServices
{
    public function allByObject(Mediable $mediable): Collection|Model
    {
        $medias = $mediable->medias;
        NotFound($medias, 'medias');

        return $medias;
    }

    public function findByObject(Mediable $mediable, int $id): Media
    {
        $media = $mediable->medias()->whereId($id)->first();
        NotFound($media, 'media');

        return $media;
    }

    public function create(Mediable $mediable, array $data): Collection|Media
    {
        Required($data, 'media data');
        $this->canCreateMedia($mediable);
        $media = $mediable->multiple($data);

        return $media;
    }

    public function delete(Media $media): bool
    {
        NotFound($media, 'Media');
        Storage::disk('public')->delete($media->url);

        return $media->delete();
    }

    private function canCreateMedia(object $mediable)
    {
        $count = config('app.settings.MAX_MEDIA_COUNT', 5);
        Truthy($mediable->medias()->count() >= $count, "Limit of $count media is reached");
    }
}
