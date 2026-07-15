<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlantCare;

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
        return view('admin.plant-cares.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Pupuk,Penyiraman,Pestisida,Pemangkasan,Lainnya',
            'unit'           => 'nullable|string|max:50',
            'stock'          => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        PlantCare::create($request->all());

        return redirect()->route('admin.plant-cares.index')->with('success', 'Data perawatan berhasil ditambahkan!');
    }

    public function edit(PlantCare $plantCare)
    {
        return view('admin.plant-cares.edit', compact('plantCare'));
    }

    public function update(Request $request, PlantCare $plantCare)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Pupuk,Penyiraman,Pestisida,Pemangkasan,Lainnya',
            'unit'           => 'nullable|string|max:50',
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