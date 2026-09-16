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
    private array $discardKeys = ['conflicts'];

    private function sendFCM(?string $firebase_token, string $title, string $body, array $data): mixed
    {
        Truthy($firebase_token === null, 'firebase token is missing');

        $factory = (new FcmFactory)->withServiceAccount($this->getFCMCredentials());
        $messaging = $factory->createMessaging();
        $notification = ['title' => $title, 'body' => $body];
        $data = $this->safeFcmDataArray($data);
        $message = CloudMessage::new()->toToken($firebase_token)
            ->withNotification(FcmNotification::fromArray($notification))
            ->withAndroidConfig($this->getFCMAndroidConfig())
            ->withData(MessageData::fromArray($data));

        return $messaging->send($message);
    }

    private function safeFcmDataArray(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->discardKeys, true)) {
                continue;
            }

            $processedValue = is_array($value) ? json_encode($value) : (string) $value;
            if (mb_strlen($processedValue, '8bit') > 500) {
                continue;
            }

            // 4. Look ahead: Create a temporary test with the new item included
            $test = $newData;
            $test[$key] = $processedValue;

            // 5. If this new item pushes the total payload over 4000 bytes, STOP and return immediately
            if (mb_strlen(json_encode($test), '8bit') > 4000) {
                return $newData;
            }

            $newData[$key] = $processedValue;
        }

        return $newData;
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
