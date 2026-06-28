<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SubCategorySeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $systemCategories = Category::whereNull('user_id')->where('is_system', true)->with('subCategories')->get();
        foreach ($systemCategories as $cat) {
            $newCat = $user->categories()->create([
                'name' => $cat->name,
                'type' => $cat->type,
                'icon' => $cat->icon,
                'color' => $cat->color,
                'is_system' => true,
            ]);

            foreach ($cat->subCategories as $sub) {
                $newCat->subCategories()->create([
                    'name' => $sub->name,
                ]);
            }
        }
    }
}
