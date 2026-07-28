<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\Unit;
use App\Models\Crop;
use App\Exports\HarvestsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class HarvestController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $harvests = Harvest::with(['crop', 'unit'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('crop', function ($c) use ($query) {
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
        // Semua tanaman bisa dipilih untuk dipanen, kecuali yang sudah manual ditandai "Dipanen" (siklus selesai)
        $crops = Crop::where('status', '!=', 'Dipanen')->get();

        return view('admin.harvests.create', compact('units', 'crops'));
    }

    public function store(Request $request)
    {
        // Bersihkan format rupiah sebelum validasi
        $request->merge([
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price),
        ]);

        $validated = $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvested_at'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit_id'       => 'required|exists:units,id',
            'selling_price' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Catat data panen
            $harvest = Harvest::create([
                'user_id'       => auth()->id(),
                'crop_id'       => $validated['crop_id'],
                'harvested_at'  => $validated['harvested_at'],
                'quantity'      => $validated['quantity'],
                'unit_id'       => $validated['unit_id'],
                'selling_price' => $validated['selling_price'],
                'notes'         => $validated['notes'] ?? null,
            ]);

            // 2. Ambil crop + jenis tanamannya (butuh relasi cropType di model Crop)
            $crop = Crop::with('cropType')->findOrFail($validated['crop_id']);
            $cropType = $crop->cropType;

            $updateData = [
                'actual_harvest_at' => $validated['harvested_at'],
            ];

            // 3. Logic otomatis: cek harvest_type dari master data jenis tanaman
            if ($cropType && $cropType->harvest_type === 'Sekali Panen') {
                // Sekali panen habis → langsung kunci status jadi "Dipanen"
                $updateData['status'] = 'Dipanen';
            }
            // Kalau 'Panen Berkelanjutan' → status TIDAK diubah otomatis,
            // tetap 'Pertumbuhan' sampai admin manual set "Dipanen" saat siklus benar-benar selesai.

            $crop->update($updateData);

            DB::commit();
            return redirect()->route('admin.harvests.index')->with('success', '🌾 Data panen berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Harvest $harvest)
    {
        $units = Unit::all();
        // Kalau edit, tampilkan semua crop termasuk yang udah dipanen (buat nampilin data lama)
        $crops = Crop::all();

        return view('admin.harvests.edit', compact('harvest', 'units', 'crops'));
    }

    public function update(Request $request, Harvest $harvest)
    {
        $request->merge([
            'selling_price' => preg_replace('/[^0-9]/', '', $request->selling_price),
        ]);

        $validated = $request->validate([
            'crop_id'       => 'required|exists:crops,id',
            'harvested_at'  => 'required|date',
            'quantity'      => 'required|numeric|min:0',
            'unit_id'       => 'required|exists:units,id',
            'selling_price' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $harvest->update($validated);

        return redirect()->route('admin.harvests.index')->with('success', 'Data panen berhasil diperbarui!');
    }

    public function destroy(Harvest $harvest)
    {
        $harvest->delete();
        return redirect()->route('admin.harvests.index')->with('success', 'Data panen berhasil dipindahkan ke sampah!');
    }

    public function exportExcel()
    {
        return Excel::download(new HarvestsExport, 'Data-Panen-' . date('Y-m-d') . '.xlsx');
    }
}