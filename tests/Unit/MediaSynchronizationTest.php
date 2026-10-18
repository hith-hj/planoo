<?php

declare(strict_types=1);

use App\Jobs\SyncMediaFile;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Tag;
use App\Services\MediaServices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('media_mirror');
});

it('synchronizes only the changed file and preserves unrelated destination files', function () {
    Storage::disk('public')->put('uploads/images/new.png', 'new image');
    Storage::disk('public')->put('uploads/images/other.png', 'source other');
    Storage::disk('media_mirror')->put('uploads/images/other.png', 'keep existing');
    (new SyncMediaFile('uploads/images/new.png'))->handle();
    expect(Storage::disk('media_mirror')->get('uploads/images/new.png'))->toBe('new image');
    expect(Storage::disk('media_mirror')->get('uploads/images/other.png'))->toBe('keep existing');
    Storage::disk('public')->delete('uploads/images/new.png');
    (new SyncMediaFile('uploads/images/new.png'))->handle();
    Storage::disk('media_mirror')->assertMissing('uploads/images/new.png');
    Storage::disk('media_mirror')->assertExists('uploads/images/other.png');
});

it('reads current file state when queued jobs run out of order', function () {
    $oldJob = new SyncMediaFile('uploads/images/replaced.png');
    Storage::disk('public')->put($oldJob->path, 'replacement');
    $oldJob->handle();
    expect(Storage::disk('media_mirror')->get($oldJob->path))->toBe('replacement');
});

it('prevents overlapping copy and deletion jobs for the same file', function () {
    $first = new SyncMediaFile('uploads/images/shared.png');
    $second = new SyncMediaFile($first->path);
    $ranWhileLocked = false;
    $first->middleware()[0]->handle($first, function () use ($second, &$ranWhileLocked): void {
        $second->middleware()[0]->handle($second, function () use (&$ranWhileLocked): void {
            $ranWhileLocked = true;
        });
    });
    expect($ranWhileLocked)->toBeFalse();
    $second->middleware()[0]->handle($second, function () use (&$ranWhileLocked): void {
        $ranWhileLocked = true;
    });
    expect($ranWhileLocked)->toBeTrue();
});

it('rejects paths outside the uploads directory', function (string $path) {
    expect(fn () => new SyncMediaFile($path))->toThrow(InvalidArgumentException::class);
})->with(['../.env', 'uploads/../.env', '/uploads/file.png', 'uploads\\file.png', 'uploads//file.png', 'uploads/./file.png']);

it('queues uploads and deletes after commit without a full directory sync', function () {
    $customer = Customer::factory()->create();
    Bus::fake([SyncMediaFile::class]);
    app()->instance('env', 'production');
    try {
        $media = $customer->uploadMedia('image', 'profile_image', UploadedFile::fake()->image('profile.png'));
        Bus::assertDispatched(SyncMediaFile::class, fn ($job) => $job->path === $media->url && $job->afterCommit === true);
        app(MediaServices::class)->delete($media);
        Bus::assertDispatchedTimes(SyncMediaFile::class, 2);
        Storage::disk('public')->assertMissing($media->url);
    } finally {
        app()->instance('env', 'testing');
    }
});

it('keeps the manual sync command available and limits a targeted sync to one file', function () {
    Bus::fake([SyncMediaFile::class]);
    Storage::disk('public')->put('uploads/a.png', 'one');
    Storage::disk('public')->put('uploads/b.png', 'two');
    $this->artisan('app:sync-files-to-public uploads/a.png')->assertSuccessful();
    Bus::assertDispatchedTimes(SyncMediaFile::class, 1);
    $this->artisan('app:sync-files-to-public')->assertSuccessful();
    Bus::assertDispatchedTimes(SyncMediaFile::class, 3);
});

it('queues only changed category and tag icons', function (string $type) {
    Bus::fake([SyncMediaFile::class]);
    app()->instance('env', 'production');
    try {
        $model = $type === 'category' ? Category::class : Tag::class;
        $label = $model::create(['name' => 'Sports icon', 'icon' => 'uploads/icons/one.png']);
        $label->update(['name' => 'Renamed sport']);
        Bus::assertDispatchedTimes(SyncMediaFile::class, 1);
        $label->update(['icon' => 'uploads/icons/two.png']);
        Bus::assertDispatchedTimes(SyncMediaFile::class, 2);
    } finally {
        app()->instance('env', 'testing');
    }
})->with(['category', 'tag']);
