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
        ], self::messages());
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
        ], self::messages());
    }

    public static function update(array $data)
    {
        return Validator::make($data, [
            'media_id' => ['required', 'exists:media,id'],
            'name' => ['required', 'string', 'max:20'],
            'group' => ['sometimes', 'string'],
        ], self::messages());
    }

    /**
     * Get the media validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'media_id.required' => __('media.media_id.required'),
            'media_id.exists' => __('media.media_id.exists'),

            'type.required' => __('media.type.required'),
            'type.in' => __('media.type.in'),

            'media.required' => __('media.media.required'),
            'media.array' => __('media.media.array'),
            'media.min' => __('media.media.min'),
            'media.max' => __('media.media.max'),

            'media.*.required' => __('media.media.*.required'),
            'media.*.array' => __('media.media.*.array'),

            'media.*.name.string' => __('media.media.*.name.string'),
            'media.*.name.max' => __('media.media.*.name.max'),

            'media.*.file.required' => __('media.media.*.file.required'),
            'media.*.file.image' => __('media.media.*.file.image'),
            'media.*.file.file' => __('media.media.*.file.file'),
            'media.*.file.mimes' => __('media.media.*.file.mimes'),
            'media.*.file.max' => __('media.media.*.file.max'),

            'name.required' => __('media.name.required'),
            'name.string' => __('media.name.string'),
            'name.max' => __('media.name.max'),

            'group.string' => __('media.group.string'),
        ];
    }
}
