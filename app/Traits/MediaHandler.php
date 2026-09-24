<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Media;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait MediaHandler
{
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'belongTo');
    }

    public function mediaByName(?string $name = ''): ?Model
    {
        return $this->medias()->where('name', $name)->first();
    }

    public function multiple(array $data): Collection
    {
        $uploads = Collection::empty();
        foreach ($data['media'] as $media) {
            $uploads->push(
                $this->uploadMedia(
                    type: $data['type'],
                    file: $media['file'],
                    name: $media['name'] ?? null,
                )
            );
        }

        return $uploads;
    }

    public function uploadMedia(
        string $type,
        ?string $name,
        UploadedFile $file,
    ): ?Media {
        $type = $this->getFileType($file);
        $fileName = time().'_'.$file->hashName();
        $path = $file->storeAs(
            $this->getFolder($type),
            $fileName,
            'public'
        );
        if (! app()->environment(['local', 'testing'])) {
            defer(fn () => Artisan::call('app:sync-files-to-public'));
        }

        try {
            return $this->medias()->create([
                'url' => $path,
                'type' => $type,
                'name' => $this->getFileName($file, $name),
            ]);
        } catch (Exception $e) {
            Log::error("Media Upload Faild: {$e->getMessage()}");
            Truthy(true, 'Media Upload Faild');

            return null;
        }
    }

    private function getFileType(UploadedFile $file): string
    {
        $mime = $this->getAllowedMime($file);

        return str_starts_with($mime, 'video') ? 'video' : 'image';
    }

    private function getFileName(UploadedFile $file, ?string $name = null): string
    {
        $maxLength = 30; // Define your fixed length here

        if ($name !== null) {
            $baseName = $name;
        } else {
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return mb_substr($baseName, 0, $maxLength, 'UTF-8');
    }

    private function getAllowedMime(UploadedFile $file): string
    {
        $mime = $file->getMimeType();
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'video/mp4'];

        if (! in_array($mime, $allowed)) {
            throw new Exception("File type $mime is not supported");
        }

        return $mime;
    }

    private function getFolder(string $type): string
    {
        return sprintf(
            'uploads/%s/%s/%s',
            mb_strtolower(Str::plural($type)),
            mb_strtolower(Str::plural(class_basename($this::class))),
            $this->id
        );
    }
}
