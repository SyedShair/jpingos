<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner_one')->nullable();
            $table->string('banner_two')->nullable();

            $table->string('vat_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();

            // { "monday": { "open": "09:00", "close": "22:00", "closed": false }, ... }
            $table->json('opening_hours')->nullable();

            $table->boolean('show_delivery_checker')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};