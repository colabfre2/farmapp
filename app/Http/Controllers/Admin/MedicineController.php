<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Unit;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $medicines = Medicine::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->latest()->get();
        return view('admin.medicines.index', compact('medicines', 'query'));
    }

    public function create()
    {
        $units = Unit::all(); 
        return view('admin.medicines.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'unit_id'           => 'required|exists:units,id',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        Medicine::create($request->all());

        return redirect()->route('admin.medicines.index')->with('success', 'Obat berhasil ditambahkan!');
    }

    public function edit(Medicine $medicine)
    {
        $units = Unit::all(); 
        

        return view('admin.medicines.edit', compact('medicine', 'units'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'unit_id'           => 'required|exists:units,id',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        $medicine->update($request->all());

        return redirect()->route('admin.medicines.index')->with('success', 'Obat berhasil diperbarui!');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('admin.medicines.index')->with('success', 'Obat berhasil dihapus!');
    }
}