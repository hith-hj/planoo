<?php

declare(strict_types=1);

return [
    'course_id' => [
        'required' => 'Please select a course.',
        'exists' => 'The chosen course does not exist.',
    ],
    'category_id' => [
        'required' => 'A category selection is mandatory.',
        'exists' => 'The selected category is invalid.',
    ],
    'name' => [
        'required' => 'The course title cannot be empty.',
    ],
    'description' => [
        'required' => 'Please fill out the course description summary.',
        'max' => 'Descriptions cannot exceed 1,500 characters.',
    ],
    'price' => [
        'required' => 'A standard purchase price must be provided.',
        'numeric' => 'The price has to be a numeric string value.',
        'min' => 'The pricing cannot drop below :min.',
    ],
    'course_duration' => [
        'required' => 'A valid duration schedule choice is required.',
    ],
    'capacity' => [
        'required' => 'Maximum class seating capacity must be specified.',
        'numeric' => 'Seating count limits must be standard digits.',
        'min' => 'Capacity must support at least :min attendee.',
        'max' => 'Capacity cannot exceed our facility rules limit of :max.',
    ],
    'cancellation_fee' => [
        'numeric' => 'The cancellation fee requires a positive numerical amount.',
        'min' => 'Fees must equal or surpass :min.',
    ],
    'start_date' => [
        'required' => 'A launch date schedule is mandatory.',
        'date_format' => 'The launch window must strictly follow the format: YYYY-MM-DD.',
    ],
    'customer_id' => [
        'required' => 'An active client link parameter is required.',
        'exists' => 'No active customer matched that reference record.',
        'required_without' => 'A valid client account identity or phone number must be added.',
    ],
    'customer_phone' => [
        'regex' => 'The mobile layout syntax format is incorrect.',
        'unique' => 'This mobile contact address is already registered to another account.',
        'required_without' => 'Please input either a customer link record reference or a phone entry line.',
    ],

];
