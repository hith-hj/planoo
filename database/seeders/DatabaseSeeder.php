<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UsersTypes;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $start = now();
        dump('seeding: categories');
        $this->createCategories();
        dump('seeding: tags');
        $this->createTags();
        dump('seeding: users');
        $this->createUsers();
        dump($start->diffForHumans());
    }

    private function createUsers()
    {
        User::factory()->createMany([
            [
                'name' => 'a',
                'email' => 'a@a.com',
                'phone' => '0911111112',
                'account_type' => UsersTypes::trainer->name,
            ],
            [
                'name' => 's',
                'email' => 's@s.com',
                'phone' => '0911111111',
                'account_type' => UsersTypes::stadium->name,
            ],
        ]);
    }

    private function createCategories()
    {
        if (DB::table('categories')->count() === 0) {
            return DB::table('categories')->insert([
                ['name' => 'football'],
                ['name' => 'basketball'],
                ['name' => 'swimming'],
                ['name' => 'billiards'],
                ['name' => 'events'],
            ]);
        }
    }

    private function createTags()
    {
        if (DB::table('tags')->count() === 0) {
            return DB::table('tags')->insert([
                ['name' => 'wifi', 'icon' => '#'],
                ['name' => 'indoor', 'icon' => '#'],
                ['name' => 'AC', 'icon' => '#'],
                ['name' => 'charging', 'icon' => '#'],
            ]);
        }
    }
}
