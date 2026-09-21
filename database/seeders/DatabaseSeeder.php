<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminsRoles;
use App\Enums\UsersTypes;
use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSettings();
        $this->createAdmin();
        $this->createCategories();
        $this->createTags();
        $this->createUsers();
    }

    public function createSettings()
    {
        Artisan::call('app:sss');
    }

    public function createAdmin()
    {
        if (DB::table('admins')->count() === 0) {
            Admin::factory()->create([
                'name' => 'S_Ad',
                'email' => 'S_Ad@admin.com',
                'role' => AdminsRoles::super->value,
            ]);
        }
    }

    public function createUsers()
    {
        User::factory()->createMany([
            [
                'name' => 'fadi partner',
                'email' => 'fadi.alfrejat@gmail.com',
                'country_code' => '+963',
                'phone' => '944102050',
                'account_type' => UsersTypes::stadium->name,
                'password' => bcrypt('Password123@@'),
            ],
            [
                'name' => 'test partner',
                'email' => 'test@partner.com',
                'country_code' => '+963',
                'phone' => '911111111',
                'account_type' => UsersTypes::stadium->name,
                'password' => bcrypt('Mm12345@@'),
            ],
        ]);
    }

    private function createCategories()
    {
        if (DB::table('categories')->count() === 0) {
            return DB::table('categories')->insert([
                [
                    'name' => 'Football',
                    'icon' => 'uploads/categories_icons/01KTXNH8P171ATNBJVBSN0W0WM.png',
                ],
                [
                    'name' => 'Basketball',
                    'icon' => 'uploads/categories_icons/01KTXNHVTZMGS85W8Z7AAK17Y5.png',
                ],
                [
                    'name' => 'Swimming',
                    'icon' => 'uploads/categories_icons/01KTXNJFHRQEQ7YMK834AHZH3D.png',
                ],
                [
                    'name' => 'Padel',
                    'icon' => 'uploads/categories_icons/01KTXNKD3AC8TXSV1ECJS7WFTN.png',
                ],
                [
                    'name' => 'Tennis',
                    'icon' => 'uploads/categories_icons/01KTXNM4N1WQWECH0NJD7MZ5PE.png',
                ],
                [
                    'name' => 'Running',
                    'icon' => 'uploads/categories_icons/01KTXNMTJ9T751WQEAA51M7CBF.png',
                ],
                [
                    'name' => 'Judo',
                    'icon' => 'uploads/categories_icons/01KTXNNEVW7YN7VHE2Q47J9D15.png',
                ],
                [
                    'name' => 'Cycling',
                    'icon' => 'uploads/categories_icons/01KTXNPTAG9J6BV0737M2HJMS1.png',
                ],
                [
                    'name' => 'Horse Riding',
                    'icon' => 'uploads/categories_icons/01KTXNRA91D3WNQ33S123CW8Z6.png',
                ],
                [
                    'name' => 'Gym',
                    'icon' => 'uploads/categories_icons/01KTXNRV30TBD5AMQ37KJ73P4T.png',
                ],
                [
                    'name' => 'Boxing',
                    'icon' => 'uploads/categories_icons/01KTXNSRHTP1E0EHK6NT7SV9F9.png',
                ],
                [
                    'name' => 'Gymnastics',
                    'icon' => 'uploads/categories_icons/01KTXNTNR8828Q0H116TT3QXWE.png',
                ],
                [
                    'name' => 'Chess',
                    'icon' => 'uploads/categories_icons/01KTXNVBW93EKD8TYZA1WS35SX.png',
                ],
                [
                    'name' => 'Ping Pon',
                    'icon' => 'uploads/categories_icons/01KTXNVZV5C78BJAZRKED0TDD6.png',
                ],
                [
                    'name' => 'Billiards',
                    'icon' => 'uploads/categories_icons/01KTXNWZ53QYJXHXHFAEPWT0K2.png',
                ],
                [
                    'name' => 'Skating',
                    'icon' => 'uploads/categories_icons/01KTXNXTW96ZRB6YFWWXBEA8YD.png',
                ],
                [
                    'name' => 'Other',
                    'icon' => 'uploads/categories_icons/01KTXNYVEZSTVMGN3EZB9AY7J9.png',
                ],
            ]);
        }
    }

    private function createTags()
    {
        if (DB::table('tags')->count() === 0) {
            return DB::table('tags')->insert([
                [
                    'name' => 'AC',
                    'icon' => 'uploads/tags_icons/01KTXNA98M24D2VNZ611MD4BXA.png',
                ],
                [
                    'name' => 'Wifi',
                    'icon' => 'uploads/tags_icons/01KTXNB3AFT42QXXBD07QZHMKT.png',
                ],
                [
                    'name' => 'Indoor',
                    'icon' => 'uploads/tags_icons/01KTXNBW11FP8RHGEWC8JG8SYH.png',
                ],
                [
                    'name' => 'Parking',
                    'icon' => 'uploads/tags_icons/01KTXNDWDQEJREENK1YT5SG250.png',
                ],
                [
                    'name' => 'Restaurant ',
                    'icon' => 'uploads/tags_icons/01KTXNG3GV3BSDGZCH2VNCM0GK.png',
                ],
            ]);
        }
    }
}
