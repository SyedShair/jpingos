<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // Anonymous visitor (cookie), optional logged-in storefront customer
            $table->uuid('visitor_uid');
            $table->unsignedBigInteger('customer_id')->nullable();

            // What was viewed
            $table->string('path', 255);
            $table->string('route_name', 100)->nullable();
            $table->string('referrer_host', 190)->nullable();

            // Device (parsed from the user agent; the raw UA and raw IP are NOT stored)
            $table->string('device_type', 10)->default('desktop');
            $table->string('browser', 30)->nullable();

            // Where
            $table->string('country_code', 2)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('area', 120)->nullable();        // neighbourhood — only from browser location
            $table->string('postal_code', 16)->nullable();  // approximate, from IP database
            $table->decimal('lat', 8, 5)->nullable();
            $table->decimal('lng', 8, 5)->nullable();
            $table->string('location_source', 10)->nullable(); // 'ip' | 'browser'

            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index(['visitor_uid', 'created_at']);
            $table->index('country_code');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
