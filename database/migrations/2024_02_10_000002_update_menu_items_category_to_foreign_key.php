<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Replaces the old `category` enum column on menu_items with a proper
     * `category_id` foreign key into the new `categories` table.
     *
     * If you're running this on a fresh database (no existing menu_items
     * rows), the data-migration step below is a no-op and this just adds
     * the foreign key.
     */
    public function up(): void
    {
        // 1. Backfill categories from whatever enum values already exist,
        //    so any existing menu items don't lose their category.
        if (Schema::hasColumn('menu_items', 'category')) {
            $existingValues = DB::table('menu_items')
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();

            foreach ($existingValues as $value) {
                DB::table('categories')->updateOrInsert(
                    ['slug' => Str::slug($value)],
                    [
                        'name'       => ucfirst($value),
                        'sort_order' => 0,
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 2. Add the new foreign key column.
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained()->nullOnDelete();
        });

        // 3. Point each menu item at its matching category row.
        if (Schema::hasColumn('menu_items', 'category')) {
            $categories = DB::table('categories')->pluck('id', 'slug');

            DB::table('menu_items')->orderBy('id')->chunk(100, function ($items) use ($categories) {
                foreach ($items as $item) {
                    $slug = Str::slug($item->category);
                    if (isset($categories[$slug])) {
                        DB::table('menu_items')
                            ->where('id', $item->id)
                            ->update(['category_id' => $categories[$slug]]);
                    }
                }
            });

            // 4. Drop the old enum column now that data has moved over.
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('category_id');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
