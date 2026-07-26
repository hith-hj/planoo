<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

trait SMSHandler
{
    protected string $syriatelBaseUrl = 'https://bms.syriatel.sy/API/SendTemplateSMS.aspx';

    protected array $syriatelTemplates = ['arabic' => 'PlanooApp_T1', 'english' => 'PlanooApp_T2'];

    protected function smsRequest(string $url): Response
    {
        $response = Http::asForm()
            ->acceptJson()
            ->post($url);

        if ($response->failed()) {
            Log::error('SMS API Failure', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        return $response;
    }

    public function sendSMS(
        ?string $phone = null,
        string $code = '000000',
        string $language = 'english',
    ): mixed {
        Truthy($phone === null, 'sms phone is missing');
        Truthy(! isset($code), 'otp code is missing');

        return $this->smsRequest($this->buildUrl($phone, (int) $code, $language));
    }

    private function setPhone(string $phone)
    {
        $trimedPhone = trim($phone);

        if (Str::startsWith($trimedPhone, '0')) {
            $formattedPhone = '963'.mb_substr($trimedPhone, 1);
        } else {
            $formattedPhone = $trimedPhone;
        }

        return $formattedPhone;
    }

    private function buildUrl(string $phone, int $code, string $language): string
    {
        if (! array_key_exists($language, $this->syriatelTemplates)) {
            $language = 'english';
        }

        return Uri::of($this->syriatelBaseUrl)->withQuery([
            'user_name' => config('services.syriatel.user_name', 'PlanooApp1'),
            'password' => config('services.syriatel.password'),
            'sender' => config('services.syriatel.sender', 'PlanooApp'),
            'template_code' => $this->syriatelTemplates[$language],
            'param_list' => (int) $code,
            'to' => $this->setPhone($phone),
        ])->decode();
    }
}
