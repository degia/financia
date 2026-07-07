<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        Institution::factory()->create();

        $response = $this->actingAs($user)->get(route('institutions.index'));

        $response->assertOk();
    }

    public function test_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('institutions.create'));

        $response->assertOk();
    }

    public function test_can_store_institution(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('institutions.store'), [
            'name' => 'Test Bank',
            'type' => 'bank',
            'color' => '#FF0000',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('institutions.index'));

        $this->assertDatabaseHas('institutions', [
            'name' => 'Test Bank',
            'type' => 'bank',
            'is_active' => true,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('institutions.store'), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();

        $response = $this->actingAs($user)->get(route('institutions.edit', $institution));

        $response->assertOk();
    }

    public function test_can_update_institution(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();

        $response = $this->actingAs($user)->patch(route('institutions.update', $institution), [
            'name' => 'Updated Institution',
            'type' => 'ewallet',
            'color' => '#00FF00',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('institutions.index'));

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'name' => 'Updated Institution',
            'type' => 'ewallet',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();

        $response = $this->actingAs($user)->patch(route('institutions.update', $institution), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_can_destroy_institution(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();

        $response = $this->actingAs($user)->delete(route('institutions.destroy', $institution));

        $response->assertSessionHasNoErrors()->assertRedirect(route('institutions.index'));

        $this->assertDatabaseMissing('institutions', ['id' => $institution->id]);
    }

    public function test_destroy_fails_if_institution_has_linked_accounts(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();
        Account::factory()->for($user)->create(['institution_id' => $institution->id]);

        $response = $this->actingAs($user)->delete(route('institutions.destroy', $institution));

        $response->assertRedirect();
        $this->assertDatabaseHas('institutions', ['id' => $institution->id]);
    }
}
