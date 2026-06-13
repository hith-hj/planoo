<?php

declare(strict_types=1);

return [

    'name' => [
        'required' => 'The name field is required.',
        'string' => 'The name must be a text value.',
        'max' => 'The name cannot exceed :max characters.',
    ],
    'description' => [
        'required' => 'The profile description is required.',
        'string' => 'The description must be a text value.',
        'max' => 'The description cannot exceed :max characters.',
    ],
    'profile_image' => [
        'required' => 'A profile image must be uploaded.',
        'image' => 'The uploaded file must be an image format.',
        'mimetypes' => 'Only JPEG and PNG file types are allowed.',
        'max' => 'The image filesize cannot exceed :max kilobytes.',
    ],

    'email' => [
        'required' => 'The email address is required.',
        'string' => 'The email must be a text value.',
        'email' => 'Please provide a valid email address.',
        'max' => 'The email cannot exceed :max characters.',
        'unique' => 'This email address is already registered.',
    ],
    'phone' => [
        'required' => 'The phone number is required.',
        'regex' => 'The phone number format is invalid.',
        'unique' => 'This phone number is already registered.',
        'exists' => 'This phone number could not be found in our records.',
    ],
    'password' => [
        'required' => 'The password is required.',
        'string' => 'The password must be a text value.',
        'min' => 'The password must be at least :min characters.',
        'confirmed' => 'The password confirmation does not match.',
    ],
    'account_type' => [
        'required' => 'An account type selection is required.',
        'in' => 'The selected account type is invalid.',
    ],
    'firebase_token' => [
        'required' => 'The Firebase device registration token is required.',
    ],
    'code' => [
        'required' => 'The verification security code is required.',
        'numeric' => 'The verification code must contain numbers only.',
        'exists' => 'This verification code is invalid or has expired.',
    ],
    'old_password' => [
        'required' => 'Your current password is required.',
        'string' => 'The current password must be a text value.',
        'min' => 'The current password must be at least :min characters.',
    ],
    'new_password' => [
        'required' => 'A new password entry is required.',
        'string' => 'The new password must be a text value.',
        'min' => 'The new password must be at least :min characters.',
        'confirmed' => 'The new password confirmation match has failed.',
    ],

];
