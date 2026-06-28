<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.create'));

        $response->assertOk();
    }

    public function test_can_store_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Test Category',
            'type' => 'expense',
            'color' => '#FF0000',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Test Category',
            'type' => 'expense',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('categories.edit', $category));

        $response->assertOk();
    }

    public function test_edit_page_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->for($other)->create();

        $response = $this->actingAs($user)->get(route('categories.edit', $category));

        $response->assertForbidden();
    }

    public function test_can_update_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['type' => 'expense']);

        $response = $this->actingAs($user)->patch(route('categories.update', $category), [
            'name' => 'Updated Category',
            'type' => 'income',
            'color' => '#00FF00',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'type' => 'income',
        ]);
    }

    public function test_update_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->for($other)->create();

        $response = $this->actingAs($user)->patch(route('categories.update', $category), [
            'name' => 'Hacked',
            'type' => 'expense',
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('categories.update', $category), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_can_destroy_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertSessionHasNoErrors()->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_returns_403_for_another_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->for($other)->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertForbidden();
    }
}
