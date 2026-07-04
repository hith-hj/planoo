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
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

trait NotificationsHandler
{
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
        $this->className = class_basename($this::class) . "[{$this->id}]";
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

    private function whatsapp(): bool
    {
        if ($this->phone === null) {
            Log::error("No phone number on {$this->className}");

            return true;
        }

        try {
            $response = $this->waTemplet(
                to: $this->getPhoneNumberForWhatsapp(),
                components: $this->getWhatsappMessageComponents()
            );
            $notification = $this->store(['result' => ['wamid' => $response->json('messages.0.id'),]]);
            $payload = [
                'id' => $this->id,
                'wamid' => $response->json('messages.0.id'),
                'phone' => $this->phone,
                'code' => $this->data['code'],
                'notification_id' => $notification->id,
            ];
            Cache::add("wa_msg_{$response->json('messages.0.id')}", $payload, now()->addMinutes(10));

            return true;
        } catch (RequestException $e) {
            return false;
        }
    }

    private function getPhoneNumberForWhatsapp(): string
    {
        $number = (string) $this->phone;
        if (str_contains($number, '+')) {
            return substr($number, 0, 1);
        }
        if (str_contains($number, '00')) {
            return substr($number, 0, 2);
        }
        return $number;
    }

    private function getWhatsappMessageComponents(): array
    {
        return [
            // 1. Map the code to {{1}} in the body text
            [
                'type' => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $this->body
                    ]
                ]
            ],
            // 2. Map the code to the "Copy Code" button url suffix parameter
            [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0', // Refers to the first button in your template layout
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $this->data['code']
                    ]
                ]
            ]
        ];
    }

    private function sms(): bool
    {
        return true;
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
}
