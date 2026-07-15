<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlantCare;
use App\Models\Unit;
class PlantCareController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $plantCares = PlantCare::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->latest()->get();
        return view('admin.plant-cares.index', compact('plantCares', 'query'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('admin.plant-cares.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Pupuk,Penyiraman,Pestisida,Pemangkasan,Lainnya',
            'unit_id'           => 'required|exists:units,id',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        PlantCare::create($request->all());

        return redirect()->route('admin.plant-cares.index')->with('success', 'Data perawatan berhasil ditambahkan!');
    }

    public function edit(PlantCare $plantCare)
    {
        $units = Unit::all();
        return view('admin.plant-cares.edit', compact('plantCare', 'units'));
    }

    public function update(Request $request, PlantCare $plantCare)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Pupuk,Penyiraman,Pestisida,Pemangkasan,Lainnya',
            'unit_id'           => 'required|exists:units,id',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        $plantCare->update($request->all());

        return redirect()->route('admin.plant-cares.index')->with('success', 'Data perawatan berhasil diperbarui!');
    }

    public function destroy(PlantCare $plantCare)
    {
        $plantCare->delete();
        return redirect()->route('admin.plant-cares.index')->with('success', 'Data perawatan berhasil dihapus!');
    }
}