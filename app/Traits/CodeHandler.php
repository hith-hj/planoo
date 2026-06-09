<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Code;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

trait CodeHandler
{
    public function codes(): MorphMany|Builder
    {
        if ($this instanceof Model) {
            return $this->morphMany(Code::class, 'belongTo');
        }

        return Code::query();
    }

    /**
     * Get code by type
     * */
    public function code(string $type): ?Code
    {
        $code = $this->codes()->where('type', $type)->first();
        Truthy($code === null, "Code[{$type}] not Found");

        return $code;
    }

    /**
     * Get code by id
     * */
    public function codeById(int $id): ?Code
    {
        $code = $this->codes()->find($id);
        Truthy($code === null, "Code[{$id}] not Found");

        return $code;
    }

    public function createCode(
        string $type = 'test',
        ?string $timeToExpire = '15:m'
    ): Code {
        $query = $this->codes() instanceof Builder ?
            $this->codes()->withAttributes([
                'belongTo_id' => random_int(10000000, 99999999),
                'belongTo_type' => $this::class,
            ]) :
            $this->codes();

        $maxAttempts = Setting('max_code_generation_attempts', 5);
        $length = (int) Setting('generated_code_length', 6);
        $attempt = 0;
        while ($attempt < $maxAttempts) {
            try {
                $attempt++;

                return $query->updateOrCreate(
                    ['type' => $type],
                    [
                        'code' => $this->generate($type, $length),
                        'expire_at' => $this->expireAt($timeToExpire),
                    ]
                );
            } catch (QueryException $e) {
                $isUniqueViolation = in_array($e->getCode(), [23000, 23005])
                    || str_contains($e->getMessage(), 'Integrity constraint violation');

                if ($isUniqueViolation && $attempt < $maxAttempts) {
                    continue;
                }
                throw $e;
            }
        }

        Truthy(true, "Could not generate a unique code after {$maxAttempts} attempts.");
    }

    /**
     * @param  int|string|Code  $param
     * */
    public function deleteCode($param): bool|int
    {
        if ($param instanceof Code) {
            return $param->delete();
        }

        $query = $this->codes()->where('type', $param)->orWhere('id', $param);
        if ($query->exists()) {
            return $query->delete();
        }

        return false;
    }

    private function generate(string $type, int $length = 6)
    {
        Truthy($length <= 3, 'min code Length is 3.');
        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        $existingCodes = $this->codes()
            ->where('type', $type)
            ->pluck('code')
            ->toArray();

        for ($i = 0; $i < 100; $i++) {
            $code = random_int($min, $max);

            if (! in_array($code, $existingCodes, true)) {
                return $code;
            }
        }

        Truthy(true, "Failed to generate a unique {$length}-digit code after 100 attempts");
    }

    private function expireAt(?string $timeToExpire): Carbon
    {
        if ($timeToExpire === null) {
            return now();
        }
        [$value, $unit] = explode(':', $timeToExpire);
        if (! in_array($unit, ['s', 'm', 'h', 'd'])) {
            return now();
        }
        $units = ['s' => 'second', 'm' => 'minute', 'h' => 'hour', 'd' => 'day'];

        return now()->add((string) $units[$unit], (int) $value);
    }
}
