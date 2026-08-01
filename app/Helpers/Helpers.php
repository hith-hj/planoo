<?php

declare(strict_types=1);

use App\Enums\SectionsTypes;
use App\Interfaces\Dayable;
use App\Interfaces\Locatable;
use App\Interfaces\Mediable;
use App\Interfaces\Reviewable;
use App\Interfaces\Taggable;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (! function_exists('Success')) {
    /**
     * Return Success JsonResponse
     */
    function Success(
        string $msg = 'Success',
        array $payload = [],
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => __("$msg"),
        ];
        if ($payload !== []) {
            $response['payload'] = $payload;
        }

        return response()->json($response, $code);
    }
}

if (! function_exists('Error')) {
    /**
     * Return Error JsonResponse
     */
    function Error(
        string $msg = 'Error',
        array $payload = [],
        int $code = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => __("$msg"),
        ];
        if ($payload !== []) {
            $response['payload'] = $payload;
        }

        return response()->json($response, $code);
    }
}

if (! function_exists('Exists')) {
    /**
     * check if argument exists
     * if true throw an exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    function Exists($argument, string $name = ''): void
    {
        if ($argument) {
            throw new Exception($name.' '.__('exists'), 400);
        }
    }
}

if (! function_exists('NotFound')) {
    /**
     * check if argument is empty
     * if true throw Not found exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    function NotFound($argument, $name = ''): void
    {
        if (
            ! $argument ||
            $argument === null ||
            empty($argument) ||
            (is_countable($argument) && count($argument) === 0)
        ) {
            throw new NotFoundHttpException(sprintf('%s %s', __("$name"), __('not found')));
        }
    }
}

if (! function_exists('Required')) {
    /**
     * check if argument is empty
     * if true throw required exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    function Required($argument, $name = ''): void
    {
        if (
            ! $argument ||
            $argument === null ||
            empty($argument) ||
            (is_countable($argument) && count($argument) === 0)
        ) {
            throw new Exception(sprintf('%s %s', __("$name"), __('is required')));
        }
    }
}

if (! function_exists('Truthy')) {
    /**
     * throw exception if the condition is true
     *
     * @param  bool  $condition
     * @param  string  $message
     * @param  mixed  $parameters
     *
     * @throws Exception
     */
    function Truthy($condition, $message, ...$parameters): bool
    {
        if ($condition) {
            throw new Exception(__("$message"), ...$parameters);
        }

        return (bool) $condition;
    }
}

if (! function_exists('Falsy')) {
    /**
     * throw exception if the condition is false
     *
     * @param  bool  $condition
     * @param  string  $message
     * @param  mixed  $parameters
     *
     * @throws Exception
     */
    function Falsy($condition, $message, ...$parameters): bool
    {
        if (! $condition) {
            throw new Exception(__("$message"), ...$parameters);
        }

        return (bool) $condition;
    }
}

if (! function_exists('getModelGlobal')) {
    /**
     * Retrieve an Eloquent model instance based on type and id.
     *
     * If type or id are not provided, they will be retrieved from the request.<br>
     * if any of the fields is not present in the request exception will be thrown.<br>
     * The function validates the model type, ensures the class exists,<br>
     *
     *
     * @throws Illuminate\Validation\ValidationException
     * @throws NotFoundHttpException
     */
    function getModelGlobal(?string $owner_type = null, ?int $owner_id = null): Taggable|Dayable|Mediable|Locatable|Reviewable|Model
    {
        $id = $owner_id ?? (int) request('owner_id');
        $type = $owner_type ?? request('owner_type');
        Truthy(is_null($type) || is_null($id), 'failed to retrieve model');
        Truthy(! in_array($type, SectionsTypes::names()), sprintf('%s : %s', __('invalid model type'), "$type"));
        $type = ucfirst($type);
        $class = "App\\Models\\{$type}";
        Truthy(! class_exists($class), sprintf('%s : %s', __('class does not exist'), "{$class}"));
        $model = $class::find($id);
        NotFound($model, sprintf('%s : %s', __('model not found'), "{$type} - {$id}"));

        return $model;
    }
}

if (! function_exists('getModel')) {
    function getModel(?string $owner_type = null, ?int $owner_id = null): Taggable|Dayable|Mediable|Locatable|Reviewable|Model
    {
        $model = getModelGlobal($owner_type, $owner_id);
        Truthy((int) $model->user_id !== (int) Auth::id(), 'unauthorized access');

        return $model;
    }
}

if (! function_exists('Setting')) {
    function Setting(?string $name = null, mixed $default = null)
    {
        if ($name !== null) {
            return Setting::where('name', $name)->first()?->value ?? $default;
        }

        return $default;
    }
}

