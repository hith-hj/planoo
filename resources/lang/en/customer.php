<?php

declare(strict_types=1);

return [
    'customer_id' => [
        'required' => 'The customer ID is required.',
        'exists' => 'The selected customer does not exist.',
    ],
    'name' => [
        'required' => 'The name is required.',
        'string' => 'The name must be a valid string.',
        'max' => 'The name must not exceed :max characters.',
    ],
    'profile_image' => [
        'required' => 'The profile image is required.',
        'image' => 'The uploaded file must be an image.',
        'mimetypes' => 'The image must be in jpeg or png format only.',
        'max' => 'The image size must not exceed :max kilobytes.',
    ],
    'phone' => [
        'required' => 'The phone number is required.',
        'regex' => 'The phone number format is invalid.',
        'unique' => 'This phone number is already registered.',
        'exists' => 'This phone number is not registered with us.',
    ],
    'password' => [
        'required' => 'The password is required.',
        'min' => 'The password must be at least :min characters.',
        'confirmed' => 'The password confirmation does not match.',
    ],

    'email' => [
        'required' => 'The email is required.',
        'email' => 'The Email must be a valid email address.',
        'unique' => 'This email address is already registered.',
    ],

    'gender' => [
        'required' => 'Gender is required.',
        'in' => 'The selected gender is invalid.',
    ],

    'birthdate' => [
        'required' => 'The Birthdate is required.',
        'date' => 'The date must strictly follow the format: YYYY-MM-DD.',
        'before_or_equal' => 'Age must be older than '.Setting('minimum_customer_age', 14),
    ],

    'firebase_token' => [
        'required' => 'The Firebase device token is required.',
    ],
    'code' => [
        'required' => 'The verification code is required.',
        'numeric' => 'The verification code must be numbers only.',
        'exists' => 'This verification code is invalid or expired.',
    ],
    'old_password' => [
        'required' => 'The old password is required.',
        'min' => 'The old password must be at least :min characters.',
    ],
    'new_password' => [
        'required' => 'The new password is required.',
        'min' => 'The new password must be at least :min characters.',
        'confirmed' => 'The new password confirmation does not match.',
    ],
];
