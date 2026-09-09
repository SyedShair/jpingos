<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Bundle rows have no single menu item, so this must be nullable.
            $table->foreignId('menu_item_id')->nullable()->change();

            // Which deal this row belongs to. Set for both storeBundle()
            // rows and, optionally, a regular item added as part of a deal.
            $table->foreignId('deal_id')->nullable()->after('menu_item_id')
                ->constrained()->nullOnDelete();

            // Bundle rows price from the deal's combo price rather than
            // a single menu item's price, but we still want one consistent
            // "name" and "slug" to hand to the view — snapshot them here
            // rather than re-deriving with a join every time the cart renders.
            $table->string('name')->after('deal_id');
            $table->string('slug')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deal_id');
            $table->dropColumn(['name', 'slug']);
            $table->foreignId('menu_item_id')->nullable(false)->change();
        });
    }
};