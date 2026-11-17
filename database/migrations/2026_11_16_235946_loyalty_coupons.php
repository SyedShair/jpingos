<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code', 20)->unique();

            // The coupon belongs to an email address (guest checkouts have
            // no account), and can only be redeemed with that same email.
            $table->string('email')->index();

            $table->string('discount_type', 10);          // percent | fixed
            $table->decimal('discount_value', 8, 2);
            $table->timestamp('expires_at');

            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_order_id')->nullable();
            $table->timestamp('voided_at')->nullable();   // cancelled by an admin
            $table->timestamp('emailed_at')->nullable();  // null = the email never went out

            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            // Which coupon these orders "earned". Once stamped, an order
            // never counts toward a coupon again — that is the reset.
            $table->unsignedBigInteger('counted_for_coupon_id')->nullable()->index();

            // The coupon that was redeemed ON this order, and what it took off.
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_code', 20)->nullable();
            $table->decimal('discount_total', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['counted_for_coupon_id', 'coupon_id', 'coupon_code', 'discount_total']);
        });

        Schema::dropIfExists('loyalty_coupons');
    }
};