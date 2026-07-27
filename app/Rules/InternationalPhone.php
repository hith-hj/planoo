<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class InternationalPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\+[1-9]\d{6,14}$/', $value)) {
            $fail('The :attribute must be a valid international phone number starting with + (e.g., +12025550143).');

            return;
        }

        $countryLengthMap = [
            '+963' => 9, // syria
            '+971' => 7, // uae
            '+1' => 12, // usa
            '+44' => 13, // britin
            '+33' => 12, // idk
            '+49' => [12, 14], // germany
        ];

        $totalLength = mb_strlen($value);

        foreach ($countryLengthMap as $prefix => $allowedLength) {
            if (str_starts_with($value, $prefix)) {
                if (is_array($allowedLength)) {
                    if ($totalLength < $allowedLength[0] || $totalLength > $allowedLength[1]) {
                        $fail("The phone number length for this country must be between {$allowedLength[0]} and {$allowedLength[1]} characters.");
                    }
                } else {
                    if ($totalLength !== $allowedLength) {
                        $fail("The phone number length for this country must be exactly {$allowedLength} characters.");
                    }
                }
                break;
            }
        }
    }
}
