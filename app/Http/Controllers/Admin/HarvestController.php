<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\Unit;

class HarvestController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
    $harvests = Harvest::when($query, fn($q) => $q->where('product_name', 'like', "%{$query}%"))->latest()->get();
    return view('admin.harvests.index', compact('harvests', 'query'));
        
    }

    public function create()
    {
        $units = Unit::all(); 
        return view('admin.harvests.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->merge([
        'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
    ]);
        $request->validate([
            'product_name'  => 'required|string|max:255',
            'harvested_at'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit_id'       => 'required|exists:units,id', // UBAH MENJADI unit_id
            'selling_price' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        Harvest::create([
            'user_id'       => auth()->id(),
            'product_name'  => $request->product_name,
            'harvested_at'  => $request->harvested_at,
            'quantity'      => $request->quantity,
            'unit_id'       => $request->unit_id, // UBAH MENJADI unit_id
            'selling_price' => $request->selling_price,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('admin.harvests.index')->with('success', 'Harvest recorded successfully!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Harvest $harvest)
    {
    // Ambil data units dari database
    $units = Unit::all(); 
    
    // Kirim $harvest dan $units ke halaman edit
    return view('admin.harvests.edit', compact('harvest', 'units'));
    }

    public function update(Request $request, Harvest $harvest)
    {
        $request->merge([
        'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price)
    ]);
        $request->validate([
            'product_name' => 'required|string|max:255',
            'harvested_at' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|string|max:20',
            'selling_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
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