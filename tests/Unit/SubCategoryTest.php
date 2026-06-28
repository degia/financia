<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Transaction;
use Database\Factories\CategoryFactory;
use Database\Factories\SubCategoryFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new SubCategory())->getFillable();

        $this->assertEquals([
            'category_id',
            'name',
        ], $fillable);
    }

    public function test_no_custom_casts()
    {
        $casts = (new SubCategory())->getCasts();

        $this->assertArrayNotHasKey('name', $casts);
    }

    public function test_category_relationship()
    {
        $category = CategoryFactory::new()->create();
        $subCategory = SubCategoryFactory::new()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(BelongsTo::class, $subCategory->category());
        $this->assertInstanceOf(Category::class, $subCategory->category);
        $this->assertTrue($subCategory->category->is($category));
    }

    public function test_transactions_relationship()
    {
        $subCategory = SubCategoryFactory::new()->create();
        $transaction = TransactionFactory::new()->create(['sub_category_id' => $subCategory->id]);

        $this->assertInstanceOf(HasMany::class, $subCategory->transactions());
        $this->assertInstanceOf(Transaction::class, $subCategory->transactions->first());
        $this->assertTrue($subCategory->transactions->first()->is($transaction));
    }
}
