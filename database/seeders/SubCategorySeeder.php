<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $subs = [
            'Food & Drinks' => ['Groceries', 'Restaurant', 'Coffee & Snacks', 'Delivery'],
            'Transportation' => ['Fuel', 'Parking', 'Public Transport', 'Maintenance', 'Taxi/RideShare'],
            'Bills & Utilities' => ['Electricity', 'Water', 'Internet', 'Phone', 'Rent', 'Insurance'],
            'Shopping' => ['Clothing', 'Electronics', 'Home & Garden', 'Personal Care'],
            'Entertainment' => ['Movies', 'Streaming', 'Games', 'Hobbies', 'Travel'],
            'Health' => ['Medical', 'Pharmacy', 'Fitness', 'Dental'],
            'Education' => ['Books', 'Courses', 'Tutoring', 'School Fees'],
            'Salary' => ['Base Salary', 'Bonus', 'Allowance', 'Overtime'],
            'Freelance' => ['Web Development', 'Design', 'Writing', 'Consulting'],
            'Investment' => ['Stocks', 'Crypto', 'Real Estate', 'Dividends'],
            'Savings' => ['Emergency Fund', 'Retirement', 'Travel Fund', 'Goal Savings'],
        ];

        foreach ($subs as $categoryName => $subNames) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($subNames as $name) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
