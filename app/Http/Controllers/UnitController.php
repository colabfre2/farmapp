<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{


public function index(Request $request)
{
    $query = $request->input('q');
    $units = \App\Models\Unit::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    return view('admin.units.index', compact('units', 'query'));

}

public function create()
{
    return view('admin.units.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'symbol' => 'required|string|max:20',
    ]);

    Unit::create($request->all());

    return redirect()->route('admin.units.index')->with('success', 'Unit created successfully!');
}

public function edit(Unit $unit)
{
    return view('admin.units.edit', compact('unit'));
}

public function update(Request $request, Unit $unit)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'symbol' => 'required|string|max:20',
    ]);

    $unit->update($request->all());

    return redirect()->route('admin.units.index')->with('success', 'Unit updated successfully!');
}

public function destroy(Unit $unit)
{
    $unit->delete();

    return redirect()->route('admin.units.index')->with('success', 'Unit deleted successfully!');
}
}
