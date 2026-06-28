<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\BudgetFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\GoalFactory;
use Database\Factories\LoanFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new User())->getFillable();

        $this->assertEquals([
            'name',
            'email',
            'password',
            'currency_preference',
        ], $fillable);
    }

    public function test_hidden_attributes()
    {
        $hidden = (new User())->getHidden();

        $this->assertEquals([
            'password',
            'remember_token',
        ], $hidden);
    }

    public function test_casts()
    {
        $casts = (new User())->getCasts();

        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
        $this->assertEquals('array', $casts['preferences']);
    }

    public function test_accounts_relationship()
    {
        $user = User::factory()->create();
        $account = AccountFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->accounts());
        $this->assertInstanceOf(Account::class, $user->accounts->first());
        $this->assertTrue($user->accounts->first()->is($account));
    }

    public function test_categories_relationship()
    {
        $user = User::factory()->create();
        $category = CategoryFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->categories());
        $this->assertInstanceOf(Category::class, $user->categories->first());
        $this->assertTrue($user->categories->first()->is($category));
    }

    public function test_transactions_relationship()
    {
        $user = User::factory()->create();
        $transaction = TransactionFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->transactions());
        $this->assertInstanceOf(Transaction::class, $user->transactions->first());
        $this->assertTrue($user->transactions->first()->is($transaction));
    }

    public function test_budgets_relationship()
    {
        $user = User::factory()->create();
        $budget = BudgetFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->budgets());
        $this->assertInstanceOf(Budget::class, $user->budgets->first());
        $this->assertTrue($user->budgets->first()->is($budget));
    }

    public function test_goals_relationship()
    {
        $user = User::factory()->create();
        $goal = GoalFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->goals());
        $this->assertInstanceOf(Goal::class, $user->goals->first());
        $this->assertTrue($user->goals->first()->is($goal));
    }

    public function test_loans_relationship()
    {
        $user = User::factory()->create();
        $loan = LoanFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->loans());
        $this->assertInstanceOf(Loan::class, $user->loans->first());
        $this->assertTrue($user->loans->first()->is($loan));
    }

    public function test_preference_methods()
    {
        $user = User::factory()->create();

        $user->setPreference('theme', 'dark');
        $user->save();

        $this->assertEquals('dark', $user->preference('theme'));
        $this->assertNull($user->preference('nonexistent'));
        $this->assertEquals('default', $user->preference('nonexistent', 'default'));
    }
}
