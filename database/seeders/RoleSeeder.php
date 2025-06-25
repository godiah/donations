<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'member',   'display_name' => 'Member'],
            ['name' => 'admin',         'display_name' => 'Administrator'],
            [
                'name' => 'single_mandate_user',
                'display_name' => 'Single Mandate User',
            ],
            [
                'name' => 'payout_maker',
                'display_name' => 'Payout Maker',
            ],
            [
                'name' => 'payout_checker',
                'display_name' => 'Payout Checker',
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
