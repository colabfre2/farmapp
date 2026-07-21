<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropType;

class CropController extends Controller
{
    public function index(Request $request)
{
    $query = $request->input('q')   ;

    $crops = Crop::with('cropType')
        ->when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->latest()
        ->get();

    return view('admin.crops.index', compact('crops', 'query'));
}

public function create()
{
    $cropTypes = CropType::all();
    return view('admin.crops.create', compact('cropTypes'));
}

public function store(Request $request)
{
    $request->validate([
        'crop_type_id' => 'required|exists:crop_types,id',
        'name' => 'required|string|max:255',
        'planted_at' => 'required|date',
        'expected_harvest_at' => 'required|date',
        'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
        'notes' => 'nullable|string',
    ]);

    Crop::create([
        'user_id' => auth()->id(),
        'crop_type_id' => $request->crop_type_id,
        'name' => $request->name,
        'planted_at' => $request->planted_at,
        'expected_harvest_at' => $request->expected_harvest_at,
        'status' => $request->status,
        'notes' => $request->notes,
    ]);

    return redirect()->route('admin.crops.index')->with('success', 'Crop added successfully!');
}

public function edit(Crop $crop)
{
    $cropTypes = CropType::all();
    return view('admin.crops.edit', compact('crop', 'cropTypes'));
}

public function update(Request $request, Crop $crop)
{
    $request->validate([
        'crop_type_id' => 'required|exists:crop_types,id',
        'name' => 'required|string|max:255',
        'planted_at' => 'required|date',
        'expected_harvest_at' => 'required|date',
        'actual_harvest_at' => 'nullable|date',
        'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
        'notes' => 'nullable|string',
    ]);

    $crop->update($request->all());

    return redirect()->route('admin.crops.index')->with('success', 'Crop updated successfully!');
}

public function destroy(Crop $crop)
{
    $crop->delete();

    return redirect()->route('admin.crops.index')->with('success', 'Crop moved to trash!');
}

public function trash()
{
    $crops = Crop::onlyTrashed()->with('cropType')->latest()->get();
    return view('admin.crops.trash', compact('crops'));
}

public function restore($id)
{
    $crop = Crop::onlyTrashed()->findOrFail($id);
    $crop->restore();

    return redirect()->route('admin.crops.trash')->with('success', 'Crop restored successfully!');
}

public function forceDelete($id)
{
    $crop = Crop::onlyTrashed()->findOrFail($id);
    $crop->forceDelete();

    return redirect()->route('admin.crops.trash')->with('success', 'Crop permanently deleted!');
}

}
