<?php

namespace Tests\Unit;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Database\Factories\BudgetFactory;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Budget())->getFillable();

        $this->assertEquals([
            'user_id',
            'category_id',
            'amount',
            'month',
            'year',
            'notify_at',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Budget())->getCasts();

        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('decimal:2', $casts['notify_at']);
        $this->assertEquals('integer', $casts['month']);
        $this->assertEquals('integer', $casts['year']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $budget = BudgetFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $budget->user());
        $this->assertInstanceOf(User::class, $budget->user);
        $this->assertTrue($budget->user->is($user));
    }

    public function test_category_relationship()
    {
        $category = CategoryFactory::new()->create();
        $budget = BudgetFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(BelongsTo::class, $budget->category());
        $this->assertInstanceOf(Category::class, $budget->category);
        $this->assertTrue($budget->category->is($category));
    }
}
