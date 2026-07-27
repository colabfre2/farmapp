<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropType;
use App\Models\CropVariety;
use App\Models\Farm;

class CropController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $status = $request->input('status');

        $crops = Crop::with('cropType', 'cropVariety', 'farm')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.crops.index', compact('crops', 'query', 'status'));
    }

    public function create()
    {
        $cropTypes = CropType::all();
        $cropVarieties = CropVariety::all();
        $farms = Farm::all();
        return view('admin.crops.create', compact('cropTypes', 'cropVarieties', 'farms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_type_id' => 'required|exists:crop_types,id',
            'crop_variety_id' => 'nullable|exists:crop_varieties,id',
            'farm_id' => 'nullable|exists:farms,id',
            'name' => 'nullable|string|max:255', // nullable buat fitur auto-generate
            'planted_at' => 'required|date',
            'expected_harvest_at' => 'required|date',
            'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
            'notes' => 'nullable|string',
        ]);

        // 🔥 LOGIC AUTO-GENERATE NAMA TANAMAN
        if (empty($validated['name'])) {
            $type = CropType::find($validated['crop_type_id']);
            $variety = $validated['crop_variety_id'] ? CropVariety::find($validated['crop_variety_id']) : null;
            $farm = $validated['farm_id'] ? Farm::find($validated['farm_id']) : null;

            $typeName = $type->name ?? 'Tanaman';
            $varietyName = $variety ? $variety->name : '';

            $baseName = $varietyName ? $varietyName : $typeName;
            $farmStr = $farm ? ' - ' . $farm->name : ' - Lahan Utama';

            $validated['name'] = $baseName . $farmStr;
        }

        $validated['user_id'] = auth()->id();

        Crop::create($validated);

        return redirect()->route('admin.crops.index')->with('success', 'Tanaman berhasil ditambahkan!');
    }

    public function show(Crop $crop)
    {
        $crop->load('cropType', 'cropVariety', 'farm', 'harvests', 'plantCareLogs.plantCare', 'user');
        return view('admin.crops.show', compact('crop'));
    }

    public function edit(Crop $crop)
    {
        $cropTypes = CropType::all();
        $cropVarieties = CropVariety::all();
        $farms = Farm::all();
        return view('admin.crops.edit', compact('crop', 'cropTypes', 'cropVarieties', 'farms'));
    }

    public function update(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'crop_type_id' => 'required|exists:crop_types,id',
            'crop_variety_id' => 'nullable|exists:crop_varieties,id',
            'farm_id' => 'nullable|exists:farms,id',
            'name' => 'nullable|string|max:255',
            'planted_at' => 'required|date',
            'expected_harvest_at' => 'required|date',
            'actual_harvest_at' => 'nullable|date',
            'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['name'])) {
            $type = CropType::find($validated['crop_type_id']);
            $variety = $validated['crop_variety_id'] ? CropVariety::find($validated['crop_variety_id']) : null;
            $farm = $validated['farm_id'] ? Farm::find($validated['farm_id']) : null;

            $typeName = $type->name ?? 'Tanaman';
            $varietyName = $variety ? $variety->name : '';

            $baseName = $varietyName ? $varietyName : $typeName;
            $farmStr = $farm ? ' - ' . $farm->name : ' - Lahan Utama';

            $validated['name'] = $baseName . $farmStr;
        }

        $crop->update($validated);

        return redirect()->route('admin.crops.index')->with('success', 'Tanaman berhasil diperbarui!');
    }

    public function destroy(Crop $crop)
    {
        $crop->delete();
        return redirect()->route('admin.crops.index')->with('success', 'Tanaman dipindahkan ke sampah!');
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
        return redirect()->route('admin.crops.trash')->with('success', 'Tanaman berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        $crop = Crop::onlyTrashed()->findOrFail($id);
        $crop->forceDelete();
        return redirect()->route('admin.crops.trash')->with('success', 'Tanaman dihapus permanen!');
    }
}