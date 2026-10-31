<?php

declare(strict_types=1);

use App\Models\Notification;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed();
});

it('pages large notification histories while preserving ownership', function (string $role) {
    $this->user($role, 'stadium')->api();
    $this->user->notifications()->delete();
    $base = Notification::factory()->make([
        'belongTo_id' => $this->user->id,
        'belongTo_type' => $this->user::class,
        'created_at' => now(),
        'updated_at' => now()
    ])->getAttributes();
    DB::table('notifications')->insert(array_fill(0, 501, $base));
    // $this->getJson(route($role.'.notification.all'))->assertUnprocessable();
    $first = $this->getJson(route($role . '.notification.all', ['perPage' => 1]))->assertOk();
    $second = $this->getJson(route($role . '.notification.all', ['perPage' => 1, 'page' => 2]))->assertOk();
    expect($first->json('payload.notifications'))->toHaveCount(1);
    expect($second->json('payload.notifications'))->toHaveCount(1);
    expect($first->json('payload.notifications.0.id'))->not->toBe($second->json('payload.notifications.0.id'));
})->with(['customer', 'partner']);
