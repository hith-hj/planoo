<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminsRoles;
use App\Enums\UsersTypes;
use App\Models\Activity;
use App\Models\Admin;
use App\Models\Appointment;
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
        $this->createCustomers();
        $this->createAppointments();
    }

    public function createAdmin()
    {
        Admin::factory()->create([
            'name' => 'S_Ad',
            'email' => 'a@a.com',
            'role' => AdminsRoles::super->value,
        ]);
    }

    private function createUsers()
    {
        User::factory()->create();
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
            [
                'name' => 'm',
                'email' => 'm@m.com',
                'phone' => '0933333333',
                'account_type' => UsersTypes::stadium->name,
                'password' => bcrypt('Mm12345@@'),
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

    private function createAppointments()
    {
        $activities = Activity::all();
        foreach ($activities as $activity) {
            Appointment::factory(5)->for($activity, 'holder')->create();
        }
    }

    private function createCustomers()
    {
        Customer::factory(5)->create();
    }
}
