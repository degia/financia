<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $systemCategories = Category::whereNull('user_id')->where('is_system', true)->get();
        foreach ($systemCategories as $cat) {
            $user->categories()->create([
                'name' => $cat->name,
                'type' => $cat->type,
                'icon' => $cat->icon,
                'color' => $cat->color,
                'is_system' => true,
            ]);
        }
    }
}
