<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Livestock;
use App\Models\LivestockType;


class LivestockController extends Controller
{
public function index(Request $request)
{
    $query = $request->input('q');

    $livestocks = Livestock::with('livestockType')
        ->when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->latest()
        ->get();

    return view('admin.livestock.index', compact('livestocks', 'query'));
}

public function create()
{
    $livestockTypes = LivestockType::all();
    return view('admin.livestock.create', compact('livestockTypes'));
}

public function store(Request $request)
{
    $request->validate([
        'livestock_type_id' => 'required|exists:livestock_types,id',
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'avg_weight' => 'nullable|string',
        'health_status' => 'required|in:Healthy,Monitoring,Sick',
        'notes' => 'nullable|string',
    ]);

    Livestock::create([
        'user_id' => auth()->id(),
        'livestock_type_id' => $request->livestock_type_id,
        'name' => $request->name,
        'quantity' => $request->quantity,
        'avg_weight' => $request->avg_weight,
        'health_status' => $request->health_status,
        'notes' => $request->notes,
    ]);

    return redirect()->route('admin.livestock.index')->with('success', 'Livestock added successfully!');
}

public function edit(Livestock $livestock)
{
    $livestockTypes = LivestockType::all();
    return view('admin.livestock.edit', compact('livestock', 'livestockTypes'));
}

public function update(Request $request, Livestock $livestock)
{
    $request->validate([
        'livestock_type_id' => 'required|exists:livestock_types,id',
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'avg_weight' => 'nullable|string',
        'health_status' => 'required|in:Healthy,Monitoring,Sick',
        'notes' => 'nullable|string',
    ]);

    $livestock->update($request->all());

    return redirect()->route('admin.livestock.index')->with('success', 'Livestock updated successfully!');
}

public function destroy(Livestock $livestock)
{
    $livestock->delete();

    return redirect()->route('admin.livestock.index')->with('success', 'Livestock moved to trash!');
}

public function trash()
{
    $livestocks = Livestock::onlyTrashed()->with('livestockType')->latest()->get();
    return view('admin.livestock.trash', compact('livestocks'));
}

public function restore($id)
{
    $livestock = Livestock::onlyTrashed()->findOrFail($id);
    $livestock->restore();

    return redirect()->route('admin.livestock.trash')->with('success', 'Livestock restored successfully!');
}

public function forceDelete($id)
{
    $livestock = Livestock::onlyTrashed()->findOrFail($id);
    $livestock->forceDelete();

    return redirect()->route('admin.livestock.trash')->with('success', 'Livestock permanently deleted!');
}
}
