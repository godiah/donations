<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payout_methods', function (Blueprint $table) {            
            $table->string('paybill_account_name')->nullable()->after('paybill_number');
            $table->json('paybill_settings')->nullable()->after('paybill_account_name'); // For additional paybill configurations
        });

        // Add new types to the type enum if needed
        DB::statement("ALTER TABLE payout_methods MODIFY COLUMN type ENUM('mobile_money', 'bank_account', 'paybill') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payout_methods', function (Blueprint $table) {
            $table->dropColumn(['paybill_number', 'paybill_account_name', 'paybill_settings']);
        });

        // Revert enum change
        DB::statement("ALTER TABLE payout_methods MODIFY COLUMN type ENUM('mobile_money', 'bank_transfer') NOT NULL");
    }
};
