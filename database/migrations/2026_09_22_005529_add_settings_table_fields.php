<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('address')->nullable()->after('vat_number');
            // Full Google Maps embed URL (the src of an <iframe>) — not
            // just lat/lng — so the admin can paste the embed link
            // Google itself provides, no geocoding needed on our end.
            $table->string('map_url', 500)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['address', 'map_url']);
        });
    }
};