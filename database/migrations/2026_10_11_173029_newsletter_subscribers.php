<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();

            // Stored lower-cased and unique, so the same address can never
            // be on the list twice just because of capitalisation.
            $table->string('email')->unique();

            $table->boolean('is_active')->default(true);

            // Where they signed up from (footer today; leaves room for a
            // checkout opt-in or a popup later without a schema change).
            $table->string('source', 50)->default('footer');

            // Kept as a record of when/where consent was given.
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};