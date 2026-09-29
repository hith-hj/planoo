<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Factory as FcmFactory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage; // Token not registered / expired
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

        try {
            return $messaging->send($message);
        } catch (NotFound $e) {
            $this->handleInvalidToken($firebase_token, 'unregistered');

            return null;
        } catch (InvalidArgument $e) {
            $this->handleInvalidToken($firebase_token, 'malformed');

            return null;
        } catch (Exception $e) {
            Log::error('FCM Core Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Custom hook to clean up bad tokens from your system.
     */
    private function handleInvalidToken(string $token, string $reason): void
    {
        // 1. Log it for clear system tracking
        Log::warning("FCM token discarded. Reason: {$reason}. Token: {$token}");

        // 2. Clear from database
        $this->update(['fcm_token' => null]);
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

            $test = $newData;
            $test[$key] = $processedValue;

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
