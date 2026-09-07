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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // Identifies which cart this row belongs to. Guests get a
            // session-bound cart, logged-in users get a user-bound cart.
            // Only one of these two will be set on a given row.
            $table->string('session_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Matches the string $rowId the controller uses to update/
            // destroy a specific line — e.g. a hash of menu_item_id +
            // options, so identical customizations collapse into one row
            // and quantity increments instead of duplicating.
            $table->string('row_id')->unique();

            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);

            // Selected option_values ids for this line (sizes, add-ons,
            // sauces, etc.) — kept as JSON since the count/shape varies
            // per menu item rather than being a fixed set of columns.
            $table->json('options')->nullable();

            // Snapshot of price at the time it was added, so historical
            // cart/order totals don't shift if the menu price changes later.
            $table->decimal('unit_price', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};