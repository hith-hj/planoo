<?php

declare(strict_types=1);

return [
    'activity_id' => [
        'required' => 'Please select a valid activity.',
        'exists' => 'The selected activity does not exist.',
    ],
    'category_id' => [
        'required' => 'A category is required.',
        'exists' => 'The chosen category is invalid.',
    ],
    'name' => [
        'required' => 'The activity name cannot be blank.',
    ],
    'description' => [
        'required' => 'Please provide an activity description.',
        'max' => 'The description cannot exceed 1,500 characters.',
    ],
    'price' => [
        'required' => 'A price must be specified.',
        'numeric' => 'The price must be a number.',
        'min' => 'The price must be at least :min.',
    ],
    'session_duration' => [
        'required' => 'Please select a valid session duration.',
    ],
];
