<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\SyncMediaFile;
use App\Models\Media;
use DomainException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Exception\LogicException;

trait MediaHandler
{
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'belongTo');
    }

    public function mediaByName(?string $name = ''): ?Model
    {
        if ($this->relationLoaded('medias')) {
            return $this->medias->firstWhere('name', $name);
        }

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
        Truthy($path === false, 'Media upload failed');

        try {
            $media = $this->medias()->create([
                'url' => $path,
                'type' => $type,
                'name' => $this->getFileName($file, $name),
            ]);
        } catch (Exception $e) {
            Storage::disk('public')->delete($path);
            Log::error("Media Upload Faild: {$e->getMessage()}");
            Truthy(true, 'Media Upload Faild');

            return null;
        }

        if (! app()->environment(['local', 'testing'])) {
            SyncMediaFile::dispatch($path);
        }

        return $media;
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
        $mime = null;

        try {
            $mime = $file->getMimeType();
        } catch (LogicException $e) {
            $mime = $file->getClientMimeType();
        }

        $allowed = ['image/jpeg', 'image/png', 'video/mp4'];

        // Normalize bad data or edge cases where client sent image/jpg
        if ($mime === 'image/jpg') {
            $mime = 'image/jpeg';
        }

        if ($mime === null || ! in_array($mime, $allowed, true)) {
            $displayMime = $mime ?? 'Unknown';
            throw new DomainException("File type $displayMime is not supported");
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
