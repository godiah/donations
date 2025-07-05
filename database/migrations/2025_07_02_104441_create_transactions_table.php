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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Our internal transaction ID
            $table->string('gateway_transaction_id')->nullable(); // Gateway's transaction ID
            $table->enum('gateway', ['cybersource', 'mpesa']);
            $table->enum('type', ['payment', 'refund', 'partial_refund'])->default('payment');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'declined', 'review'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->json('gateway_response')->nullable(); // Store full gateway response for auditing
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['contribution_id', 'status']);
            $table->index(['gateway', 'status']);
            $table->index(['transaction_id']);
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
