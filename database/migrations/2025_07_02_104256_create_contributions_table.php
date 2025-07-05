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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_link_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES'); // KES, USD
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('donation_type', ['anonymous', 'family', 'friend', 'colleague', 'supporter', 'other'])->default('anonymous');
            $table->enum('payment_method', ['mpesa', 'card']);
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('cybersource_request_id')->nullable()->unique();
            $table->string('cybersource_transaction_id')->nullable();
            $table->json('payment_response')->nullable(); // Store full gateway response
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['donation_link_id', 'payment_status']);
            $table->index(['payment_status', 'created_at']);
            $table->index('cybersource_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
