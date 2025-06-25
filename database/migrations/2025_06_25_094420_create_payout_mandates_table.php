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
        Schema::create('payout_mandates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['single', 'dual'])->default('single');
            $table->foreignId('maker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('checker_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('checker_name')->nullable();
            $table->string('checker_email')->nullable();
            $table->boolean('is_active')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['application_id', 'type']);
            $table->index('maker_id');
            $table->index('checker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_mandates');
    }
};
