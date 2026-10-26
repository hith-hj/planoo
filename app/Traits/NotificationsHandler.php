<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\NotificationTypes;
use App\Jobs\DeliverNotification;
use App\Models\Notification;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Throwable;

trait NotificationsHandler
{
    use FCMHandler;
    use SMSHandler;
    use WhatsappHandler;

    private string $title = '';

    private string $body = '';

    private array $data = [];

    private string $className = '';

    private ?Throwable $notificationDeliveryException = null;

    public function notificationDeliveryException(): ?Throwable
    {
        return $this->notificationDeliveryException;
    }

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
        if (! app()->environment(['testing', 'local']) && config('services.notifications.queued', true)) {
            DeliverNotification::dispatch($this, $title, $body, $data, $provider);

            return true;
        }

        return $this->deliverNotification($title, $body, $data, $provider);
    }

    public function deliverNotification(
        string $title = '',
        string $body = '',
        array $data = [],
        string $provider = 'fcm'
    ): bool {
        $this->notificationDeliveryException = null;
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
            $token = $this->fcmToken();
            if ($token === null) {
                $this->store(['result' => 'push skipped: missing firebase token']);

                return true;
            }

            $res = $this->sendFCM($token, $this->title, $this->body, $this->data);
            $this->store(['result' => $res]);

            return true;
        } catch (Exception $e) {
            $this->notificationDeliveryException = $e;
            Log::error("FCM notification error {$e->getMessage()}");

            return false;
        }
    }

    private function whatsapp(): bool
    {
        try {
            $res = $this->sendWA($this->phone(), $this->body, $this->data);
            $this->store(['result' => ['wamid' => $res->json('messages.0.id')]]);

            return true;
        } catch (Exception $e) {
            Log::error("Whatsapp message error {$e->getMessage()}");

            return $this->fcm();
        }
    }

    private function sms(): bool
    {
        try {
            $res = $this->sendSMS(
                phone: $this->phone(),
                code: $this->data['code'],
            );
            $this->store(['result' => ['status' => 'sent', 'response' => $res->body()]]);

            return true;
        } catch (Exception $e) {
            Log::error("SMS message error {$e->getMessage()}");

            return $this->fcm();
        }
    }

    private function email(): bool
    {
        // No mailer integration is configured yet: persist the notification
        // record so the message stays auditable instead of being dropped.
        try {
            $this->store(['result' => 'email provider not configured']);

            return true;
        } catch (Exception $e) {
            $this->notificationDeliveryException = $e;
            Log::error("Email notification error {$e->getMessage()}");

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
            'payload' => [
                ...$this->data,
                ...$extra,
            ],
            'is_viewed' => false,
        ]);
    }

    private function phone(): mixed
    {
        if ($this->phone !== null && $this->country_code !== null) {
            return $this->country_code.formatPhone($this->phone);
        }
        Log::error("No valid phone number on {$this->className}");

        return null;
    }

    private function fcmToken(): mixed
    {
        if ($this->firebase_token !== null) {
            if (! str_starts_with($this->firebase_token, 'fake_')) {
                return $this->firebase_token;
            }

            return null;
        }
        Log::error("No FCM token on {$this->className}");

        return null;
    }
}
