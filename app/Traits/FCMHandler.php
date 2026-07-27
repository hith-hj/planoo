<?php

declare(strict_types=1);

namespace App\Traits;

use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

trait FCMHandler
{
    private function sendFCM(string $firebase_token, string $title, string $body, array $data): mixed
    {
        Truthy($firebase_token === null, 'firebase token is missing');

        $factory = (new FcmFactory)->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $notification = ['title' => $title, 'body' => $body];
        $message = CloudMessage::new()->toToken($firebase_token)
            ->withNotification(FcmNotification::fromArray($notification))
            ->withAndroidConfig($this->getFCMAndroidConfig())
            ->withData(MessageData::fromArray($data));

        return $messaging->send($message);
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
}
