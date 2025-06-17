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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_key')->unique(); // 'wedding_invitation', 'death_certificate', etc.
            $table->string('display_name'); // 'Wedding Invitation', 'Death Certificate'
            $table->text('description'); // 'Official wedding invitation or ceremony announcement'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type_key', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
