<?php

declare(strict_types=1);

use App\Enums\SectionsTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (! function_exists('Success')) {
    function Success(
        string $msg = 'Success',
        array $payload = [],
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $msg,
        ];
        if ($payload !== []) {
            $response['payload'] = $payload;
        }

        return response()->json($response, $code);
    }
}

if (! function_exists('Error')) {
    function Error(
        string $msg = 'Error',
        array $payload = [],
        int $code = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $msg,
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
     * if true throw not found exception
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
     * @param  mixed  $name
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
     * @param  mixed  $name
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

if (! function_exists('getModel')) {
    /**
     * Retrieve an Eloquent model instance based on type and id.
     *
     * If type or id are not provided, they will be retrieved from the request.<br>
     * if any of the fields is not present in the request exception will be thrown.<br>
     * The function validates the model type, ensures the class exists,<br>
     * and confirms the model belongs to the authenticated user.<br>
     *
     * @param  string|null  $type
     * @param  int|null  $id
     *
     * @throws Illuminate\Validation\ValidationException
     * @throws NotFoundHttpException
     */
    function getModel(?string $owner_type = null, ?int $owner_id = null): Model
    {
        $id = $owner_id ?? (int) request('owner_id');
        $type = $owner_type ?? request('owner_type');
        Truthy(is_null($type) || is_null($id), 'Failed to retrieve model');
        Truthy(! in_array($type, SectionsTypes::names()), "Invalid model type: {$type}");
        $type = ucfirst($type);
        $class = "App\\Models\\{$type}";
        Truthy(! class_exists($class), "Class does not exist: {$class}");
        $model = $class::find($id);
        NotFound($model, "Model not found: {$type} with ID {$id}");
        Truthy((int) $model->user_id !== (int) Auth::id(), 'Unauthorized access to model.');

        return $model;
    }
}

if (! function_exists('checkAndCastData')) {
    /**
     * check if fields in requiredFields array exists <br>
     *
     * if true casts it to the provieded cast <br>
     *
     * if false assign default value if provided <br>
     *
     * if not is possible set it as missing field <br>
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
        Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));

        return $data;
    }
}
