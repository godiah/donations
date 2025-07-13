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
        Schema::table('contributions', function (Blueprint $table) {
            $table->decimal('platform_fee', 10, 2)->nullable()->default(null)->after('amount');
            $table->decimal('net_amount', 10, 2)->nullable()->default(null)->after('platform_fee');
            $table->decimal('platform_fee_percentage', 5, 2)->default(5.00)->after('net_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn(['platform_fee', 'net_amount', 'platform_fee_percentage']);
        });
    }
};
