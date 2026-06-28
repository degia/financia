<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Institution;
use App\Models\Transaction;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Account())->getFillable();

        $this->assertEquals([
            'user_id',
            'institution_id',
            'name',
            'type',
            'category',
            'initial_balance',
            'current_balance',
            'currency',
            'icon',
            'color',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Account())->getCasts();

        $this->assertEquals('decimal:2', $casts['initial_balance']);
        $this->assertEquals('decimal:2', $casts['current_balance']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $account = AccountFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $account->user());
        $this->assertInstanceOf(User::class, $account->user);
        $this->assertTrue($account->user->is($user));
    }

    public function test_institution_relationship()
    {
        $institution = Institution::factory()->create();
        $account = AccountFactory::new()->create(['institution_id' => $institution->id]);

        $this->assertInstanceOf(BelongsTo::class, $account->institution());
        $this->assertInstanceOf(Institution::class, $account->institution);
        $this->assertTrue($account->institution->is($institution));
    }

    public function test_transactions_relationship()
    {
        $account = AccountFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(HasMany::class, $account->transactions());
        $this->assertInstanceOf(Transaction::class, $account->transactions->first());
        $this->assertTrue($account->transactions->first()->is($transaction));
    }
}
