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
        Schema::table('donation_links', function (Blueprint $table) {
            $table->enum('mpesa_payment_method', ['stk_push', 'paybill', 'both'])->default('stk_push')->after('status');
            $table->foreignId('paybill_payout_method_id')->nullable()->constrained('payout_methods')->after('mpesa_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_links', function (Blueprint $table) {
            $table->dropForeign(['paybill_payout_method_id']);
            $table->dropColumn(['mpesa_payment_method', 'paybill_payout_method_id']);
        });
    }
};
