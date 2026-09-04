<?php

declare(strict_types=1);

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed();
    Cache::flush();
});

/**
 * Covers improvement report 5.2 
 */
it('caches the categories payload', function () {
    $res = $this->getJson(route('label.categories'));
    $res->assertOk();

    expect(Cache::has('labels.categories'))->toBeTrue();

    // raw insert bypasses model events -> cache must still serve old data
    DB::table('categories')->insert(['name' => 'uncached_sport']);

    $cachedRes = $this->getJson(route('label.categories'))->assertOk();
    $names = collect($cachedRes->json('payload.categories'))->pluck('name');

    expect($names)->not->toContain('uncached_sport');
});

it('invalidates the categories cache when a category is saved', function () {
    $this->getJson(route('label.categories'))->assertOk();
    expect(Cache::has('labels.categories'))->toBeTrue();

    Category::create(['name' => 'handball']);

    expect(Cache::has('labels.categories'))->toBeFalse();

    $res = $this->getJson(route('label.categories'))->assertOk();
    expect(collect($res->json('payload.categories'))->pluck('name'))->toContain('handball');
});
