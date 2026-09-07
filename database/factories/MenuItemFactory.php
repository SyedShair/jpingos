<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        $dishesByCategory = [
            'Starters' => ['Garlic Bread', 'Tomato Bruschetta', 'Calamari Fritti', 'Soup of the Day'],
            'Mains'    => ['Grilled Salmon', 'Chicken Alfredo', 'Beef Burger', 'Margherita Pizza', 'Lamb Chops'],
            'Desserts' => ['Tiramisu', 'Chocolate Lava Cake', 'Cheesecake'],
            'Drinks'   => ['Fresh Lemonade', 'House Red Wine', 'Iced Tea', 'Espresso'],
            'Specials' => ["Chef's Tasting Plate", 'Weekend Brunch Board'],
        ];

        $categoryName = fake()->randomElement(array_keys($dishesByCategory));
        $category = Category::firstOrCreate(['name' => $categoryName]);

        $price = fake()->randomFloat(2, 5, 45);
        $onSale = fake()->boolean(25);

        return [
            'name'               => fake()->randomElement($dishesByCategory[$categoryName]),
            'description'        => fake()->sentence(12),
            'category_id'        => $category->id,
            'price'              => $price,
            'discount_price'     => $onSale ? round($price * 0.8, 2) : null,
            'prep_time_minutes'  => fake()->numberBetween(5, 40),
            'spice_level'        => fake()->randomElement(['none', 'mild', 'medium', 'hot']),
            'is_vegetarian'      => fake()->boolean(30),
            'is_vegan'           => fake()->boolean(15),
            'is_gluten_free'     => fake()->boolean(20),
            'ingredients'        => implode(', ', fake()->words(5)),
            'is_available'       => fake()->boolean(90),
            'is_featured'        => fake()->boolean(15),
        ];
    }
}
