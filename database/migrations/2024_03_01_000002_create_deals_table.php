<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Which of the 3 requested groups + specific behavior this deal is.
            $table->enum('type', [
                'flash_deal',    // Daily & Time-Based: dish-specific price drop, limited window
                'happy_hour',    // Daily & Time-Based: recurring daily discount on select items
                'lunch_special', // Daily & Time-Based: recurring daily discount, lunch window
                'tiered_spend',  // Conditional: spend $X get $Y off / free delivery
                'bogo',          // Conditional: buy X get Y free/discounted
                'free_gift',     // Conditional: free item when spend >= threshold
                'combo',         // Bundled: fixed-price meal combo
                'bundle',        // Bundled: fixed-price family/group bundle
                'promo_code',    // Bundled: coupon code discount
            ]);

            $table->enum('discount_type', [
                'percentage', 'fixed_amount', 'free_delivery', 'fixed_price', 'none',
            ])->default('none');

            $table->decimal('discount_value', 8, 2)->nullable();  // % or $ off, depending on discount_type
            $table->decimal('combo_price', 8, 2)->nullable();     // fixed total price, for combo/bundle
            $table->decimal('min_spend', 8, 2)->nullable();       // threshold, for tiered_spend / free_gift

            // BOGO-specific
            $table->unsignedTinyInteger('buy_quantity')->nullable();
            $table->unsignedTinyInteger('get_quantity')->nullable();
            $table->unsignedTinyInteger('get_discount_percent')->nullable(); // 100 = fully free

            $table->string('promo_code')->nullable()->unique();

            // Overall campaign window (optional — e.g. "through end of month")
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            // Recurring daily schedule (happy hour / lunch special / "every Tuesday" flash deals)
            $table->json('recurring_days')->nullable(); // [0..6], 0 = Sunday
            $table->time('daily_start_time')->nullable();
            $table->time('daily_end_time')->nullable();

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
