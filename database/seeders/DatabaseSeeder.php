<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminsRoles;
use App\Enums\UsersTypes;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdmin();
        $this->createCategories();
        $this->createTags();
        $this->createUsers();
        // $this->createCustomers();
    }

    public function createAdmin()
    {
        Admin::factory()->create([
            'name' => 'S_Ad',
            'email' => 'S_Ad@admin.com',
            'role' => AdminsRoles::super->value,
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
                ['name' => 'AC', 'icon' => '#'],
                ['name' => 'wifi', 'icon' => '#'],
                ['name' => 'indoor', 'icon' => '#'],
                ['name' => 'charging', 'icon' => '#'],
            ]);
        }
    }

    private function createUsers()
    {
        // User::factory()->create();
        User::factory()->createMany([
            [
                'name' => 'fadi partner',
                'email' => 'fadi.alfrejat@gmail.com',
                'phone' => '0944102050',
                'account_type' => UsersTypes::stadium->name,
                'password' => bcrypt('Password123@@'),
            ],
            [
                'name' => 'test partner',
                'email' => 'test@partner.com',
                'phone' => '0911111111',
                'account_type' => UsersTypes::stadium->name,
                'password' => bcrypt('Mm12345@@'),
            ],
        ]);
    }

    private function createCustomers()
    {
        Customer::factory()->create();
    }
}
