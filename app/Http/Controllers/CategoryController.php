<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = $request->input('q');
    $categories = \App\Models\Category::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    return view('admin.categories.index', compact('categories', 'query'));


}

public function create()
{
    return view('admin.categories.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    Category::create($request->all());

    return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
}

public function edit(Category $category)
{
    return view('admin.categories.edit', compact('category'));
}

public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $category->update($request->all());

    return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
}

public function destroy(Category $category)
{
    $category->delete();

    return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
}
}