if (! function_exists('checkAndCastData')) {
    /**
     * check if fields in requiredFields exists and data <br>
     * if true casts it to the provieded cast <br>
     * if false assign default value if provided <br>
     * if no default provided set as missing field <br>
     * */
    function checkAndCastData(array $data, array $requiredFields = []): array
    {
        Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = [];
        foreach ($requiredFields as $key => $value) {
            $value = trim($value);
            if (str_contains($value, '|')) {
                [$type, $default] = explode('|', $value);
                $value = $type;
                if (! isset($data[$key])) {
                    $data[$key] = $default;
                }
            }

            if (str_contains($key, '.')) {
                [$name, $sub] = explode('.', $key);
                if (! isset($data[$name][$sub])) {
                    $missing[] = $key;

                    continue;
                }
                settype($data[$name][$sub], $value);

                continue;
            }
            if (! isset($data[$key])) {
                $missing[] = $key;

                continue;
            }
            settype($data[$key], $value);
        }
        Falsy(empty($missing), sprintf('%s : %s', __('fields missing'), implode(', ', $missing)));

        return $data;
    }
}

if (! function_exists('globalPhone')) {
    function globalPhone(string $phone)
    {
        $trimmedPhone = trim($phone);

        if (Str::startsWith($trimmedPhone, '+')) {
            $trimmedPhone = mb_substr($trimmedPhone, 1);
        } elseif (Str::startsWith($trimmedPhone, '00')) {
            $trimmedPhone = mb_substr($trimmedPhone, 2);
        }

        return $trimmedPhone;
    }
}

if (! function_exists('countryCodesLengths')) {
    function countryCodesLengths(?string $code = null): int|array
    {
        $list = [
            '+1' => 10,           // USA, Canada, and NANP countries (3-digit area code + 7-digit local)
            '+7' => 10,           // Russia, Kazakhstan
            '+20' => [9, 10],       // Egypt
            '+27' => 9,            // South Africa
            '+30' => 10,           // Greece
            '+31' => 9,            // Netherlands
            '+32' => [8, 9],       // Belgium
            '+33' => 9,            // France
            '+34' => 9,            // Spain
            '+36' => 9,            // Hungary
            '+39' => [9, 10],      // Italy
            '+40' => 9,            // Romania
            '+41' => 9,            // Switzerland
            '+43' => [4, 5, 6, 7, 8, 9, 10, 11, 12, 13], // Austria (highly variable)
            '+44' => [9, 10],      // United Kingdom
            '+45' => 8,            // Denmark
            '+46' => [7, 8, 9],    // Sweden
            '+47' => 8,            // Norway
            '+48' => 9,            // Poland
            '+49' => [10, 11, 12, 13], // Germany
            '+51' => 9,            // Peru
            '+52' => 10,           // Mexico
            '+54' => 10,           // Argentina
            '+55' => [10, 11],     // Brazil
            '+56' => 9,            // Chile
            '+57' => 10,           // Colombia
            '+60' => [7, 8, 9],    // Malaysia
            '+61' => 9,            // Australia
            '+62' => [9, 10, 11],  // Indonesia
            '+63' => 10,           // Philippines
            '+64' => [8, 9, 10],   // New Zealand
            '+65' => 8,            // Singapore
            '+66' => 9,            // Thailand
            '+81' => [9, 10],      // Japan
            '+82' => [9, 10],      // South Korea
            '+84' => [9, 10],      // Vietnam
            '+86' => 11,           // China
            '+90' => 10,           // Turkey
            '+91' => 10,           // India
            '+92' => 10,           // Pakistan
            '+93' => 9,            // Afghanistan
            '+94' => 9,            // Sri Lanka
            '+95' => [8, 9, 10],   // Myanmar
            '+961' => [7, 8],       // Lebanon
            '+962' => [8, 9],       // Jordan
            '+963' => 9,            // Syria
            '+964' => 10,           // Iraq
            '+965' => 8,            // Kuwait
            '+966' => 9,            // Saudi Arabia
            '+967' => 9,            // Yemen
            '+968' => 8,            // Oman
            '+971' => 9,            // UAE (Standard mobile/fixed with area code, e.g., 50XXXXXXX)
            '+972' => 9,            // Israel
            '+973' => 8,            // Bahrain
            '+974' => 8,            // Qatar
            '+98' => 10,           // Iran
            '+994' => 9,            // Azerbaijan
        ];

        if ($code !== null && array_key_exists($code, $list)) {
            return $list[$code];
        }

        return $list;
    }
}
