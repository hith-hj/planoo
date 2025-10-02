<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class MediaValidators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'media_id' => ['required', 'exists:media,id'],
        ]);
    }

    public static function create(array $data)
    {
        $validator = Validator::make($data, [
            'type' => ['required', 'in:image,video'],
            'media' => ['required', 'array', 'min:1', 'max:5'],
            'media.*' => ['required', 'array'],
            'media.*.name' => ['nullable', 'string'],
        ])->sometimes(
            'media.*.file',
            ['image', 'mimetypes:image/jpeg,image/png,', 'max:2048'],
            fn ($input) => $input->type === 'image'
        )->sometimes(
            'media.*.file',
            ['file', 'mimetypes:video/avi,video/mpeg,video/mp4', 'max:20480'],
            fn ($input) => $input->type === 'video'
        );

        return $validator;
    }

    public static function update(array $data)
    {
        return Validator::make($data, [
            'media_id' => ['required', 'exists:media,id'],
            'name' => ['required', 'string', 'max:20'],
            'group' => ['sometimes', 'string'],
        ]);
    }
}
