<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Code;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        int $length = 5,
        ?string $timeToExpire = '15:m'
    ): Code {
        $query = $this->codes() instanceof Builder ?
            $this->codes()->withAttributes([
                'belongTo_id' => $this->number(),
                'belongTo_type' => $this::class,
            ]) :
            $this->codes();

        return $query->updateOrCreate(
            ['type' => $type],
            [
                'code' => $this->generate($type, $length),
                'expire_at' => $this->expireAt($timeToExpire),
            ]
        );
    }

    /**
     * @param  int|string|Code  $param
     * */
    public function deleteCode($param): bool|int
    {
        if ($param instanceof Code) {
            return $param->delete();
        }

        $query = $this->codes()
            ->where('type', $param)
            ->orWhere('id', $param);
        if ($query->exists()) {
            return $query->delete();
        }

        return false;
    }

    private function generate(string $type, int $length): int
    {
        $query = $this->codes();
        for ($i = 0; $i < 10; $i++) {
            $code = $this->number($length);
            if (
                mb_strlen((string) $code) === $length &&
                ! $query->where([['type', $type], ['code', $code]])->exists()
            ) {
                break;
            }
        }

        return (int) $code;
    }

    private function number(int $length = 5): int
    {
        Truthy($length <= 3, 'min code Length is 3.');
        $min = (int) pow(10, $length - 1);
        $max = (int) (pow(10, $length) - 1);

        return random_int($min, $max);
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
