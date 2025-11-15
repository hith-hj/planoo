<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\CodesTypes;
use App\Enums\NotificationTypes;
use Exception;

trait VerificationHandler
{
    public function verify(string $codeType = CodesTypes::verification->name, string $by = 'fcm'): static
    {
        $this->checkFields();
        $this->checkMethods();
        $code = $this->createCode(type: $codeType, timeToExpire: '15:m');
        $this->update([
            'verified_at' => null,
            'verified_by' => $by,
        ]);

        $this->notify(
            title: "$codeType code",
            body: "Your code: $code->code, expire at {$code->expire_at->format('Y-m-d H:i')}",
            data: ['type' => NotificationTypes::verification->value, 'code' => $code->code],
            provider: $by
        );

        return $this;
    }

    public function verified(string $codeType = CodesTypes::verification->name): static
    {
        $this->checkFields();
        $this->checkMethods();
        $this->deleteCode($codeType);
        $this->touch('verified_at');

        return $this;
    }

    private function checkFields()
    {
        if (count(array_diff(['verified_at', 'verified_by'], array_keys($this->toArray()))) !== 0) {
            throw new Exception(class_basename($this::class).' missing verification fields');
        }
    }

    private function checkMethods()
    {
        if (! method_exists($this, 'code')) {
            throw new Exception(class_basename($this::class).' missing codes Handler');
        }

        if (! method_exists($this, 'notify')) {
            throw new Exception(class_basename($this::class).' missing notifications Handler');
        }
    }
}
