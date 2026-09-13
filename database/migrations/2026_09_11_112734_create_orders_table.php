<?php
// create_orders_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            // Guest checkout stores contact/address directly on the order
            // itself — a logged-in customer's values are copied here too
            // at the moment of order placement, so a later profile edit
            // never silently rewrites the address on a past order.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('postcode');
            $table->string('order_type'); // pickup | delivery
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 8, 2);
            $table->decimal('total', 8, 2);

            $table->string('status')->default('pending'); // pending, confirmed, preparing, ready, completed, cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};