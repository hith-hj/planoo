<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

final class DeliverNotification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public Model $recipient,
        public string $title,
        public string $body,
        public array $data,
        public string $provider,
    ) {
        $this->recipient = $recipient->withoutRelations();
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        if (! $this->recipient->deliverNotification($this->title, $this->body, $this->data, $this->provider)) {
            throw new RuntimeException('Notification delivery failed.');
        }
    }
}
