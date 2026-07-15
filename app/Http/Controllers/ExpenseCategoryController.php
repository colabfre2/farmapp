<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
public function index(Request $request)
{
    $query = $request->input('q');
    $expenseCategories = \App\Models\ExpenseCategory::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    return view('admin.expense-categories.index', compact('expenseCategories', 'query'));

}

public function create()
{
    return view('admin.expense-categories.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    ExpenseCategory::create($request->all());

    return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category created successfully!');
}

public function edit(ExpenseCategory $expenseCategory)
{
    return view('admin.expense-categories.edit', compact('expenseCategory'));
}

public function update(Request $request, ExpenseCategory $expenseCategory)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $expenseCategory->update($request->all());

    return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category updated successfully!');
}

public function destroy(ExpenseCategory $expenseCategory)
{
    $expenseCategory->delete();

    return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category deleted successfully!');
}
}
