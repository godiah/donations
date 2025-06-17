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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship to individuals or companies
            $table->morphs('applicant'); // Creates applicant_type and applicant_id

            // Application status
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'additional_info_required',
                'approved',
                'rejected',
                'cancelled'
            ])->default('draft');

            // Admin review information
            $table->text('admin_comments')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for admin interface performance
            $table->index(['status']);
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
