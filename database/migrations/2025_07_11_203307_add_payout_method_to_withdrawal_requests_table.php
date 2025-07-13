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
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreignId('payout_method_id')->after('wallet_id')->nullable()->constrained()->onDelete('set null');

            // Drop existing withdrawal_method enum
            $table->dropColumn('withdrawal_method');

            // Recreate withdrawal_method enum with new option
            $table->enum('withdrawal_method', ['mpesa', 'bank_transfer', 'paybill']);

            $table->index(['payout_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropIndex(['payout_method_id']);
            $table->dropColumn('payout_method_id');

            // Drop the modified withdrawal_method enum
            $table->dropColumn('withdrawal_method');

            // Recreate original withdrawal_method enum
            $table->enum('withdrawal_method', ['mpesa', 'bank_transfer']);
        });
    }
};
