<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        $category->transactions()->delete();
        $category->delete();
    }
}
