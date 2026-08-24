<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

test('eloquent is not unguarded', function () {
    expect(Model::isUnguarded())->toBeFalse();
});

test('unguard is never called inside the application code', function () {
    $offenders = collect(File::allFiles(app_path()))
        ->filter(fn($file) => str_contains((string) file_get_contents($file->getRealPath()), 'Model::unguard'))
        ->map(fn($file) => $file->getPathname());

    expect($offenders->all())->toBeArray()->toBeEmpty();
});

test('every model declares its fillable attributes', function () {
    $files = File::allFiles(app_path('Models'));
    expect(count($files))->toBeGreaterThan(0);

    foreach ($files as $file) {
        $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();
        $properties = (new ReflectionClass($class))->getDefaultProperties();

        expect($properties)->toHaveKey('fillable')
            ->and($properties['fillable'])
            ->toBeArray()
            ->not->toBeEmpty("{$class} must define \$fillable");
    }
});

it('rejects mass assignment of attributes outside fillable', function () {
    Category::create([
        'name' => 'legit',
        'is_admin' => true, // not a column, not fillable
    ]);
})->throws(MassAssignmentException::class);

it('still allows mass assignment of fillable attributes', function () {
    $setting = Setting::create(['name' => 'mass_assignment_probe', 'value' => '1']);

    expect($setting->exists)->toBeTrue()
        ->and(Setting::query()->where('name', 'mass_assignment_probe')->exists())->toBeTrue();

    $setting->delete();
});
