<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('menu_item_option_values');
        Schema::dropIfExists('menu_item_option_groups');
    }

    public function down(): void
    {
        // Intentionally left blank — this is a one-way cleanup migration.
        // If you need the old per-dish tables back, restore from the
        // earlier migration files in version control instead.
    }
};