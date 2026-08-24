<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Category;
use App\Models\Court;
use App\Models\Tag;
use App\Models\User;

/**
 * Harness class exposing the Filters trait for direct testing.
 */
final class FiltersTraitHarness
{
    use App\Traits\Filters;
}

beforeEach(function () {
    // factories chain related records (category associate, tag attach)
    Category::factory()->count(3)->create();
    Tag::factory()->count(3)->create();
});

/**
 * Covers Filters::getter() (improvement report 6.1). The raw SQL dump is
 * guarded behind an explicit $debug parameter, so the default path must
 * never throw.
 */
it('returns models through the configured getter', function () {
    // use a trainer account so the factory does not spin up its own court
    Court::factory()->for(User::factory()->create(['account_type' => 'trainer']), 'user')->create();

    $harness = new FiltersTraitHarness();

    expect($harness->getter('court'))->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class)
        ->and($harness->getter('court'))->toHaveCount(1)
        ->and($harness->getter('court', [], 'first'))->toBeInstanceOf(Illuminate\Database\Eloquent\Model::class);
});

it('throws for unsupported section types', function () {
    (new FiltersTraitHarness())->getter('not_a_section');
})->throws(Exception::class, 'invalid model type');

it('never dumps raw sql unless debug is explicitly enabled', function () {
    Category::factory()->count(2)->create();
    Tag::factory()->count(2)->create();
    Activity::factory()->for(User::factory()->create(), 'user')->create();

    $harness = new FiltersTraitHarness();

    // default ($debug = false): must resolve normally, without throwing
    expect(fn () => $harness->getter('activity'))->not->toThrow(Exception::class);
});

it('dumps the raw sql only when debug is enabled', function () {
    Category::factory()->count(2)->create();
    Tag::factory()->count(2)->create();
    Activity::factory()->for(User::factory()->create(), 'user')->create();

    $harness = new FiltersTraitHarness();

    try {
        $harness->getter('activity', debug: true);
        $this->fail('debug mode should throw');
    } catch (Exception $exception) {
        expect($exception->getMessage())->toContain('select');
    }
});
