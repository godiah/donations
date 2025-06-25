<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('individuals', function (Blueprint $table) {
            // Add new name fields
            $table->string('first_name')->after('contribution_reason_id');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->after('middle_name');

            // Add KYC verification fields
            $table->boolean('kyc_verified')->default(false)->after('id_number');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_verified');
            $table->json('kyc_response_data')->nullable()->after('kyc_verified_at');

            // Remove emergency contact fields
            $table->dropColumn(['emergency_contact_name', 'emergency_contact_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individuals', function (Blueprint $table) {
            // Remove new name fields
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);

            // Remove KYC verification fields
            $table->dropColumn(['kyc_verified', 'kyc_verified_at', 'kyc_response_data']);

            // Add back emergency contact fields
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
        });
    }
};
