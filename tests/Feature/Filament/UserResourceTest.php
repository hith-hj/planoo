<?php

declare(strict_types=1);

use App\Filament\Resources\Users\UserResource;
use App\Models\Admin;
use App\Models\User;
use Livewire\Livewire;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ViewUser;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->seed();
    $this->actingAs(Admin::factory()->create(), 'admin');
});

describe('User Resource Test', function () {
    it('can render index page', function () {
        $this->get(UserResource::getUrl('index'))->assertSuccessful();
    });

    // it('can load the list page', function () {
    //     $users = User::factory()->count(5)->create();

    //     Livewire::test(ListUsers::class)
    //         ->assertOk()
    //         ->assertCanSeeTableRecords($users);
    // });

    // it('can search users by `name` or `email`', function () {
    //     $users = User::factory()->count(5)->create();

    //     Livewire::test(ListUsers::class)
    //         ->assertCanSeeTableRecords($users)
    //         ->searchTable($users->first()->name)
    //         ->assertCanSeeTableRecords($users->take(1))
    //         ->assertCanNotSeeTableRecords($users->skip(1))
    //         ->searchTable($users->last()->email)
    //         ->assertCanSeeTableRecords($users->take(-1))
    //         ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
    // });

    // it('can sort users by `name`', function () {
    //     $users = User::factory()->count(5)->create();

    //     Livewire::test(ListUsers::class)
    //         ->assertCanSeeTableRecords($users)
    //         ->sortTable('name')
    //         ->assertCanSeeTableRecords($users->sortBy('name'), inOrder: true)
    //         ->sortTable('name', 'desc')
    //         ->assertCanSeeTableRecords($users->sortByDesc('name'), inOrder: true);
    // });

    // it('can filter users by `account_type`', function () {
    //     $users = User::factory()->count(5)->create();

    //     Livewire::test(ListUsers::class)
    //         ->assertCanSeeTableRecords($users)
    //         ->filterTable('account_type', $users->first()->account_type)
    //         ->assertCanSeeTableRecords($users->where('account_type', $users->first()->account_type))
    //         ->assertCanNotSeeTableRecords($users->where('account_type', '!=', $users->first()->account_type));
    // });

    // it('can load the create page', function () {
    //     Livewire::test(CreateUser::class)->assertOk();
    // });

    // it('can create a user', function () {
    //     $newUserData = User::factory()->make();

    //     Livewire::test(CreateUser::class)
    //         ->fillForm([
    //             'name' => $newUserData->name,
    //             'email' => $newUserData->email,
    //         ])
    //         ->call('create')
    //         ->assertNotified()
    //         ->assertRedirect();

    //     assertDatabaseHas(User::class, [
    //         'name' => $newUserData->name,
    //         'email' => $newUserData->email,
    //     ]);
    // });

    // it('validates the create form data', function (array $data, array $errors) {
    //     $newUserData = User::factory()->make();

    //     Livewire::test(CreateUser::class)
    //         ->fillForm([
    //             'name' => $newUserData->name,
    //             'email' => $newUserData->email,
    //             ...$data,
    //         ])
    //         ->call('create')
    //         ->assertHasFormErrors($errors)
    //         ->assertNotNotified()
    //         ->assertNoRedirect();
    // })->with([
    //     '`name` is required' => [['name' => null], ['name' => 'required']],
    //     '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    //     '`email` is a valid email address' => [['email' => Str::random()], ['email' => 'email']],
    //     '`email` is required' => [['email' => null], ['email' => 'required']],
    //     '`email` is max 255 characters' => [['email' => Str::random(256)], ['email' => 'max']],
    // ]);

    // it('can load the edit page', function () {
    //     $user = User::factory()->create();

    //     Livewire::test(EditUser::class, [
    //         'record' => $user->id,
    //     ])
    //         ->assertOk()
    //         ->assertSchemaStateSet([
    //             'name' => $user->name,
    //             'email' => $user->email,
    //         ]);
    // });

    // it('can update a user', function () {
    //     $user = User::factory()->create();

    //     $newUserData = User::factory()->make();

    //     Livewire::test(EditUser::class, [
    //         'record' => $user->id,
    //     ])
    //         ->fillForm([
    //             'name' => $newUserData->name,
    //             'email' => $newUserData->email,
    //         ])
    //         ->call('save')
    //         ->assertNotified();

    //     assertDatabaseHas(User::class, [
    //         'id' => $user->id,
    //         'name' => $newUserData->name,
    //         'email' => $newUserData->email,
    //     ]);
    // });

    // it('validates the update form data', function (array $data, array $errors) {
    //     $user = User::factory()->create();

    //     $newUserData = User::factory()->make();

    //     Livewire::test(EditUser::class, [
    //         'record' => $user->id,
    //     ])
    //         ->fillForm([
    //             'name' => $newUserData->name,
    //             'email' => $newUserData->email,
    //             ...$data,
    //         ])
    //         ->call('save')
    //         ->assertHasFormErrors($errors)
    //         ->assertNotNotified();
    // })->with([
    //     '`name` is required' => [['name' => null], ['name' => 'required']],
    //     '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    //     '`email` is a valid email address' => [['email' => Str::random()], ['email' => 'email']],
    //     '`email` is required' => [['email' => null], ['email' => 'required']],
    //     '`email` is max 255 characters' => [['email' => Str::random(256)], ['email' => 'max']],
    // ]);

    // it('can delete a user', function () {
    //     $user = User::factory()->create();

    //     Livewire::test(EditUser::class, [
    //         'record' => $user->id,
    //     ])
    //         ->callAction(DeleteAction::class)
    //         ->assertNotified()
    //         ->assertRedirect();

    //     assertDatabaseMissing($user);
    // });

    // it('can load the view page', function () {
    //     $user = User::factory()->create();

    //     Livewire::test(ViewUser::class, [
    //         'record' => $user->id,
    //     ])
    //         ->assertOk()
    //         ->assertSchemaStateSet([
    //             'name' => $user->name,
    //             'email' => $user->email,
    //         ]);
    // });
});
