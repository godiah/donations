<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['name' => 'equity_bank', 'display_name' => 'Equity Bank'],
            ['name' => 'kcb_bank', 'display_name' => 'KCB Bank'],
            ['name' => 'absa_bank', 'display_name' => 'ABSA Bank'],
            ['name' => 'coop_bank', 'display_name' => 'Co-operative Bank'],
            ['name' => 'ncba_bank', 'display_name' => 'NCBA Bank'],
            ['name' => 'standard_chartered', 'display_name' => 'Standard Chartered'],
            ['name' => 'family_bank', 'display_name' => 'Family Bank'],
            ['name' => 'stanbic_bank', 'display_name' => 'Stanbic Bank'],
            ['name' => 'dtb_bank', 'display_name' => 'Diamond Trust Bank (DTB)'],
        ];

        foreach ($banks as $bank) {
            Bank::updateOrCreate(['name' => $bank['name']], $bank);
        }
    }
}
