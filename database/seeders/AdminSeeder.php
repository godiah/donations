<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@donations.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('admin2025'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );

        // attach the 'admin' role
        $role = Role::where('name', 'admin')->first();
        $admin->roles()->syncWithoutDetaching([$role->id]);
    }
}
