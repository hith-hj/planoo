<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

arch()
    ->expect('App')
    ->toUseStrictTypes()
    // ->toHaveMethodsDocumented()
    ->not->toUse(['die', 'dd', 'dump']);

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toHaveLineCountLessThan(300)
    ->toBeFinal();

arch()
    ->expect('App\Http')
    ->toOnlyBeUsedIn('App\Http');

arch()
    ->expect('App\Traits')
    ->toBeTraits();

arch()
    ->expect('App\Interfaces')
    ->toBeInterfaces();

arch()
    ->expect('App\Services')
    ->toBeClasses()
    ->toExtendNothing();

arch()
    ->expect('App\Validators\Validators')
    ->toImplement('App\Interfaces\ValidatorsInterface');

arch()
    ->expect('App\Validators')
    ->toBeClasses()
    ->toExtend('App\Validators\Validators')
    ->ignoring('App\Validators\Validators');

arch()
    ->expect('App\Enums')
    ->toHaveMethod(['names', 'values']);

arch()->preset()->security();
arch()->preset()->php();

test('eloquent mass assignment protection is enabled', function () {
    expect(Model::isUnguarded())->toBeFalse();
});

test('Model::unguard() is never used in app code', function () {
    $offenders = collect(File::allFiles(app_path()))
        ->filter(fn($file) => str_contains((string) file_get_contents($file->getRealPath()), 'Model::unguard'))
        ->map(fn($file) => $file->getPathname());

    expect($offenders->all())->toBeEmpty();
});

test('every model declares its fillable attributes', function () {
    foreach (File::allFiles(app_path('Models')) as $file) {
        $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();
        $properties = (new ReflectionClass($class))->getDefaultProperties();

        expect(array_key_exists('fillable', $properties))->toBeTrue("{$class} is missing \$fillable")
            ->and($properties['fillable'])->toBeArray()->not->toBeEmpty("{$class} has an empty \$fillable");
    }
});
