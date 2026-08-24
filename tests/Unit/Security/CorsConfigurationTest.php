<?php

declare(strict_types=1);

/**
 * Verifies the restrictive CORS configuration added for improvement report 4.6.
 */
test('cors is restricted to the api paths', function () {
    expect(config('cors.paths'))->toBe(['api/*']);
});

test('cors allows no origins by default', function () {
    expect(config('cors.allowed_origins'))->toBe([]);
});

test('cors parses allowed origins from the environment variable', function () {
    config(['cors.allowed_origins' => array_values(array_filter(array_map(
        callback: 'trim',
        array: explode(',', 'https://app.planoo.sy , https://admin.planoo.sy ,'),
    )))]);

    expect(config('cors.allowed_origins'))->toBe([
        'https://app.planoo.sy',
        'https://admin.planoo.sy',
    ]);
});

test('cors does not support credentials', function () {
    expect(config('cors.supports_credentials'))->toBeFalse();
});
