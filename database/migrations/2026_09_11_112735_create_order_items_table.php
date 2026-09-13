<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Same distinction CartService uses: a bundle row has
            // deal_id set and menu_item_id null; a plain or deal-linked
            // dish row has menu_item_id set, deal_id optionally too.
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot fields — the menu item/deal can change or be
            // deleted later; the order should keep showing what was
            // actually bought, not whatever that record says today.
            $table->string('name');
            $table->json('options')->nullable(); // [{name, price_delta}, ...]
            $table->decimal('unit_price', 8, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 8, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};