<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Institution;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── 0. Clean up existing demo data ──────────────────────
        $existing = User::where('email', 'demo@financia.com')->first();
        if ($existing) {
            foreach ($existing->loans as $l) { $l->payments()->delete(); }
            $existing->loans()->delete();
            $existing->goals()->delete();
            $existing->budgets()->delete();
            $existing->transactions()->delete();
            $existing->accounts()->delete();
            foreach ($existing->categories as $c) { $c->subCategories()->delete(); }
            $existing->categories()->delete();
            $existing->delete();
            $this->command->info('Cleaned up existing demo data.');
        }

        // ── 1. Demo User ────────────────────────────────────────
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@financia.com',
            'password' => bcrypt('password'),
            'currency_preference' => 'IDR',
            'email_verified_at' => now(),
        ]);

        $user->setPreference('menu_visibility', [
            'dashboard' => true, 'accounts' => true, 'transactions' => true,
            'categories' => true, 'budgets' => true, 'goals' => true,
            'loans' => true, 'reports' => true,
        ]);
        $user->save();

        // ── 2. Clone System Categories ──────────────────────────
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
                $newCat->subCategories()->create(['name' => $sub->name]);
            }
        }

        $cats = $user->categories()->with('subCategories')->get();
        $catsByName = $cats->keyBy('name');
        $subCatMap = [];
        foreach ($cats as $cat) {
            foreach ($cat->subCategories as $sub) {
                $subCatMap[$cat->name][$sub->name] = $sub->id;
            }
        }

        // ── 3. Institutions ─────────────────────────────────────
        $institutions = Institution::all()->keyBy('slug');

        // ── 4. Accounts ─────────────────────────────────────────
        $accountsData = [
            ['name' => 'Cash', 'type' => 'cash', 'category' => 'real', 'inst' => 'cash', 'initial' => 5000000, 'icon' => 'wallet', 'color' => '#10B981'],
            ['name' => 'BCA Checking', 'type' => 'bank', 'category' => 'real', 'inst' => 'bca', 'initial' => 20000000, 'icon' => 'building-columns', 'color' => '#0066AE'],
            ['name' => 'Mandiri Savings', 'type' => 'bank', 'category' => 'real', 'inst' => 'mandiri', 'initial' => 50000000, 'icon' => 'building-columns', 'color' => '#003E7E'],
            ['name' => 'GoPay', 'type' => 'ewallet', 'category' => 'real', 'inst' => 'gopay', 'initial' => 500000, 'icon' => 'wallet', 'color' => '#00AAFF'],
            ['name' => 'BRI Credit Card', 'type' => 'credit_card', 'category' => 'real', 'inst' => 'bri', 'initial' => 0, 'icon' => 'credit-card', 'color' => '#004B93'],
            ['name' => 'Emergency Fund', 'type' => 'savings', 'category' => 'savings', 'inst' => null, 'initial' => 0, 'icon' => 'piggy-bank', 'color' => '#8B5CF6'],
        ];

        $accs = [];
        foreach ($accountsData as $data) {
            $attrs = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'type' => $data['type'],
                'category' => $data['category'],
                'initial_balance' => $data['initial'],
                'current_balance' => $data['initial'],
                'currency' => 'IDR',
                'icon' => $data['icon'],
                'color' => $data['color'],
            ];
            if ($data['inst'] && isset($institutions[$data['inst']])) {
                $attrs['institution_id'] = $institutions[$data['inst']]->id;
            }
            $accs[$data['name']] = Account::create($attrs);
        }

        $user->setPreference('default_account_id', $accs['BCA Checking']->id);
        $user->save();

        // ── Helper ──────────────────────────────────────────────
        $now = Carbon::now();
        $nowMonth = (int) $now->month;
        $nowYear = (int) $now->year;
        $isFuture = fn($d) => Carbon::parse($d)->isFuture();

        // ── 5. Income Transactions ──────────────────────────────
        $salaryId = $catsByName['Salary']->id;
        $salarySubBase = $subCatMap['Salary']['Base Salary'] ?? null;
        $salarySubBonus = $subCatMap['Salary']['Bonus'] ?? null;
        $freelanceId = $catsByName['Freelance']->id;
        $freelanceSubWeb = $subCatMap['Freelance']['Web Development'] ?? null;
        $freelanceSubDesign = $subCatMap['Freelance']['Design'] ?? null;
        $otherIncomeId = $catsByName['Other Income']->id;
        $investId = $catsByName['Investment']->id;

        $incomes = [
            // salary monthly
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15000000, 'd' => '2026-01-25', 'desc' => 'Monthly Salary'],
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15000000, 'd' => '2026-02-25', 'desc' => 'Monthly Salary'],
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15000000, 'd' => '2026-03-25', 'desc' => 'Monthly Salary'],
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15000000, 'd' => '2026-04-25', 'desc' => 'Monthly Salary'],
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15500000, 'd' => '2026-05-25', 'desc' => 'Monthly Salary'],
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBase, 'amt' => 15500000, 'd' => '2026-06-25', 'desc' => 'Monthly Salary'],
            // bonus
            ['a' => 'BCA Checking', 'c' => $salaryId, 's' => $salarySubBonus, 'amt' => 5000000, 'd' => '2026-03-25', 'desc' => 'Annual Bonus'],
            // freelance
            ['a' => 'BCA Checking', 'c' => $freelanceId, 's' => $freelanceSubWeb, 'amt' => 5000000, 'd' => '2026-03-15', 'desc' => 'Web Dev Project - PT Maju Jaya'],
            ['a' => 'BCA Checking', 'c' => $freelanceId, 's' => $freelanceSubDesign, 'amt' => 2500000, 'd' => '2026-05-10', 'desc' => 'Logo Design - StartupXYZ'],
            // investment
            ['a' => 'Mandiri Savings', 'c' => $investId, 's' => $subCatMap['Investment']['Dividends'] ?? null, 'amt' => 750000, 'd' => '2026-04-10', 'desc' => 'Stock Dividend - BBCA'],
            // other
            ['a' => 'BCA Checking', 'c' => $otherIncomeId, 's' => null, 'amt' => 500000, 'd' => '2026-04-15', 'desc' => 'THR (Holiday Allowance)'],
        ];

        foreach ($incomes as $t) {
            if ($isFuture($t['d'])) continue;
            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs[$t['a']]->id,
                'category_id' => $t['c'],
                'sub_category_id' => $t['s'],
                'amount' => $t['amt'],
                'type' => 'income',
                'description' => $t['desc'],
                'date' => $t['d'],
                'is_recurring' => $t['desc'] === 'Monthly Salary',
                'recurring_interval' => $t['desc'] === 'Monthly Salary' ? 'monthly' : null,
            ]);
        }

        // ── 6. Expense Transactions ─────────────────────────────
        $foodId = $catsByName['Food & Drinks']->id;
        $transpId = $catsByName['Transportation']->id;
        $billsId = $catsByName['Bills & Utilities']->id;
        $shopId = $catsByName['Shopping']->id;
        $entId = $catsByName['Entertainment']->id;
        $healthId = $catsByName['Health']->id;
        $eduId = $catsByName['Education']->id;

        $expenses = [
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 150000, 'd' => '2026-01-05', 'desc' => 'Lunch at Sederhana'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 250000, 'd' => '2026-01-12', 'desc' => 'Dinner at Plataran'],
            ['a' => 'GoPay', 'c' => $foodId, 's' => 'Coffee & Snacks', 'amt' => 85000, 'd' => '2026-01-19', 'desc' => 'Coffee & Pastry'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Groceries', 'amt' => 200000, 'd' => '2026-01-15', 'desc' => 'Groceries - Superindo'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 120000, 'd' => '2026-02-03', 'desc' => 'Lunch at Solaria'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 350000, 'd' => '2026-02-14', 'desc' => 'Valentine Dinner'],
            ['a' => 'GoPay', 'c' => $foodId, 's' => 'Coffee & Snacks', 'amt' => 95000, 'd' => '2026-02-20', 'desc' => 'Coffee Meeting'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Groceries', 'amt' => 185000, 'd' => '2026-02-16', 'desc' => 'Groceries - Transmart'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 175000, 'd' => '2026-03-07', 'desc' => 'Brunch at GI'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Delivery', 'amt' => 130000, 'd' => '2026-03-28', 'desc' => 'Sushi Delivery'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 160000, 'd' => '2026-04-04', 'desc' => 'Lunch with Client'],
            ['a' => 'GoPay', 'c' => $foodId, 's' => 'Coffee & Snacks', 'amt' => 90000, 'd' => '2026-04-18', 'desc' => 'Coffee & Snacks'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 280000, 'd' => '2026-04-25', 'desc' => 'Family Dinner'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 110000, 'd' => '2026-05-02', 'desc' => 'Lunch at Pizza Hut'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Groceries', 'amt' => 195000, 'd' => '2026-05-16', 'desc' => 'Groceries - Ranch Market'],
            ['a' => 'GoPay', 'c' => $foodId, 's' => 'Coffee & Snacks', 'amt' => 85000, 'd' => '2026-05-30', 'desc' => 'Bubble Tea & Pastry'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 145000, 'd' => '2026-06-06', 'desc' => 'Lunch at Solaria'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Groceries', 'amt' => 220000, 'd' => '2026-06-14', 'desc' => 'Groceries - Farmers Market'],
            ['a' => 'Cash', 'c' => $foodId, 's' => 'Restaurant', 'amt' => 175000, 'd' => '2026-06-20', 'desc' => 'Dinner Outing'],

            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 500000, 'd' => '2026-01-07', 'desc' => 'Fuel - Pertamina'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Public Transport', 'amt' => 45000, 'd' => '2026-02-05', 'desc' => 'MRT Pass'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 550000, 'd' => '2026-02-18', 'desc' => 'Fuel - Shell'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Taxi/RideShare', 'amt' => 60000, 'd' => '2026-03-08', 'desc' => 'Grab to Airport'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 480000, 'd' => '2026-03-22', 'desc' => 'Fuel - Pertamina'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Taxi/RideShare', 'amt' => 35000, 'd' => '2026-04-03', 'desc' => 'Gojek to Meeting'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 520000, 'd' => '2026-04-19', 'desc' => 'Fuel - Shell'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Parking', 'amt' => 75000, 'd' => '2026-05-11', 'desc' => 'Parking - Plaza Senayan'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 490000, 'd' => '2026-05-25', 'desc' => 'Fuel - Pertamina'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Taxi/RideShare', 'amt' => 55000, 'd' => '2026-06-07', 'desc' => 'Grab to Office'],
            ['a' => 'GoPay', 'c' => $transpId, 's' => 'Fuel', 'amt' => 510000, 'd' => '2026-06-21', 'desc' => 'Fuel - Shell'],

            // Bills & Utilities (monthly recurring)
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 850000, 'd' => '2026-01-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-01-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-01-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-01-13', 'desc' => 'Phone - Telkomsel'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 820000, 'd' => '2026-02-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-02-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-02-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-02-13', 'desc' => 'Phone - Telkomsel'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 900000, 'd' => '2026-03-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-03-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-03-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-03-13', 'desc' => 'Phone - Telkomsel'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 780000, 'd' => '2026-04-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-04-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-04-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-04-13', 'desc' => 'Phone - Telkomsel'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 860000, 'd' => '2026-05-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-05-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-05-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-05-13', 'desc' => 'Phone - Telkomsel'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Electricity', 'amt' => 840000, 'd' => '2026-06-10', 'desc' => 'Electricity - PLN'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Water', 'amt' => 450000, 'd' => '2026-06-11', 'desc' => 'Water - PAM'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Internet', 'amt' => 500000, 'd' => '2026-06-12', 'desc' => 'Internet - Biznet'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Phone', 'amt' => 200000, 'd' => '2026-06-13', 'desc' => 'Phone - Telkomsel'],

            // Rent (monthly)
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-01-01', 'desc' => 'Apartment Rent - Kuningan City'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-02-01', 'desc' => 'Apartment Rent - Kuningan City'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-03-01', 'desc' => 'Apartment Rent - Kuningan City'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-04-01', 'desc' => 'Apartment Rent - Kuningan City'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-05-01', 'desc' => 'Apartment Rent - Kuningan City'],
            ['a' => 'BCA Checking', 'c' => $billsId, 's' => 'Rent', 'amt' => 5000000, 'd' => '2026-06-01', 'desc' => 'Apartment Rent - Kuningan City'],

            // Shopping
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Clothing', 'amt' => 750000, 'd' => '2026-01-20', 'desc' => 'Clothing - Uniqlo'],
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Electronics', 'amt' => 1200000, 'd' => '2026-02-10', 'desc' => 'Wireless Earbuds'],
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Home & Garden', 'amt' => 450000, 'd' => '2026-03-05', 'desc' => 'Home Decor - Informa'],
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Electronics', 'amt' => 250000, 'd' => '2026-04-12', 'desc' => 'USB Hub'],
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Clothing', 'amt' => 550000, 'd' => '2026-05-18', 'desc' => 'Shoes - Nike'],
            ['a' => 'BRI Credit Card', 'c' => $shopId, 's' => 'Personal Care', 'amt' => 350000, 'd' => '2026-06-08', 'desc' => 'Skincare - Sephora'],

            // Entertainment
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Movies', 'amt' => 100000, 'd' => '2026-01-22', 'desc' => 'Movie Ticket - Noxxi'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Streaming', 'amt' => 159000, 'd' => '2026-02-01', 'desc' => 'Netflix Subscription'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Hobbies', 'amt' => 300000, 'd' => '2026-03-18', 'desc' => 'Photography Workshop'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Streaming', 'amt' => 159000, 'd' => '2026-03-01', 'desc' => 'Netflix Subscription'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Streaming', 'amt' => 159000, 'd' => '2026-04-01', 'desc' => 'Netflix Subscription'],
            ['a' => 'Cash', 'c' => $entId, 's' => 'Travel', 'amt' => 2000000, 'd' => '2026-04-20', 'desc' => 'Weekend Trip to Bandung'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Streaming', 'amt' => 159000, 'd' => '2026-05-01', 'desc' => 'Netflix Subscription'],
            ['a' => 'Cash', 'c' => $entId, 's' => 'Movies', 'amt' => 120000, 'd' => '2026-06-05', 'desc' => 'Movie Ticket'],
            ['a' => 'GoPay', 'c' => $entId, 's' => 'Streaming', 'amt' => 159000, 'd' => '2026-06-01', 'desc' => 'Netflix Subscription'],

            // Health
            ['a' => 'Cash', 'c' => $healthId, 's' => 'Medical', 'amt' => 350000, 'd' => '2026-02-08', 'desc' => 'General Checkup'],
            ['a' => 'Cash', 'c' => $healthId, 's' => 'Pharmacy', 'amt' => 85000, 'd' => '2026-03-12', 'desc' => 'Vitamins - Guardian'],
            ['a' => 'Cash', 'c' => $healthId, 's' => 'Fitness', 'amt' => 450000, 'd' => '2026-04-01', 'desc' => 'Gym Membership'],
            ['a' => 'Cash', 'c' => $healthId, 's' => 'Pharmacy', 'amt' => 120000, 'd' => '2026-05-20', 'desc' => 'Medicine - Century'],
            ['a' => 'Cash', 'c' => $healthId, 's' => 'Fitness', 'amt' => 450000, 'd' => '2026-06-01', 'desc' => 'Gym Membership'],

            // Education
            ['a' => 'BCA Checking', 'c' => $eduId, 's' => 'Courses', 'amt' => 1500000, 'd' => '2026-01-15', 'desc' => 'Online Course - Laravel'],
            ['a' => 'BCA Checking', 'c' => $eduId, 's' => 'Books', 'amt' => 350000, 'd' => '2026-03-20', 'desc' => 'Technical Books'],
            ['a' => 'BCA Checking', 'c' => $eduId, 's' => 'Courses', 'amt' => 2000000, 'd' => '2026-05-05', 'desc' => 'Data Science Bootcamp'],
        ];

        $recurringSubs = ['Electricity', 'Water', 'Internet', 'Phone', 'Rent', 'Streaming', 'Fitness'];

        foreach ($expenses as $t) {
            if ($isFuture($t['d'])) continue;
            $catName = $cats->find($t['c'])?->name ?? '';
            $subId = $t['s'] ? ($subCatMap[$catName][$t['s']] ?? null) : null;

            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs[$t['a']]->id,
                'category_id' => $t['c'],
                'sub_category_id' => $subId,
                'amount' => $t['amt'],
                'type' => 'expense',
                'description' => $t['desc'],
                'date' => $t['d'],
                'is_recurring' => in_array($t['s'] ?? '', $recurringSubs),
                'recurring_interval' => in_array($t['s'] ?? '', $recurringSubs) ? 'monthly' : null,
            ]);
        }

        // ── 7. Transfers Between Accounts ───────────────────────
        $transferExpCat = $cats->where('name', 'Transfer')->where('type', 'expense')->first();
        $transferIncCat = $cats->where('name', 'Transfer')->where('type', 'income')->first();

        $transfers = [
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-01-02'],
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-02-02'],
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-03-02'],
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-04-02'],
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-05-02'],
            ['from' => 'BCA Checking', 'to' => 'GoPay', 'amt' => 500000, 'd' => '2026-06-02'],
        ];

        foreach ($transfers as $tr) {
            if ($isFuture($tr['d'])) continue;
            $expenseTx = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs[$tr['from']]->id,
                'category_id' => $transferExpCat->id,
                'amount' => $tr['amt'],
                'type' => 'expense',
                'description' => "Transfer to {$tr['to']}",
                'date' => $tr['d'],
            ]);
            $incomeTx = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs[$tr['to']]->id,
                'category_id' => $transferIncCat->id,
                'amount' => $tr['amt'],
                'type' => 'income',
                'description' => "Transfer from {$tr['from']}",
                'date' => $tr['d'],
            ]);
            $expenseTx->update(['transfer_id' => $incomeTx->id]);
            $incomeTx->update(['transfer_id' => $expenseTx->id]);
        }

        // ── 8. Savings Transactions ─────────────────────────────
        $savingsCatId = $catsByName['Savings']->id;
        $savingsSubId = $subCatMap['Savings']['Emergency Fund'] ?? null;

        foreach (range(1, 6) as $m) {
            $d = sprintf('2026-%02d-28', $m);
            if ($isFuture($d)) continue;
            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs['Emergency Fund']->id,
                'category_id' => $savingsCatId,
                'sub_category_id' => $savingsSubId,
                'amount' => 2000000,
                'type' => 'expense',
                'is_savings' => true,
                'description' => "Monthly Savings - Month {$m}",
                'date' => $d,
                'is_recurring' => true,
                'recurring_interval' => 'monthly',
            ]);
        }

        // ── 9. Budgets ──────────────────────────────────────────
        $budgetsDef = [
            'Food & Drinks' => 3000000,
            'Transportation' => 2000000,
            'Bills & Utilities' => 8000000,
            'Shopping' => 2000000,
            'Entertainment' => 1500000,
            'Health' => 1000000,
            'Education' => 2000000,
        ];

        foreach ([$nowMonth - 1, $nowMonth] as $bm) {
            if ($bm < 1) continue;
            $by = $nowYear;
            if ($bm > 12) { $bm = 1; $by++; }
            if ($by > $nowYear || ($by === $nowYear && $bm > $nowMonth)) continue;

            foreach ($budgetsDef as $bname => $bamt) {
                $bc = $catsByName->get($bname);
                if (!$bc) continue;
                Budget::create([
                    'user_id' => $user->id,
                    'category_id' => $bc->id,
                    'month' => $bm,
                    'year' => $by,
                    'amount' => $bamt,
                    'notify_at' => 80.00,
                ]);
            }
        }

        // ── 10. Goals ────────────────────────────────────────────
        $goals = [
            ['name' => 'Emergency Fund', 'target' => 100000000, 'current' => 45000000, 'date' => '2027-06-30', 'icon' => 'umbrella', 'color' => '#EF4444'],
            ['name' => 'New MacBook Pro', 'target' => 25000000, 'current' => 15000000, 'date' => '2026-09-15', 'icon' => 'laptop', 'color' => '#6366F1'],
            ['name' => 'Bali Vacation', 'target' => 15000000, 'current' => 5000000, 'date' => '2026-12-20', 'icon' => 'plane', 'color' => '#3B82F6'],
        ];

        foreach ($goals as $g) {
            Goal::create([
                'user_id' => $user->id,
                'name' => $g['name'],
                'target_amount' => $g['target'],
                'current_amount' => $g['current'],
                'target_date' => $g['date'],
                'icon' => $g['icon'],
                'color' => $g['color'],
                'is_achieved' => false,
            ]);
        }

        // ── 11. Loans ───────────────────────────────────────────
        $loan1 = Loan::create([
            'user_id' => $user->id,
            'account_id' => $accs['BCA Checking']->id,
            'name' => 'Car Loan - Honda Civic',
            'type' => 'borrow',
            'lender_name' => 'Bank Mandiri',
            'amount' => 300000000,
            'interest_rate' => 6.5,
            'paid_amount' => 0,
            'remaining_amount' => 300000000,
            'start_date' => '2025-06-01',
            'due_date' => '2030-06-01',
            'notes' => 'Monthly installment: Rp 5,870,000',
            'status' => 'active',
        ]);

        $loan2 = Loan::create([
            'user_id' => $user->id,
            'account_id' => $accs['Cash']->id,
            'name' => 'Loan to Budi',
            'type' => 'lend',
            'lender_name' => 'Budi Santoso',
            'amount' => 15000000,
            'interest_rate' => 0,
            'paid_amount' => 0,
            'remaining_amount' => 15000000,
            'start_date' => '2026-01-15',
            'due_date' => '2026-07-15',
            'notes' => 'Emergency loan for friend',
            'status' => 'active',
        ]);

        $loanPayments = [
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-01-05', 'n' => 'Monthly installment'],
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-02-05', 'n' => 'Monthly installment'],
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-03-05', 'n' => 'Monthly installment'],
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-04-05', 'n' => 'Monthly installment'],
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-05-05', 'n' => 'Monthly installment'],
            ['l' => $loan1, 'a' => 'BCA Checking', 'amt' => 5870000, 'd' => '2026-06-05', 'n' => 'Monthly installment'],
            ['l' => $loan2, 'a' => 'Cash', 'amt' => 1000000, 'd' => '2026-02-20', 'n' => 'Partial repayment'],
            ['l' => $loan2, 'a' => 'Cash', 'amt' => 1000000, 'd' => '2026-04-10', 'n' => 'Partial repayment'],
            ['l' => $loan2, 'a' => 'Cash', 'amt' => 1000000, 'd' => '2026-06-15', 'n' => 'Partial repayment'],
        ];

        foreach ($loanPayments as $lp) {
            if ($isFuture($lp['d'])) continue;
            $txType = $lp['l']->type === 'borrow' ? 'expense' : 'income';
            $txCatId = $lp['l']->type === 'borrow' ? $billsId : $otherIncomeId;

            $tx = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accs[$lp['a']]->id,
                'category_id' => $txCatId,
                'amount' => $lp['amt'],
                'type' => $txType,
                'loan_id' => $lp['l']->id,
                'description' => "{$lp['n']} - {$lp['l']->name}",
                'date' => $lp['d'],
            ]);

            LoanPayment::create([
                'loan_id' => $lp['l']->id,
                'account_id' => $accs[$lp['a']]->id,
                'transaction_id' => $tx->id,
                'amount' => $lp['amt'],
                'payment_date' => $lp['d'],
                'notes' => $lp['n'],
            ]);
        }

        // Update loan paid/remaining amounts
        foreach ([$loan1, $loan2] as $ln) {
            $totalPaid = (float) $ln->payments()->sum('amount');
            $ln->paid_amount = $totalPaid;
            $ln->remaining_amount = $ln->amount - $totalPaid;
            $ln->save();
        }

        // ── 12. Recalculate Account Balances ────────────────────
        foreach ($accs as $acc) {
            $incomeSum = (float) Transaction::where('account_id', $acc->id)->where('type', 'income')->sum('amount');
            $expenseSum = (float) Transaction::where('account_id', $acc->id)->where('type', 'expense')->sum('amount');
            $savingsSum = (float) Transaction::where('account_id', $acc->id)->where('type', 'expense')->where('is_savings', true)->sum('amount');

            if ($acc->category === 'savings') {
                // Savings expenses represent money going INTO the savings account
                $acc->current_balance = $acc->initial_balance + $incomeSum + $savingsSum;
            } else {
                $acc->current_balance = $acc->initial_balance + $incomeSum - $expenseSum;
            }
            $acc->save();
        }

        // ── Summary ─────────────────────────────────────────────
        $totalTx = Transaction::where('user_id', $user->id)->count();
        $this->command->info("✅ Demo user created: demo@financia.com / password (IDR)");
        $this->command->info("   Accounts: " . count($accs));
        $this->command->info("   Transactions: {$totalTx}");
        $this->command->info("   Budgets: " . count($budgetsDef) . " categories");
        $this->command->info("   Goals: " . count($goals));
        $this->command->info("   Loans: 2 with " . count($loanPayments) . " payments");
    }
}
