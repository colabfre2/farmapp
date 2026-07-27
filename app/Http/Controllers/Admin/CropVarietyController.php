<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CropVariety;
use App\Models\CropType;

class CropVarietyController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $cropVarieties = CropVariety::with('cropType')
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->latest()->get();
        return view('admin.crop-varieties.index', compact('cropVarieties', 'query'));
    }

    public function create()
    {
        $cropTypes = CropType::all();
        return view('admin.crop-varieties.create', compact('cropTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'crop_type_id' => 'required|exists:crop_types,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);

        CropVariety::create($request->all());

        return redirect()->route('admin.crop-varieties.index')->with('success', 'Varian tanaman berhasil ditambahkan!');
    }

    public function edit(CropVariety $cropVariety)
    {
        $cropTypes = CropType::all();
        return view('admin.crop-varieties.edit', compact('cropVariety', 'cropTypes'));
    }

    public function update(Request $request, CropVariety $cropVariety)
    {
        $request->validate([
            'crop_type_id' => 'required|exists:crop_types,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);

        $cropVariety->update($request->all());

        return redirect()->route('admin.crop-varieties.index')->with('success', 'Varian tanaman berhasil diperbarui!');
    }

    public function destroy(CropVariety $cropVariety)
    {
        $cropVariety->delete();
        return redirect()->route('admin.crop-varieties.index')->with('success', 'Varian tanaman berhasil dihapus!');
    }
}