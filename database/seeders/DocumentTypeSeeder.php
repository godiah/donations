<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            // Wedding documents
            [
                'type_key' => 'wedding_invitation',
                'display_name' => 'Wedding Invitation',
                'description' => 'Official wedding invitation or ceremony announcement',
            ],
            [
                'type_key' => 'identity_document',
                'display_name' => 'Identity Document',
                'description' => 'National ID, Passport, or other government-issued identification',
            ],

            // Funeral documents
            [
                'type_key' => 'burial_permit',
                'display_name' => 'Burial Permit',
                'description' => 'Burial Permit issued by authorized authority',
            ],

            // Medical documents
            [
                'type_key' => 'medical_report',
                'display_name' => 'Medical Report',
                'description' => 'Medical report or diagnosis from licensed healthcare provider',
            ],
            [
                'type_key' => 'hospital_bill',
                'display_name' => 'Hospital Bill',
                'description' => 'Hospital bill or medical expense documentation',
            ],
            [
                'type_key' => 'identity_document',
                'display_name' => 'Identity Document',
                'description' => 'National ID, Passport, or other government-issued identification',
            ],

            // Education documents
            [
                'type_key' => 'admission_letter',
                'display_name' => 'Admission Letter',
                'description' => 'Official admission letter from educational institution',
            ],
            [
                'type_key' => 'fee_structure',
                'display_name' => 'Fee Structure',
                'description' => 'Official fee structure or invoice from educational institution',
            ],

            // Business documents


            // General documents
            [
                'type_key' => 'supporting_document',
                'display_name' => 'Supporting Document',
                'description' => 'Any relevant supporting document for the contribution request',
            ],
        ];

        foreach ($documentTypes as $docType) {
            DocumentType::updateOrCreate(
                ['type_key' => $docType['type_key']],
                $docType
            );
        }
    }
}
