<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk();
    }

    public function test_index_can_filter_by_type(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->income()->create();
        Transaction::factory()->for($user)->expense()->create();

        $response = $this->actingAs($user)->get(route('transactions.index', ['type' => 'income']));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create();
        Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('transactions.create'));

        $response->assertOk();
    }

    public function test_can_store_transaction(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 150.50,
            'type' => 'expense',
            'description' => 'Test transaction',
            'date' => '2025-01-15',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => 150.50,
            'type' => 'expense',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), []);

        $response->assertSessionHasErrors(['account_id', 'category_id', 'amount', 'type', 'date']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($user)->for($account)->for($category)->create();

        $response = $this->actingAs($user)->get(route('transactions.edit', $transaction));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $transaction = Transaction::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('transactions.edit', $transaction));

        $response->assertForbidden();
    }

    public function test_can_update_transaction(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($user)->for($account)->for($category)->create();

        $newAccount = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('transactions.update', $transaction), [
            'account_id' => $newAccount->id,
            'category_id' => $category->id,
            'amount' => 300.00,
            'type' => 'income',
            'description' => 'Updated transaction',
            'date' => '2025-02-01',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 300.00,
            'type' => 'income',
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $transaction = Transaction::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('transactions.update', $transaction), [
            'account_id' => $transaction->account_id,
            'category_id' => $transaction->category_id,
            'amount' => 1,
            'type' => 'expense',
            'date' => '2025-01-01',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('transactions.update', $transaction), []);

        $response->assertSessionHasErrors(['account_id', 'category_id', 'amount', 'type', 'date']);
    }

    public function test_can_destroy_transaction(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

        $response->assertSessionHasNoErrors()->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $transaction = Transaction::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

        $response->assertForbidden();
    }
}
