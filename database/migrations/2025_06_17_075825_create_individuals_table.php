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
        Schema::create('individuals', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contribution_reason_id')->constrained();
            $table->foreignId('id_type_id')->constrained();

            // Contribution details
            $table->string('contribution_name');
            $table->text('contribution_description')->nullable();

            // Personal details
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Identification
            $table->string('id_number');
            $table->string('kra_pin')->nullable();

            // Financial goals
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->decimal('amount_raised', 15, 2)->default(0);
            $table->decimal('fees_charged', 15, 2)->default(0);

            // Optional extra field
            $table->json('additional_info')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'contribution_reason_id']);
            $table->index('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individuals');
    }
};
