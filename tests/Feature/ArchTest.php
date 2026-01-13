<?php

declare(strict_types=1);

arch()
    ->expect('App')
    ->toUseStrictTypes()
    // ->toHaveMethodsDocumented()
    ->not->toUse(['die', 'dd', 'dump']);

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toHaveLineCountLessThan(100)
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
