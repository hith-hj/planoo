<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\NotificationTypes;
use App\Models\Notification;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait NotificationsHandler
{
    use FCMHandler;
    use SMSHandler;
    use WhatsappHandler;

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
            'whatsapp' => $this->whatsapp(),
            default => $this->fcm(),
        };
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

    private function fcm(): bool
    {
        try {
            $res = $this->sendFCM($this->fcmToken(), $this->title, $this->body, $this->data);
            $this->store(['result' => $res]);

            return true;
        } catch (Exception $e) {
            Log::error("FCM notification error {$e->getMessage()}");

            return false;
        }
    }

    private function whatsapp(): bool
    {
        try {
            $res = $this->sendWA($this->phone(), $this->body, $this->data);
            $notification = $this->store(['result' => ['wamid' => $res->json('messages.0.id')]]);
            $payload = [
                'id' => $this->id,
                'wamid' => $res->json('messages.0.id'),
                'phone' => $this->phone(),
                'code' => $this->data['code'],
                'notification_id' => $notification->id,
            ];
            Cache::add("wa_msg_{$res->json('messages.0.id')}", $payload, now()->addMinutes(10));

            return true;
        } catch (Exception $e) {
            Log::error("Whatsapp message error {$e->getMessage()}");

            return false;
        }
    }

    private function sms($language = 'english'): bool
    {
        try {
            $res = $this->sendSMS(
                phone: $this->phone(),
                code: $this->data['code'],
                language: $language,
            );
            $this->store(['result' => ['status' => 'send', 'sms_req_id' => $res->body()]]);

            return true;
        } catch (Exception $e) {
            Log::error("SMS message error {$e->getMessage()}");

            return false;
        }
    }

    private function email(): bool
    {
        return true;
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

    private function phone(): mixed
    {
        if ($this->phone !== null) {
            return $this->phone;
        }
        Log::error("No phone number on {$this->className}");

        return null;
    }

    private function fcmToken(): mixed
    {
        if ($this->firebase_token !== null) {
            return $this->firebase_token;
        }
        Log::error("No FCM token on {$this->className}");

        return null;
    }
}
