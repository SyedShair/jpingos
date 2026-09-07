<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        // CategorySeeder must run first — see DatabaseSeeder.
        MenuItem::factory()->count(20)->create();
    }
}
