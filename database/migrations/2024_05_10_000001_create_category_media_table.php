<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deliberately a separate table rather than columns bolted onto
     * `categories` — keeps this feature fully independent of the existing
     * Categories CRUD, which is untouched by this migration.
     */
    public function up(): void
    {
        Schema::create('category_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('pdf_menu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_media');
    }
};
