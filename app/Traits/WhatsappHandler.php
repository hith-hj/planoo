<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

trait WhatsappHandler
{
    protected string $token;
    protected string $baseUrl;

    /**
     * Send system-initiated Template Message
     */
    public function waTemplet(
        string $to,
        string $templateName = 'wa_msg',
        array $components = [],
        string $languageCode = 'ar_SA'
    ): Response {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ]
            ]
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->request($payload);
    }

    /**
     * Send Text Message (Only inside 24h session window)
     */
    public function waText(
        string $to,
        string $text
    ): Response {
        return $this->request([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $text
            ]
        ]);
    }

    private function setSettings(): void
    {
        $config = config('services.whatsapp');

        $this->token = $config['token'];
        $this->baseUrl = "https://facebook.com/{$config['version']}/{$config['phone_number_id']}/messages";
    }

    /**
     * Internal abstraction for making the HTTP Call
     */
    protected function request(array $payload): Response
    {
        $this->setSettings();
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl, $payload);

        if ($response->failed()) {
            Log::error('WhatsApp API Failure', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            $response->throw();
        }

        return $response;
    }
}
