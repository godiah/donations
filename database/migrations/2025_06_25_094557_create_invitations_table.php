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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('payout_mandate_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['token', 'expires_at']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
