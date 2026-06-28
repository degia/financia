<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Budget::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('budgets.index'));

        $response->assertOk();
    }

    public function test_index_accepts_month_and_year_parameters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('budgets.index', ['month' => 3, 'year' => 2025]));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Category::factory()->for($user)->create(['type' => 'expense']);

        $response = $this->actingAs($user)->get(route('budgets.create'));

        $response->assertOk();
    }

    public function test_can_store_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['type' => 'expense']);

        $response = $this->actingAs($user)->post(route('budgets.store'), [
            'category_id' => $category->id,
            'amount' => 500.00,
            'month' => 6,
            'year' => 2025,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('budgets.index'));

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 500.00,
            'month' => 6,
            'year' => 2025,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('budgets.store'), []);

        $response->assertSessionHasErrors(['category_id', 'amount', 'month', 'year']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['type' => 'expense']);
        $budget = Budget::factory()->for($user)->for($category)->create();

        $response = $this->actingAs($user)->get(route('budgets.edit', $budget));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('budgets.edit', $budget));

        $response->assertForbidden();
    }

    public function test_can_update_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['type' => 'expense']);
        $budget = Budget::factory()->for($user)->for($category)->create();

        $response = $this->actingAs($user)->patch(route('budgets.update', $budget), [
            'category_id' => $category->id,
            'amount' => 750.00,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]));

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 750.00,
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('budgets.update', $budget), [
            'category_id' => $budget->category_id,
            'amount' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('budgets.update', $budget), []);

        $response->assertSessionHasErrors(['category_id', 'amount']);
    }

    public function test_can_destroy_budget(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('budgets.destroy', $budget));

        $response->assertSessionHasNoErrors()->assertRedirect(route('budgets.index'));

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('budgets.destroy', $budget));

        $response->assertForbidden();
    }
}
