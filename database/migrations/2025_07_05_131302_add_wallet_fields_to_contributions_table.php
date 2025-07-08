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
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->after('processed_at');
            $table->boolean('wallet_credited')->default(false)->after('wallet_transaction_id');
            $table->timestamp('wallet_credited_at')->nullable()->after('wallet_credited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropForeign(['wallet_transaction_id']);
            $table->dropColumn(['wallet_transaction_id', 'wallet_credited', 'wallet_credited_at']);
        });
    }
};
