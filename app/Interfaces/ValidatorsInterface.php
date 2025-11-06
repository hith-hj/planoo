<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ValidatorsInterface
{
    public static function authorize(bool|callable $arg): static;
}
