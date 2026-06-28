<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Drinks', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#EF4444'],
            ['name' => 'Transportation', 'type' => 'expense', 'icon' => 'truck', 'color' => '#F97316'],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'icon' => 'receipt', 'color' => '#EAB308'],
            ['name' => 'Shopping', 'type' => 'expense', 'icon' => 'shopping-cart', 'color' => '#22C55E'],
            ['name' => 'Entertainment', 'type' => 'expense', 'icon' => 'film', 'color' => '#3B82F6'],
            ['name' => 'Health', 'type' => 'expense', 'icon' => 'heart', 'color' => '#EC4899'],
            ['name' => 'Education', 'type' => 'expense', 'icon' => 'book', 'color' => '#8B5CF6'],
            ['name' => 'Savings', 'type' => 'expense', 'icon' => 'piggy-bank', 'color' => '#8B5CF6'],
            ['name' => 'Other Expense', 'type' => 'expense', 'icon' => 'credit-card', 'color' => '#6B7280'],
            ['name' => 'Salary', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#22C55E'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'laptop', 'color' => '#14B8A6'],
            ['name' => 'Investment', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#6366F1'],
            ['name' => 'Other Income', 'type' => 'income', 'icon' => 'plus-circle', 'color' => '#6B7280'],
            ['name' => 'Transfer', 'type' => 'expense', 'icon' => 'arrow-right-left', 'color' => '#6366F1'],
            ['name' => 'Transfer', 'type' => 'income', 'icon' => 'arrow-right-left', 'color' => '#6366F1'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create([
                'user_id' => null,
                'name' => $category['name'],
                'type' => $category['type'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_system' => true,
            ]);
        }
    }
}
