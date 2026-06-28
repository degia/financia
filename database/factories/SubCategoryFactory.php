<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubCategory>
 */
class SubCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement([
                'Groceries', 'Dining Out', 'Coffee & Snacks',
                'Online Shopping', 'Clothing', 'Electronics',
                'Rent', 'Electricity', 'Water', 'Internet',
                'Gas & Fuel', 'Public Transport', 'Parking',
            ]),
        ];
    }
}
