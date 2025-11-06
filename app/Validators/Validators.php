<?php

declare(strict_types=1);

namespace App\Validators;

use App\Interfaces\ValidatorsInterface;

class Validators implements ValidatorsInterface
{
    /**
     * Authorize if the validator should validate the data
     * @param bool| callable $condition
     * @return static
     * @throws Exception
     * */
    public static function authorize(bool|callable $condition = false): static
    {
        $res = $condition;
        if (is_callable($condition)) {
            $res = $condition();
        }
        Falsy((bool) $res, 'Authorization Failed');

        return new static();
    }
}
