<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard against the column already existing — safe to run
        // regardless of whether a previous attempt partially succeeded.
        if (! Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('categories')
                    ->nullOnDelete();
            });

            return;
        }

        // Column exists, but the foreign key constraint might not (e.g. if
        // it was added by hand, or by a migration that failed after the
        // ALTER TABLE succeeded but before the constraint was attached).
        // Try to add just the constraint; silently skip if it's already there.
        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Constraint already exists — nothing to do.
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropConstrainedForeignId('parent_id');
            });
        }
    }
};