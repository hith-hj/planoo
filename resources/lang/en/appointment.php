<?php

declare(strict_types=1);

return [

    'appointment_id' => [
        'required' => 'Please select a valid appointment reference.',
        'exists' => 'The selected appointment record does not exist.',
    ],
    'activity_id' => [
        'required' => 'An activity choice is required.',
        'exists' => 'The chosen activity option is invalid.',
    ],
    'day_id' => [
        'required' => 'Please pick an execution day.',
        'exists' => 'The selected weekday calendar route is invalid.',
    ],
    'date' => [
        'required' => 'An active calendar booking date is mandatory.',
        'date' => 'The system requires a clean calendar format standard.',
    ],
    'session_duration' => [
        'required' => 'Please specify a valid timing window allocation.',
    ],
    'code' => [
        'required' => 'The system identifier security code cannot be blank.',
    ],
    'time' => [
        'required' => 'An absolute start time schedule entry is required.',
        'regex' => 'Time slots must align specifically to hour or half-hour blocks (HH:00 or HH:30).',
    ],
    'notes' => [
        'string' => 'The remarks context section must only contain valid character lines.',
        'max' => 'Internal scheduling logs cannot exceed 500 characters total.',
    ],
    'customer_id' => [
        'required' => 'A valid consumer link parameter is required.',
        'exists' => 'No client profile data matches that identification record.',
        'required_without' => 'Please attach either a customer registration account link or a mobile number.',
    ],
    'customer_phone' => [
        'regex' => 'The primary customer mobile tracking layout format is incorrect.',
        'required_without' => 'Provide either a phone number line entry or map a known profile record parameter.',
    ],

];
