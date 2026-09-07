<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Path relative to the "public" disk, e.g. deals/xyz.jpg.
            // Nullable: plenty of deal types (BOGO, tiered spend, promo
            // code) work fine without a dedicated image.
            $table->string('image')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};