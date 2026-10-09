<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "Halloween Night", "Ramadan Iftar"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();     // stored path, e.g. booking-events/halloween.jpg
            $table->date('starts_on');
            $table->date('ends_on');
            $table->unsignedInteger('party_size_min')->default(1);
            $table->unsignedInteger('party_size_max')->default(12);
            $table->unsignedInteger('capacity')->nullable();   // null = unlimited
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_events');
    }
};
