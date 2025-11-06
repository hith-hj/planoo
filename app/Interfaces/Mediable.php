<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface Mediable
{
    public function medias();

    public function multiple(array $data);

    public function uploadMedia(
        string $type,
        ?string $name,
        UploadedFile $file,
    );
}
