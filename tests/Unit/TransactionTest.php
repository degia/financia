<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Transaction;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\SubCategoryFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Transaction())->getFillable();

        $this->assertEquals([
            'user_id',
            'account_id',
            'category_id',
            'sub_category_id',
            'transfer_id',
            'loan_id',
            'is_savings',
            'amount',
            'type',
            'description',
            'date',
            'is_recurring',
            'recurring_interval',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Transaction())->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('date', $casts['date']);
        $this->assertEquals('boolean', $casts['is_recurring']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $transaction = TransactionFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $transaction->user());
        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertTrue($transaction->user->is($user));
    }

    public function test_account_relationship()
    {
        $account = AccountFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(BelongsTo::class, $transaction->account());
        $this->assertInstanceOf(Account::class, $transaction->account);
        $this->assertTrue($transaction->account->is($account));
    }

    public function test_category_relationship()
    {
        $category = CategoryFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(BelongsTo::class, $transaction->category());
        $this->assertInstanceOf(Category::class, $transaction->category);
        $this->assertTrue($transaction->category->is($category));
    }

    public function test_sub_category_relationship()
    {
        $subCategory = SubCategoryFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['sub_category_id' => $subCategory->id]);

        $this->assertInstanceOf(BelongsTo::class, $transaction->subCategory());
        $this->assertInstanceOf(SubCategory::class, $transaction->subCategory);
        $this->assertTrue($transaction->subCategory->is($subCategory));
    }

    public function test_transfer_relationship()
    {
        $transfer = TransactionFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['transfer_id' => $transfer->id]);

        $this->assertInstanceOf(BelongsTo::class, $transaction->transfer());
        $this->assertInstanceOf(Transaction::class, $transaction->transfer);
        $this->assertTrue($transaction->transfer->is($transfer));
    }

    public function test_transfers_relationship()
    {
        $transaction = TransactionFactory::new()->create();
        $transfer = TransactionFactory::new()->create(['transfer_id' => $transaction->id]);

        $this->assertInstanceOf(HasMany::class, $transaction->transfers());
        $this->assertInstanceOf(Transaction::class, $transaction->transfers->first());
        $this->assertTrue($transaction->transfers->first()->is($transfer));
    }
}
