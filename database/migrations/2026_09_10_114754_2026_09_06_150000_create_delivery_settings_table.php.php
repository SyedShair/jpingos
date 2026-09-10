<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_settings', function (Blueprint $table) {
            $table->id();

            // The center point of the delivery zone — typically the
            // restaurant's own address, set via the map's search box
            // or by dragging the marker.
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Stored in km regardless of what unit the admin UI displays
            // (miles get converted client-side / in the controller), so
            // every distance calculation in the app has one consistent unit.
            $table->decimal('radius_km', 6, 2)->default(5);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_settings');
    }
};