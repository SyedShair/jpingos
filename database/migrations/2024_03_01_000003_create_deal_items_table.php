<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();

            // What role this dish plays in the deal:
            //   applies_to        – flash deal / happy hour / lunch special target
            //   buy                – BOGO "buy" item
            //   free               – BOGO "get free" item, or the free_gift item
            //   bundle_component   – one line of a combo/bundle
            $table->enum('role', ['applies_to', 'buy', 'free', 'bundle_component']);

            $table->unsignedTinyInteger('quantity')->default(1);
            $table->decimal('override_price', 8, 2)->nullable(); // per-component price, for combos

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_items');
    }
};
