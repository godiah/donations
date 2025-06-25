<?php

namespace Database\Seeders;

use App\Models\ContributionReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContributionReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            [
                'name' => 'Wedding',
                'description' => 'Wedding ceremonies and celebrations',
                'requires_document' => true,
                'required_document_types' => ['wedding_invitation', 'identity_document'],
            ],
            [
                'name' => 'Funeral',
                'description' => 'Funeral and memorial services',
                'requires_document' => true,
                'required_document_types' => ['burial_permit', 'identity_document'],
            ],
            [
                'name' => 'Medical',
                'description' => 'Medical treatment and healthcare',
                'requires_document' => true,
                'required_document_types' => ['medical_report', 'hospital_bill', 'identity_document'],
            ],
            [
                'name' => 'Education',
                'description' => 'Educational funding and scholarships',
                'requires_document' => true,
                'required_document_types' => ['admission_letter', 'fee_structure'],
            ],
            [
                'name' => 'Other',
                'description' => 'Other valid reasons',
                'requires_document' => true,
                'required_document_types' => ['supporting_document'],
            ],
        ];

        foreach ($reasons as $reason) {
            ContributionReason::updateOrCreate(
                ['name' => $reason['name']],
                $reason
            );
        }
    }
}
