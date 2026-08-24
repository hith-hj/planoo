<?php

declare(strict_types=1);

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed();
    Cache::flush();
    $this->url = '/api/label/categories';
});

/**
 * Covers improvement report 5.2 / 10.7: reference data endpoints are cached
 * and must survive raw database changes until invalidation.
 */
it('caches the categories payload', function () {
    $res = $this->getJson($this->url);
    $res->assertOk();

    expect(Cache::has('labels.categories'))->toBeTrue();

    // raw insert bypasses model events -> cache must still serve old data
    DB::table('categories')->insert(['name' => 'uncached_sport']);

    $cachedRes = $this->getJson($this->url)->assertOk();
    $names = collect($cachedRes->json('payload.categories'))->pluck('name');

    expect($names)->not->toContain('uncached_sport');
});

it('invalidates the categories cache when a category is saved', function () {
    $this->getJson($this->url)->assertOk();
    expect(Cache::has('labels.categories'))->toBeTrue();

    Category::create(['name' => 'handball']);

    expect(Cache::has('labels.categories'))->toBeFalse();

    $res = $this->getJson($this->url)->assertOk();
    expect(collect($res->json('payload.categories'))->pluck('name'))->toContain('handball');
});
