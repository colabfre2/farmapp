<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;

class FarmController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $farms = Farm::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->withCount('crops')
            ->latest()->get();
        return view('admin.farms.index', compact('farms', 'query'));
    }

    public function create()
    {
        return view('admin.farms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'area_size'   => 'nullable|numeric|min:0',
            'area_unit'   => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Farm::create([
            'name'        => $request->name,
            'area_size'   => $request->area_size,
            'area_unit'   => $request->area_unit ?? 'm²',
            'description' => $request->description,
        ]);

        return redirect()->route('admin.farms.index')->with('success', 'Ladang berhasil ditambahkan!');
    }

    public function edit(Farm $farm)
    {
        return view('admin.farms.edit', compact('farm'));
    }

    public function update(Request $request, Farm $farm)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'area_size'   => 'nullable|numeric|min:0',
            'area_unit'   => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $farm->update($request->all());

        return redirect()->route('admin.farms.index')->with('success', 'Ladang berhasil diperbarui!');
    }

    public function destroy(Farm $farm)
    {
        $farm->delete();
        return redirect()->route('admin.farms.index')->with('success', 'Ladang berhasil dihapus!');
    }
}