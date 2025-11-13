<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\NotificationTypes;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

trait NotificationsHandler
{
    private string $title = '';

    private string $body = '';

    private array $data = [];

    private string $className = '';

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'belongTo');
    }

    public function notify(
        string $title = '',
        string $body = '',
        array $data = [],
        string $provider = 'fcm'
    ): bool {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->className = class_basename($this::class)."[{$this->id}]";
        if (app()->environment(['testing', 'local'])) {
            $this->store(['result' => 'testing notification']);
            Log::info("Notification {$this->className} : {$this->body}");

            return true;
        }

        if ($this->hasIsNotifiable() && ! $this->isNotifiable()) {
            $this->store(['result' => 'silent notification']);
            Log::info(" {$this->className} is not notifiable");

            return true;
        }

        return match ($provider) {
            'fcm' => $this->fcm(),
            'sms' => $this->sms(),
            'email' => $this->email(),
            default => $this->fcm(),
        };
    }

    public function fcm(): bool
    {
        if ($this->firebase_token === null) {
            Log::error("No FCM token on {$this->className}");

            return true;
        }
        $factory = (new FcmFactory)->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $notification = ['title' => $this->title, 'body' => $this->body];
        $message = CloudMessage::new()->toToken($this->firebase_token)
            ->withNotification(FcmNotification::fromArray($notification))
            ->withAndroidConfig($this->getFCMAndroidConfig())
            ->withData(MessageData::fromArray($this->data));

        try {
            $res = $messaging->send($message);
            $this->store(['result' => $res]);

            return true;
        } catch (MessagingException) {
            return false;
        }
    }

    private function store(array $extra): Notification
    {
        Truthy(! method_exists($this, 'notifications'), "{$this->className} Missing notifications() method");

        return $this->notifications()->create([
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->data['type'] ?? NotificationTypes::normal->value,
            'payload' => json_encode([
                ...$this->data,
                ...$extra,
            ]),
            'is_viewed' => false,
        ]);
    }

    private function sms(): bool
    {
        return true;
    }

    private function email(): bool
    {
        return true;
    }

    private function getFCMCredentials(): string
    {
        Truthy(! file_exists(storage_path('app/fcm.json')), 'Missing firebase config file');

        return storage_path('app/fcm.json');
    }

    private function getFCMAndroidConfig(): object
    {
        return AndroidConfig::fromArray([
            'ttl' => '1800s',
            'priority' => 'high',
            'notification' => [
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
                'sound' => 'default',
            ],
        ]);
    }

    private function hasIsNotifiable(): bool
    {
        if (in_array('is_notifiable', array_keys($this->toArray()))) {
            return true;
        }

        return false;
    }

    private function isNotifiable(): bool
    {
        return (bool) $this->is_notifiable;
    }
}
