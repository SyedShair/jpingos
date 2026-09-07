<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Starters', 'icon' => 'tapas',       'sort_order' => 0, 'description' => 'Small plates to start the meal'],
            ['name' => 'Mains',    'icon' => 'restaurant',  'sort_order' => 1, 'description' => 'Hearty entrées'],
            ['name' => 'Desserts', 'icon' => 'icecream',    'sort_order' => 2, 'description' => 'Something sweet to finish'],
            ['name' => 'Drinks',   'icon' => 'local_bar',   'sort_order' => 3, 'description' => 'Beverages, cocktails, and wine'],
            ['name' => 'Specials', 'icon' => 'star',        'sort_order' => 4, 'description' => "Chef's picks and seasonal features"],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
