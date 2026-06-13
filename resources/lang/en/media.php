<?php

declare(strict_types=1);

return [

    'media_id' => [
        'required' => 'The media ID identifier is required.',
        'exists' => 'The selected media file does not exist.',
    ],
    'type' => [
        'required' => 'The media type field is required.',
        'in' => 'The media type must be either an image or a video.',
    ],
    'media' => [
        'required' => 'Please provide the media content.',
        'array' => 'The media must be submitted as a structured list.',
        'min' => 'You must upload at least :min file.',
        'max' => 'You cannot upload more than :max files at once.',
    ],
    'media.*' => [
        'required' => 'Each media item detail block is required.',
        'array' => 'Each media item metadata must be a structured list.',
    ],
    'media.*.name' => [
        'string' => 'The media item custom label name must be a text value.',
        'max' => 'The file name entry must not exceed :max characters.',
    ],
    'media.*.file' => [
        'required' => 'The media attachment source file is required.',
        'image' => 'The uploaded file attachment must be a valid image.',
        'file' => 'The uploaded video entry attachment must be a valid file.',
        'mimes' => 'The file extension type must match one of these options: :values.',
        'max' => 'The uploaded attachment file size cannot exceed :max kilobytes.',
    ],
    'name' => [
        'required' => 'The group name or title is required.',
        'string' => 'The name must be a text value.',
        'max' => 'The name cannot exceed :max characters.',
    ],
    'group' => [
        'string' => 'The assigned layout group must be a text sequence string.',
    ],

];
