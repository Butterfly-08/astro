<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request): View
    {
        $query = ProductCategory::withCount('products')->ordered();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => ProductCategory::count(),
            'active'   => ProductCategory::where('status', 'active')->count(),
            'featured' => ProductCategory::where('is_featured', true)->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store newly created category.
     */
    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = ProductCategory::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' created successfully.");
    }

    /**
     * Show edit form for category.
     */
    public function edit(ProductCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Delete category.
     */
    public function destroy(ProductCategory $category): RedirectResponse
    {
        $name = $category->name;

        if ($category->products()->count() > 0) {
            return back()->with('error', "Cannot delete category '{$name}' because it contains {$category->products()->count()} products. Reassign or delete those products first.");
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }

    /**
     * Quick action: toggle category status.
     */
    public function toggleStatus(ProductCategory $category): RedirectResponse
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);

        return back()->with('success', "Category '{$category->name}' is now {$newStatus}.");
    }
}
