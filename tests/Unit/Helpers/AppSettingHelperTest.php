<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * Covers the cached app_setting() helper (improvement report 5.1) and the
 * Setting model cache-invalidation keys, which previously did not match the
 * keys used by the helper.
 */
it('returns the default when the setting is missing', function () {
    expect(app_setting('missing_setting_xyz', 'fallback'))->toBe('fallback')
        ->and(app_setting(null, ['a' => 1]))->toBe(['a' => 1]);
});

it('persists settings through the fillable model', function () {
    Setting::create(['name' => 'generated_code_length', 'value' => '8']);

    expect(app_setting('generated_code_length', 6))->toBe('8');
});

it('caches lookups and avoids repeated queries', function () {
    Setting::create(['name' => 'appointment_cancelation_period', 'value' => '2']);

    // warm the cache
    expect(app_setting('appointment_cancelation_period', 1))->toBe('2');

    $queries = 0;
    DB::listen(function () use (&$queries): void {
        $queries++;
    });

    app_setting('appointment_cancelation_period', 1);
    app_setting('appointment_cancelation_period', 1);

    expect($queries)->toBe(0);
});

it('refreshes the cached value when the setting is saved', function () {
    $setting = Setting::create(['name' => 'max_media_count', 'value' => '5']);
    expect(app_setting('max_media_count', 0))->toBe('5');

    $setting->update(['value' => '9']);

    // invalidation must clear the cache immediately (no stale read)
    expect(app_setting('max_media_count', 0))->toBe('9');
});

it('falls back to the default when the setting is deleted', function () {
    $setting = Setting::create(['name' => 'course_capacity', 'value' => '30']);
    expect(app_setting('course_capacity', 10))->toBe('30');

    $setting->delete();

    expect(app_setting('course_capacity', 10))->toBe(10);
});
