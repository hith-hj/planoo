<?php

declare(strict_types=1);

return [
    'event_id' => [
        'required' => 'The event identifier is required.',
        'exists' => 'The selected event is invalid or does not exist.',
    ],
    'category_id' => [
        'required' => 'An event category is required.',
        'exists' => 'The chosen event category is invalid.',
    ],
    'name' => [
        'required' => 'The event name is required.',
        'string' => 'The event name must be text.',
    ],
    'description' => [
        'required' => 'The event description is required.',
        'string' => 'The event description must be text.',
        'max' => 'The description text cannot exceed :max characters.',
    ],
    'event_duration' => [
        'required' => 'The event duration is required.',
        'numeric' => 'The duration must be a valid number.',
        'min' => 'The duration must be at least :min.',
        'max' => 'The duration cannot exceed the allowed limit of :max.',
    ],
    'capacity' => [
        'required' => 'The event capacity limit is required.',
        'numeric' => 'The capacity must be a valid number.',
        'min' => 'The capacity must be at least :min.',
        'max' => 'The capacity cannot exceed the structural limit of :max.',
    ],
    'admission_fee' => [
        'numeric' => 'The admission fee must be a valid number.',
        'min' => 'The admission fee must be at least :min.',
    ],
    'withdrawal_fee' => [
        'numeric' => 'The withdrawal fee must be a valid number.',
        'min' => 'The withdrawal fee must be at least :min.',
    ],
    'start_date' => [
        'required' => 'The event start date is required.',
        'date_format' => 'The start date format must strictly be YYYY-MM-DD.',
    ],
    'customer_id' => [
        'required' => 'The customer ID is required.',
        'exists' => 'The chosen customer record does not exist.',
        'required_without' => 'Please provide either a customer ID or a customer phone number.',
    ],
    'customer_phone' => [
        'regex' => 'The customer phone number format is invalid.',
        'unique' => 'This customer phone number has already taken a spot.',
        'required_without' => 'Please provide either a customer phone number or a customer ID.',
    ],

];
