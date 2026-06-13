<?php

declare(strict_types=1);

return [
    'day_id' => [
        'required' => 'The day identifier is required.',
        'exists' => 'The selected day is invalid.',
    ],
    'day' => [
        'required' => 'The day name field is required.',
        'string' => 'The day must be a text value.',
        'in' => 'The selected day must be a valid day of the week.',
    ],
    'start' => [
        'required' => 'The start time is required.',
        'regex' => 'The start time must match a 24-hour format on the hour or half-hour (e.g., 14:00 or 14:30).',
        'date_format' => 'The start time must match the format HH:MM.',
    ],
    'end' => [
        'required' => 'The end time is required.',
        'regex' => 'The end time must match a 24-hour format on the hour or half-hour (e.g., 18:00 or 18:30).',
        'date_format' => 'The end time must match the format HH:MM.',
    ],
    'days' => [
        'required' => 'The days list field is required.',
        'array' => 'The days must be provided as a structured list.',
        'min' => 'You must provide at least :min day item.',
    ],
    'days.*.day' => [
        'required' => 'The day name is required for all entries.',
        'string' => 'The day name entry must be a valid string.',
        'in' => 'One or more of the selected days are not valid days of the week.',
    ],
    'days.*.start' => [
        'required' => 'A start time is required for all entries.',
        'date_format' => 'The start time format must strictly match HH:MM.',
    ],
    'days.*.end' => [
        'required' => 'An end time is required for all entries.',
        'date_format' => 'The end time format must strictly match HH:MM.',
    ],

];
