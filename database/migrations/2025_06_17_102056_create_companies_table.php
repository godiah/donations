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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Contribution details
            $table->string('contribution_name');
            $table->text('contribution_description');
            $table->foreignId('contribution_reason_id')->constrained();

            // Company registration details
            $table->string('company_name');
            $table->string('registration_certificate');
            $table->string('pin_number');
            $table->string('cr12'); // list of directors
            $table->date('cr12_date');

            // Address information 
            $table->text('address');
            $table->string('city');
            $table->string('county');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Kenya');

            // Banking details
            $table->foreignId('bank_id')->nullable()->constrained('banks');
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_proof')->nullable();  // letter or cancelled cheque                     
            $table->string('settlement')->nullable();

            // Contact information
            $table->json('contact_persons')->nullable(); // Array of contact person objects

            // Financial goals
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->decimal('amount_raised', 15, 2)->default(0);
            $table->decimal('fees_charged', 15, 2)->default(0);

            // Additional metadata
            $table->json('additional_info')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'contribution_reason_id']);
            $table->index('registration_certificate');
            $table->index('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
