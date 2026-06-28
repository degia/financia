<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('accounts.index'));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Institution::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('accounts.create'));

        $response->assertOk();
    }

    public function test_can_store_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('accounts.store'), [
            'name' => 'Test Account',
            'type' => 'bank',
            'category' => 'real',
            'initial_balance' => 1000,
            'currency' => 'USD',
            'color' => '#6366F1',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('accounts.index'));

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'Test Account',
            'type' => 'bank',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('accounts.store'), []);

        $response->assertSessionHasErrors(['name', 'type', 'category']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('accounts.edit', $account));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('accounts.edit', $account));

        $response->assertForbidden();
    }

    public function test_can_update_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('accounts.update', $account), [
            'name' => 'Updated Account',
            'type' => 'savings',
            'category' => 'savings',
            'color' => '#FF0000',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('accounts.index'));

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Account',
            'type' => 'savings',
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('accounts.update', $account), [
            'name' => 'Hacked',
            'type' => 'bank',
            'category' => 'real',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('accounts.update', $account), []);

        $response->assertSessionHasErrors(['name', 'type', 'category']);
    }

    public function test_can_destroy_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('accounts.destroy', $account));

        $response->assertSessionHasNoErrors()->assertRedirect(route('accounts.index'));

        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('accounts.destroy', $account));

        $response->assertForbidden();
    }
}
