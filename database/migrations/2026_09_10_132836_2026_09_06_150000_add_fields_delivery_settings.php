<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_settings', function (Blueprint $table) {
            $table->decimal('base_price', 8, 2)->default(2.50)->after('radius_km');
            $table->decimal('base_km', 8, 2)->default(1.00)->after('base_price');
            $table->decimal('per_km_price', 8, 2)->default(1.00)->after('base_km');
            $table->decimal('max_delivery_fee', 8, 2)->nullable()->after('per_km_price');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_settings', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'base_km', 'per_km_price', 'max_delivery_fee']);
        });
    }
};