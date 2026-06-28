<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->user_id && $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $sub = $category->subCategories()->create([
            'name' => $validated['name'],
        ]);

        return redirect()->back()
            ->with('success', 'Sub-category "' . $sub->name . '" added.');
    }

    public function update(Request $request, SubCategory $subCategory): RedirectResponse
    {
        if ($subCategory->category->user_id && $subCategory->category->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $subCategory->update($validated);

        return redirect()->back()
            ->with('success', 'Sub-category renamed to "' . $subCategory->name . '".');
    }

    public function destroy(Request $request, SubCategory $subCategory): RedirectResponse
    {
        if ($subCategory->category->user_id && $subCategory->category->user_id !== $request->user()->id) {
            abort(403);
        }

        $subCategory->delete();

        return redirect()->back()
            ->with('success', 'Sub-category deleted.');
    }
}
