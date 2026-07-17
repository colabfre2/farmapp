<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\Unit;
use App\Models\Crop;

class HarvestController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        // Menggunakan relasi 'crop' untuk mencari berdasarkan nama tanaman
        $harvests = Harvest::with(['crop', 'unit'])
            ->when($query, function($q) use ($query) {
                $q->whereHas('crop', function($c) use ($query) {
                    $c->where('name', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->get();

        return view('admin.harvests.index', compact('harvests', 'query'));
    }

    public function create()
    {
        $units = Unit::all();
        $crops = Crop::all(); // Tambahkan ini agar dropdown tanaman muncul
        return view('admin.harvests.create', compact('units', 'crops'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
        ]);

        $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvested_at'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit_id'       => 'required|exists:units,id',
            'selling_price' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        Harvest::create([
            'user_id'       => auth()->id(),
            'crop_id'       => $request->crop_id,
            'harvested_at'  => $request->harvested_at,
            'quantity'      => $request->quantity,
            'unit_id'       => $request->unit_id,
            'selling_price' => $request->selling_price,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('admin.harvests.index')->with('success', 'Harvest recorded successfully!');
    }

    public function edit(Harvest $harvest)
    {
        $units = Unit::all();
        $crops = Crop::all(); // Tambahkan ini
        return view('admin.harvests.edit', compact('harvest', 'units', 'crops'));
    }

    public function update(Request $request, Harvest $harvest)
    {
        $request->merge([
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
        ]);

        $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvested_at'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit_id'       => 'required|exists:units,id', // Di sini diperbaiki dari string menjadi exists
            'selling_price' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $harvest->update($request->all());

        return redirect()->route('admin.harvests.index')->with('success', 'Harvest updated successfully!');
    }

    public function destroy(Harvest $harvest)
    {
        $harvest->delete();
        return redirect()->route('admin.harvests.index')->with('success', 'Harvest deleted successfully!');
    }
}