<?php

namespace App\Http\Controllers;

use App\Models\CropType;
use Illuminate\Http\Request;

class CropTypeController extends Controller
{


public function index(Request $request)
{
    $query = $request->input('q');
    $cropTypes = \App\Models\CropType::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    return view('admin.crop-types.index', compact('cropTypes', 'query'));

}

public function create()
{
    return view('admin.crop-types.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    CropType::create($request->all());

    return redirect()->route('admin.crop-types.index')->with('success', 'Crop Type created successfully!');
}

public function edit(CropType $cropType)
{
    return view('admin.crop-types.edit', compact('cropType'));
}

public function update(Request $request, CropType $cropType)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $cropType->update($request->all());

    return redirect()->route('admin.crop-types.index')->with('success', 'Crop Type updated successfully!');
}

public function destroy(CropType $cropType)
{
    $cropType->delete();

    return redirect()->route('admin.crop-types.index')->with('success', 'Crop Type deleted successfully!');
}
}
