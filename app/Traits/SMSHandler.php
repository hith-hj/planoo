<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

trait SMSHandler
{
    protected string $syriatelBaseUrl = 'https://bms.syriatel.sy/API/SendTemplateSMS.aspx';

    protected array $syriatelTemplates = ['arabic' => 'PlanooApp_T1', 'english' => 'PlanooApp_T2'];

    public function sendSMS(
        ?string $phone = null,
        string|int $code = '000000',
    ): mixed {
        Truthy($phone === null, 'sms phone is missing');
        Truthy(! isset($code), 'otp code is missing');
        if (! Str::startsWith($phone, '+963')) {
            return false;
        }

        return $this->SyriatelSMSRequest($this->buildSyriatelUrl($phone, (int) $code));
    }

    private function buildSyriatelUrl(string $phone, int $code): string
    {
        $language = $this->getLanguage();
        if (! array_key_exists($language, $this->syriatelTemplates)) {
            $language = 'english';
        }

        return Uri::of($this->syriatelBaseUrl)->withQuery([
            'user_name' => config('services.syriatel.user_name', 'PlanooApp1'),
            'password' => config('services.syriatel.password'),
            'sender' => config('services.syriatel.sender', 'PlanooApp'),
            'template_code' => $this->syriatelTemplates[$language],
            'param_list' => (int) $code,
            'to' => formatPhone($phone),
        ])->decode();
    }

    private function SyriatelSMSRequest(string $url): Response
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

    private function getLanguage(): string
    {
        $languages = ['ar' => 'arabic', 'en' => 'english'];

        $locale = Request::header('Accept-Language')
            ?? Request::header('X-Language')
            ?? 'en';
        $shortLocale = mb_substr(mb_strtolower(trim($locale)), 0, 2);

        return $languages[$shortLocale] ?? 'english';
    }
}
