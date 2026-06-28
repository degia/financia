<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('goals.index'));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('goals.create'));

        $response->assertOk();
    }

    public function test_can_store_goal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('goals.store'), [
            'name' => 'Test Goal',
            'target_amount' => 5000.00,
            'target_date' => '2026-12-31',
            'color' => '#FF0000',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('goals.index'));

        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 5000.00,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('goals.store'), []);

        $response->assertSessionHasErrors(['name', 'target_amount', 'target_date']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('goals.edit', $goal));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('goals.edit', $goal));

        $response->assertForbidden();
    }

    public function test_can_update_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('goals.update', $goal), [
            'name' => 'Updated Goal',
            'target_amount' => 10000.00,
            'target_date' => '2027-06-30',
            'color' => '#00FF00',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('goals.index'));

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'name' => 'Updated Goal',
            'target_amount' => 10000.00,
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('goals.update', $goal), [
            'name' => 'Hacked',
            'target_amount' => 100,
            'target_date' => '2026-01-01',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('goals.update', $goal), []);

        $response->assertSessionHasErrors(['name', 'target_amount', 'target_date']);
    }

    public function test_can_destroy_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('goals.destroy', $goal));

        $response->assertSessionHasNoErrors()->assertRedirect(route('goals.index'));

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('goals.destroy', $goal));

        $response->assertForbidden();
    }

    public function test_can_contribute_to_goal(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create(['current_amount' => 1000]);

        $response = $this->actingAs($user)->post(route('goals.contribute', $goal), [
            'amount' => 500,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('goals.index'));

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'current_amount' => 1500,
        ]);
    }

    public function test_contribute_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->for($other)->create();

        $response = $this->actingAs($user)->post(route('goals.contribute', $goal), [
            'amount' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_contribute_validates_amount(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('goals.contribute', $goal), [
            'amount' => -10,
        ]);

        $response->assertSessionHasErrors(['amount']);
    }
}
