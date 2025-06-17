<?php

namespace Database\Seeders;

use App\Models\IDType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'national_id', 'display_name' => 'National ID'],
            ['name' => 'passport', 'display_name' => 'Passport'],
            ['name' => 'alien_id', 'display_name' => 'Alien ID'],
        ];

        foreach ($types as $type) {
            IDType::updateOrCreate(
                ['name' => $type['name']], // Lookup by unique name
                $type
            );
        }
    }
}
