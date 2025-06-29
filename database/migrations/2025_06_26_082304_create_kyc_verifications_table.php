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
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individual_id')->constrained('individuals')->onDelete('cascade');
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('job_id')->unique(); // Our unique job identifier
            $table->string('smile_job_id')->nullable(); // Smile Identity job ID from response
            $table->string('id_type'); // national_id, passport, alien_id
            $table->string('id_number');
            $table->string('country_code', 2); // KE for Kenya
            $table->enum('status', ['pending', 'processing', 'verified', 'rejected', 'failed'])->default('pending');
            $table->string('result_code')->nullable(); // 1020, 1021, 1022, etc.
            $table->string('result_text')->nullable(); // Exact Match, Partial Match, No Match
            $table->json('verification_data')->nullable(); // Full response from Smile Identity
            $table->json('actions')->nullable(); // Actions object from response
            $table->text('failure_reason')->nullable(); // Error message if failed
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('initiated_by')->constrained('users')->onDelete('cascade'); // Admin who initiated

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['individual_id', 'application_id']);
            $table->index('status');
            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
