<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertOk();
    }

    public function test_can_update_settings(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['currency' => 'USD']);
        $expenseCategory = Category::factory()->for($user)->create(['type' => 'expense']);
        $incomeCategory = Category::factory()->for($user)->create(['type' => 'income']);

        $response = $this->actingAs($user)->patch(route('settings.update'), [
            'currency_preference' => 'EUR',
            'menu_visibility' => ['dashboard', 'accounts'],
            'default_account_id' => $account->id,
            'default_expense_category_id' => $expenseCategory->id,
            'default_income_category_id' => $incomeCategory->id,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('settings.index'));

        $user->refresh();
        $this->assertEquals('EUR', $user->currency_preference);
    }

    public function test_update_validates_currency_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('settings.update'), [
            'currency_preference' => 'INVALID',
        ]);

        $response->assertSessionHasErrors(['currency_preference']);
    }

    public function test_update_rejects_invalid_menu_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('settings.update'), [
            'currency_preference' => 'USD',
            'menu_visibility' => ['invalid_menu'],
        ]);

        $response->assertSessionHasErrors(['menu_visibility.0']);
    }

    public function test_update_rejects_account_from_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherAccount = Account::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('settings.update'), [
            'currency_preference' => 'USD',
            'default_account_id' => $otherAccount->id,
        ]);

        $response->assertSessionHasErrors(['default_account_id']);
    }
}
