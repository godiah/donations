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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('mpesa_checkout_request_id')->nullable()->after('gateway');
            $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_request_id');
            $table->string('mpesa_receipt_number')->nullable()->after('mpesa_merchant_request_id');
            $table->decimal('mpesa_amount', 10, 2)->nullable()->after('mpesa_receipt_number');
            $table->string('mpesa_phone_number')->nullable()->after('mpesa_amount');
            $table->timestamp('mpesa_transaction_date')->nullable()->after('mpesa_phone_number');
            $table->enum('mpesa_payment_type', ['stk_push', 'paybill'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_checkout_request_id',
                'mpesa_merchant_request_id',
                'mpesa_receipt_number',
                'mpesa_amount',
                'mpesa_phone_number',
                'mpesa_transaction_date',
                'mpesa_payment_type'
            ]);
        });
    }
};
