<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Course;
use App\Models\Court;
use App\Models\Event;
use App\Services\ActivityServices;
use App\Services\CourseServices;
use App\Services\CourtServices;
use App\Services\EventServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed();
});

it('pages owned lists with stable ordering and summary counts', function (string $kind) {
    [$model, $key, $counts, $nested] = match ($kind) {
        'activity' => [Activity::firstOrFail(), 'activities', ['reviews_count'], ['reviews']],
        'course' => [Course::firstOrFail(), 'courses', ['reviews_count', 'customers_count'], ['reviews', 'customers']],
        'event' => [Event::firstOrFail(), 'events', ['reviews_count', 'customers_count'], ['reviews', 'customers']],
        'court' => [Court::firstOrFail(), 'courts', ['activities_count', 'courses_count', 'events_count'], ['activities', 'courses', 'events']],
    };
    $this->replaceUser($model->user);
    $rows = [];
    for ($i = 0; $i < 3; $i++) {
        $rows[] = [...$model->getAttributes(), 'id' => (string) Str::ulid()];
    }
    DB::table($model->getTable())->insert($rows);
    $first = $this->getJson(route('partner.'.$kind.'.all', ['perPage' => 1]))->assertOk();
    $second = $this->getJson(route('partner.'.$kind.'.all', ['perPage' => 1, 'page' => 2]))->assertOk();
    expect($first->json('payload.'.$key))->toHaveCount(1);
    expect($second->json('payload.'.$key))->toHaveCount(1);
    expect($first->json('payload.'.$key.'.0.id'))->not->toBe($second->json('payload.'.$key.'.0.id'));
    expect($first->json('payload.'.$key.'.0'))->toHaveKeys($counts);
    foreach ($nested as $relation) {
        expect($first->json('payload.'.$key.'.0'))->not->toHaveKey($relation);
    }
    $expected = $model->user->{$key}()->orderBy('id')->firstOrFail();
    expect($first->json('payload.'.$key.'.0.id'))->toBe($expected->id);
    expect($second->json('payload.page'))->toBe(2);
    expect($second->json('payload.perPage'))->toBe(1);
    $this->getJson(route('partner.'.$kind.'.all', ['page' => 10000, 'perPage' => 1]))
        ->assertOk()->assertJsonPath('payload.'.$key, []);
})->with(['activity', 'course', 'event', 'court']);

it('validates paging arguments before querying partner lists', function (string $kind, array $parameters) {
    $this->user('partner', 'stadium')->api();
    $this->getJson(route('partner.'.$kind.'.all', $parameters))->assertUnprocessable();
})->with(['activity', 'course', 'event', 'court'])->with([
    [['perPage' => 101]], [['page' => 0]], [['perPage' => 'bad']], [['page' => '1.5']],
]);

it('rejects overflowing legacy lists and allows the same data to be paged', function (string $kind) {
    [$model, $service] = match ($kind) {
        'activity' => [Activity::firstOrFail(), app(ActivityServices::class)],
        'course' => [Course::firstOrFail(), app(CourseServices::class)],
        'event' => [Event::firstOrFail(), app(EventServices::class)],
        'court' => [Court::firstOrFail(), app(CourtServices::class)],
    };
    $this->replaceUser($model->user);
    $rows = [];
    for ($i = 0; $i < 501; $i++) {
        $rows[] = [...$model->getAttributes(), 'id' => (string) Str::ulid()];
    }
    DB::table($model->getTable())->insert($rows);
    $this->getJson(route('partner.'.$kind.'.all'))->assertUnprocessable();
    $this->getJson(route('partner.'.$kind.'.all', ['perPage' => 10]))->assertOk();
    $page = $service->allByUser($model->user, 1, 10);
    expect($page)->toHaveCount(10);
    foreach ($page as $item) {
        expect($item->user_id)->toBe($model->user_id);
        expect($item->relationLoaded('reviews'))->toBeFalse();
        expect($item->relationLoaded('customers'))->toBeFalse();
    }
})->with(['activity', 'course', 'event', 'court']);

it('does not hydrate attendee or review collections for public group lists', function (string $kind) {
    $this->user('customer')->api();
    $service = $kind === 'course' ? app(CourseServices::class) : app(EventServices::class);
    $page = $service->allByFilter(perPage: 1);
    $item = $page->first();
    expect($item->relationLoaded('customers'))->toBeFalse();
    expect($item->relationLoaded('reviews'))->toBeFalse();
    expect($item->customers_count)->toBe($item->customers()->count());
    expect($item->reviews_count)->toBe($item->reviews()->count());
})->with(['course', 'event']);

it('bounds legacy review responses while keeping complete paginated access', function () {
    $activity = Activity::firstOrFail();
    $this->replaceUser($activity->user);
    $base = $activity->reviews()->firstOrFail()->getAttributes();
    unset($base['id']);
    DB::table('reviews')->insert(array_fill(0, 501, $base));
    $parameters = ['owner_type' => 'activity', 'owner_id' => $activity->id];
    $this->getJson(route('partner.review.all', $parameters))->assertUnprocessable();
    $first = $this->getJson(route('partner.review.all', [...$parameters, 'perPage' => 1]))->assertOk();
    $second = $this->getJson(route('partner.review.all', [...$parameters, 'perPage' => 1, 'page' => 2]))->assertOk();
    expect($first->json('payload.reviews'))->toHaveCount(1);
    expect($second->json('payload.reviews'))->toHaveCount(1);
    expect($first->json('payload.reviews.0.id'))->not->toBe($second->json('payload.reviews.0.id'));
    $this->getJson(route('partner.review.all', [...$parameters, 'perPage' => 101]))->assertUnprocessable();
});
