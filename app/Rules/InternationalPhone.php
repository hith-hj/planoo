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
            $fail('The :attribute must be a valid international phone number.');

            return;
        }
        $cleanPhone = preg_replace('/[^0-9]/', '', $value);
        $totalLength = mb_strlen($cleanPhone);

        foreach (countryCodesLengths() as $prefix => $allowedLengths) {
            if (str_starts_with($value, $prefix)) {

                if (! is_array($allowedLengths)) {
                    $allowedLengths = [$allowedLengths];
                }

                if (! in_array($totalLength, $allowedLengths)) {
                    $fail("The phone number length is invalid for country code {$prefix}.");
                }
                break;
            }
        }
    }
}
