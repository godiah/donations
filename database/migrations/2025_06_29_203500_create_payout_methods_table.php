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
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable'); // For Individual or Company
            $table->enum('type', ['mobile_money', 'bank_account']);
            $table->string('provider')->nullable(); // M-Pesa, Airtel Money, etc.
            $table->string('account_number'); // Phone number or account number
            $table->string('account_name');
            $table->foreignId('bank_id')->nullable()->constrained('banks'); // For bank accounts
            $table->string('bank_account_no')->nullable(); // For bank accounts
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('additional_info')->nullable(); // Additional data
            $table->timestamps();

            // Ensure only one primary method per payable
            $table->unique(['payable_type', 'payable_id', 'is_primary'], 'unique_primary_payout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};
