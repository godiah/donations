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
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'additional_info_required',
                'approved',
                'rejected',
                'resubmitted'
            ])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'additional_info_required',
                'approved',
                'rejected',
                'cancelled'
            ])->default('draft')->change();
        });
    }
};
