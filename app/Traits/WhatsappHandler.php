<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait WhatsappHandler
{
    protected string $waToken;

    protected string $waBaseUrl;

    public function sendWA(string $phone, string $body = '', array $data = [], string $template = 'template')
    {
        Truthy($phone === null, 'wa phone is missing');

        return match ($template) {
            'template' => $this->waTemplet(
                to: globalPhone($phone),
                components: $this->getWAMessageComponents($body, $data)
            ),
            'text' => $this->waText(
                to: globalPhone($phone),
                text: $this->body
            )
        };
    }

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
                    'code' => $languageCode,
                ],
            ],
        ];

        if (! empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->waRequest($payload);
    }

    /**
     * Send Text Message (Only inside 24h session window)
     */
    public function waText(
        string $to,
        string $text
    ): Response {
        return $this->waRequest([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $text,
            ],
        ]);
    }

    private function setSettings(): void
    {
        $config = config('services.whatsapp');

        $this->waToken = $config['token'];
        $this->waBaseUrl = "https://facebook.com/{$config['version']}/{$config['phone_number_id']}/messages";
    }

    private function getWAMessageComponents(string $body = '', array $data = []): array
    {
        if (empty($data)) {
            return [];
        }

        return [
            // 1. Map the code to {{1}} in the body text
            [
                'type' => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $body,
                    ],
                ],
            ],
            // 2. Map the code to the "Copy Code" button url suffix parameter
            [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0', // Refers to the first button in your template layout
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $data['code'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Internal abstraction for making the HTTP Call
     */
    private function waRequest(array $payload): Response
    {
        $this->setSettings();
        $response = Http::withToken($this->waToken)
            ->acceptJson()
            ->asJson()
            ->post($this->waBaseUrl, $payload);

        if ($response->failed()) {
            Log::error('WhatsApp API Failure', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $response->throw();
        }

        return $response;
    }
}
