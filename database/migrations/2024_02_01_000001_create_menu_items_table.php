<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('category', [
                'starters', 'mains', 'desserts', 'drinks', 'specials',
            ])->default('mains');

            $table->decimal('price', 8, 2);
            $table->decimal('discount_price', 8, 2)->nullable();

            $table->unsignedSmallInteger('prep_time_minutes')->nullable();
            $table->enum('spice_level', ['none', 'mild', 'medium', 'hot'])->default('none');

            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);

            $table->text('ingredients')->nullable();

            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
