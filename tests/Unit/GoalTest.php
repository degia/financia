<?php

namespace Tests\Unit;

use App\Models\Goal;
use App\Models\User;
use Database\Factories\GoalFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Goal())->getFillable();

        $this->assertEquals([
            'user_id',
            'name',
            'target_amount',
            'current_amount',
            'target_date',
            'icon',
            'color',
            'is_achieved',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Goal())->getCasts();

        $this->assertEquals('decimal:2', $casts['target_amount']);
        $this->assertEquals('decimal:2', $casts['current_amount']);
        $this->assertEquals('date', $casts['target_date']);
        $this->assertEquals('boolean', $casts['is_achieved']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $goal = GoalFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $goal->user());
        $this->assertInstanceOf(User::class, $goal->user);
        $this->assertTrue($goal->user->is($user));
    }
}
