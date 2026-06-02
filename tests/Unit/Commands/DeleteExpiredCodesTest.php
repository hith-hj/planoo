<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    DB::table('codes')->truncate();
});

describe('Remove expired codes from db', function () {

    it('deletes expired codes from the database', function () {
        DB::table('codes')->insert([
            'belongTo_type' => User::class,
            'belongTo_id'   => 1,
            'type'          => 'password_reset',
            'code'          => 'EXPIRED123',
            'expire_at'     => now()->subHour(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        artisan('app:clean-expired-codes')->assertExitCode(0);

        assertDatabaseCount('codes', 0);
    });

    it('keeps active and future codes in the database', function () {
        DB::table('codes')->insert([
            'belongTo_type' => User::class,
            'belongTo_id'   => 1,
            'type'          => 'password_reset',
            'code'          => 'VALID123',
            'expire_at'     => now()->addHour(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        artisan('app:clean-expired-codes')->assertExitCode(0);

        assertDatabaseHas('codes', [
            'code' => 'VALID123',
        ]);
    });

    it('keeps codes that never expire', function () {
        DB::table('codes')->insert([
            'belongTo_type' => User::class,
            'belongTo_id'   => 1,
            'type'          => 'api_token',
            'code'          => 'PERMANENT123',
            'expire_at'     => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        artisan('app:clean-expired-codes')->assertExitCode(0);

        assertDatabaseHas('codes', [
            'code' => 'PERMANENT123',
        ]);
    });

});
