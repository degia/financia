<?php

namespace Tests\Unit;

use App\Models\Budget;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Transaction;
use App\Models\User;
use Database\Factories\BudgetFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\SubCategoryFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Category())->getFillable();

        $this->assertEquals([
            'user_id',
            'name',
            'type',
            'icon',
            'color',
            'is_system',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Category())->getCasts();

        $this->assertEquals('boolean', $casts['is_system']);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $category = CategoryFactory::new()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $category->user());
        $this->assertInstanceOf(User::class, $category->user);
        $this->assertTrue($category->user->is($user));
    }

    public function test_transactions_relationship()
    {
        $category = CategoryFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(HasMany::class, $category->transactions());
        $this->assertInstanceOf(Transaction::class, $category->transactions->first());
        $this->assertTrue($category->transactions->first()->is($transaction));
    }

    public function test_budgets_relationship()
    {
        $category = CategoryFactory::new()->create();
        $budget = BudgetFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(HasMany::class, $category->budgets());
        $this->assertInstanceOf(Budget::class, $category->budgets->first());
        $this->assertTrue($category->budgets->first()->is($budget));
    }

    public function test_sub_categories_relationship()
    {
        $category = CategoryFactory::new()->create();
        $subCategory = SubCategoryFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(HasMany::class, $category->subCategories());
        $this->assertInstanceOf(SubCategory::class, $category->subCategories->first());
        $this->assertTrue($category->subCategories->first()->is($subCategory));
    }
}
