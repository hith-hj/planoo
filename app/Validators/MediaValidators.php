<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class MediaValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'media_id' => ['required', 'exists:media,id'],
        ]);
    }

    public static function create(array $data): \Illuminate\Validation\Validator
    {
        $maxMedia = setting('max_media_count', 5);
        $isImage = ($data['type'] ?? null) === 'image';
        $isVideo = ($data['type'] ?? null) === 'video';

        return Validator::make($data, [
            'type' => ['required', Rule::in(['image', 'video'])],
            'media' => ['required', 'array', 'min:1', "max:{$maxMedia}"],
            'media.*' => ['required', 'array'],
            'media.*.name' => ['nullable', 'string', 'max:255'],
            'media.*.file' => [
                'required',
                Rule::when($isImage, ['image', 'mimes:jpeg,png', 'max:2048']),
                Rule::when($isVideo, ['file', 'mimes:avi,mp4,mpeg', 'max:20480']),
            ],
        ]);
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
