<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Bank Central Asia', 'Bank Mandiri', 'Bank Negara Indonesia',
            'GoPay', 'OVO', 'DANA', 'PayPal', 'Cash',
        ]);

        return [
            'name' => $name,
            'type' => fake()->randomElement(['cash', 'bank', 'ewallet', 'credit_card', 'savings', 'other']),
            'color' => fake()->hexColor(),
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'is_active' => true,
        ];
    }
}
