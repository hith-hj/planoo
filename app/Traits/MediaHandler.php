<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Media;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait MediaHandler
{
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function mediaByName(?string $name = ''): ?Model
    {
        return $this->medias()->where('name', $name)->first();
    }

    public function multible(array $data): Collection
    {
        $uploads = [];
        foreach ($data['media'] as $media) {
            $uploads[] = $this->uploadMedia(
                type: $data['type'],
                file: $media['file'],
                name: $media['name'] ?? null,
            );
        }

        return Collection::make($uploads);
    }

    public function uploadMedia(
        string $type,
        ?string $name,
        UploadedFile $file,
    ): Media {
        $type = $this->getFileType($file);
        $fileName = time().'_'.$file->hashName();
        $path = $file->storeAs(
            $this->getFolder($type),
            $fileName,
            'public'
        );
        if (! app()->environment(['local', 'testing'])) {
            defer(fn () => $this->syncImagesToPublic());
        }

        return $this->medias()->create([
            'url' => $path,
            'type' => $type,
            'name' => $this->getFileName($file, $name),
        ]);
    }

    private function getFileType(UploadedFile $file): string
    {
        $mime = $this->getAllowedMime($file);

        return str_starts_with($mime, 'video') ? 'video' : 'image';
    }

    private function getFileName(UploadedFile $file, ?string $name = null): string
    {
        if ($name !== null) {
            return $name;
        }

        return explode('.', $file->getClientOriginalName())[0];
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

    // private function syncImagesToPublic()
    // {
    //     try {
    //         if (function_exists('shell_exec')) {
    //             Log::info('Syncing Images');
    //             $command = 'rsync -a --delete --inplace --quiet '
    //                 . '/home/planoo/repositories/planoo/public/uploads/ '
    //                 . '/home/planoo/public_html/uploads';
    //             $guarded = "nice -n 10 ionice -c2 -n7 $command";
    //             $result = \exec($guarded);
    //             Log::info('Images Synced', ['result' => $result]);
    //         } else {
    //             Log::info('Shell is not enabled');
    //         }
    //     } catch (Exception $e) {
    //         Log::info('images syncing error', ['error' => $e->getMessage()]);
    //     }
    // }

    private function syncImagesToPublic()
    {
        try {
            Log::info('Syncing Images');

            $source = '/home/planoo/repositories/planoo/public/uploads/';
            $destination = '/home/planoo/public_html/uploads';

            $this->recursiveCopy($source, $destination);
            $this->deleteExtraFiles($source, $destination);

            Log::info('Images Synced');
        } catch (Exception $e) {
            Log::info('images syncing error', ['error' => $e->getMessage()]);
        }
    }

    private function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src.DIRECTORY_SEPARATOR.$file;
            $dstPath = $dst.DIRECTORY_SEPARATOR.$file;

            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }

    private function deleteExtraFiles($src, $dst)
    {
        $dir = opendir($dst);

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src.DIRECTORY_SEPARATOR.$file;
            $dstPath = $dst.DIRECTORY_SEPARATOR.$file;

            if (! file_exists($srcPath)) {
                if (is_dir($dstPath)) {
                    $this->deleteDirectory($dstPath);
                } else {
                    unlink($dstPath);
                }
            } elseif (is_dir($dstPath)) {
                $this->deleteExtraFiles($srcPath, $dstPath);
            }
        }

        closedir($dir);
    }

    private function deleteDirectory($dir)
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
