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
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];
        $mime = null;

        try {
            $mime = $file->getMimeType();
        } catch (LogicException $e) {
            // 2. Fallback: fileinfo is missing. Check client properties.
            $allowedExtensions = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'mp4' => 'video/mp4',
            ];

            $extension = mb_strtolower($file->getClientOriginalExtension());

            // Fail if the extension itself isn't allowed
            if (! array_key_exists($extension, $allowedExtensions)) {
                throw new DomainException("File extension .$extension is not allowed");
            }

            // Determine expected MIME type based purely on the file extension
            $expectedMimeFromExt = $allowedExtensions[$extension];

            // Fetch what the browser claims the MIME type is
            $clientMime = $file->getClientMimeType();

            // Normalize non-standard client inputs (e.g., image/jpg -> image/jpeg)
            if ($clientMime === 'image/jpg') {
                $clientMime = 'image/jpeg';
            }

            // STRICT MATCH: Compare extension-deduced MIME with browser-provided MIME
            if ($expectedMimeFromExt !== $clientMime) {
                throw new DomainException('Security Alert: File extension and MIME type mismatch');
            }

            $mime = $expectedMimeFromExt;
        }

        // 3. Normalize secure check output if necessary
        if ($mime === 'image/jpg') {
            $mime = 'image/jpeg';
        }

        if ($mime === null || ! in_array($mime, $allowedMimes, true)) {
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
