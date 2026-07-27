<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidPhoneLength implements ValidationRule
{
    private ?string $countryCode;

    public function __construct(?string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->countryCode || ! array_key_exists($this->countryCode, countryCodesLengths())) {
            $fail('The selected country code is invalid or unsupported.');

            return;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $value);
        $phoneLength = mb_strlen($cleanPhone);

        $allowedLengths = countryCodesLengths($this->countryCode);
        if (! is_array($allowedLengths)) {
            $allowedLengths = [$allowedLengths];
        }

        if (! in_array($phoneLength, $allowedLengths)) {
            $fail("The phone number length is invalid for country code {$this->countryCode}.");
        }
    }
}
