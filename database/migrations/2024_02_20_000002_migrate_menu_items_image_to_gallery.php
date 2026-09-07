<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Moves each menu item's single `image` column into a matching
     * `menu_item_images` row (marked as primary), then drops the old
     * column. Safe to run even if `image` is already empty/missing —
     * the loop simply won't find anything to migrate.
     */
    public function up(): void
    {
        if (Schema::hasColumn('menu_items', 'image')) {
            DB::table('menu_items')
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->orderBy('id')
                ->each(function ($item) {
                    DB::table('menu_item_images')->insert([
                        'menu_item_id' => $item->id,
                        'path'         => $item->image,
                        'sort_order'   => 0,
                        'is_primary'   => true,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                });

            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        // Best-effort restore: pull each item's primary (or first) image
        // back onto the column. Any additional gallery images are left
        // in menu_item_images, which is not dropped by this rollback.
        DB::table('menu_item_images')
            ->orderBy('menu_item_id')
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('menu_item_id')
            ->each(function ($images, $menuItemId) {
                DB::table('menu_items')
                    ->where('id', $menuItemId)
                    ->update(['image' => $images->first()->path]);
            });
    }
};
