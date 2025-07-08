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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference')->unique();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 15, 2);
            $table->decimal('running_balance', 15, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('source_type')->nullable(); // 'donation', 'withdrawal', 'adjustment'
            $table->unsignedBigInteger('source_id')->nullable(); // ID of the source record
            $table->string('gateway')->nullable(); // 'cybersource', 'mpesa', 'bank_transfer'
            $table->string('gateway_reference')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['wallet_id', 'type']);
            $table->index(['status']);
            $table->index(['source_type', 'source_id']);
            $table->index(['transaction_reference']);
            $table->index(['gateway']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
