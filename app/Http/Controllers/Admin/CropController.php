<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropType;
use App\Models\CropVariety;
use App\Models\Farm;
use Illuminate\Support\Facades\DB;

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

    /**
     * Helper: rakit nama otomatis untuk satu baris data tanaman.
     */
    private function generateCropName(array $row): string
    {
        if (!empty($row['name'])) {
            return $row['name'];
        }

        $type = CropType::find($row['crop_type_id']);
        $variety = !empty($row['crop_variety_id']) ? CropVariety::find($row['crop_variety_id']) : null;
        $farm = !empty($row['farm_id']) ? Farm::find($row['farm_id']) : null;

        $typeName = $type->name ?? 'Tanaman';
        $varietyName = $variety ? $variety->name : '';

        $baseName = $varietyName ? $varietyName : $typeName;
        $farmStr = $farm ? ' - ' . $farm->name : ' - Lahan Utama';

        return $baseName . $farmStr;
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
            // Saat pertama kali dibuat, tanaman TIDAK BOLEH langsung berstatus "Dipanen".
            // Status "Dipanen" hanya boleh terjadi lewat pencatatan panen (otomatis untuk
            // jenis "Sekali Panen") atau diubah manual lewat halaman Edit.
            'status' => 'required|in:Bibit,Pertumbuhan',
            'notes' => 'nullable|string',
        ]);

        $validated['name'] = $this->generateCropName($validated);
        $validated['user_id'] = auth()->id();

        Crop::create($validated);

        return redirect()->route('admin.crops.index')->with('success', 'Tanaman berhasil ditambahkan!');
    }

    /**
     * Simpan banyak tanaman sekaligus dari form dinamis (multi-baris).
     * Tiap baris divalidasi & disimpan independen: baris yang valid tetap
     * tersimpan walau ada baris lain yang gagal.
     */
    public function storeBulk(Request $request)
    {
        $rows = $request->input('crops', []);

        if (empty($rows)) {
            return back()->with('error', 'Tidak ada data tanaman yang dikirim.')->withInput();
        }

        $rules = [
            'crop_type_id' => 'required|exists:crop_types,id',
            'crop_variety_id' => 'nullable|exists:crop_varieties,id',
            'farm_id' => 'nullable|exists:farms,id',
            'name' => 'nullable|string|max:255',
            'planted_at' => 'required|date',
            'expected_harvest_at' => 'required|date',
            'status' => 'required|in:Bibit,Pertumbuhan',
            'notes' => 'nullable|string',
        ];

        $successCount = 0;
        $failedRows = []; // index baris (1-based, buat ditampilkan ke user) => pesan error
        $failedInput = []; // simpan input asli baris yang gagal, biar bisa ditampilkan lagi di form

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;

            $validator = \Illuminate\Support\Facades\Validator::make($row, $rules);

            if ($validator->fails()) {
                $failedRows[$rowNumber] = $validator->errors()->all();
                $failedInput[$index] = $row;
                continue;
            }

            $data = $validator->validated();

            try {
                DB::beginTransaction();

                $data['name'] = $this->generateCropName($data);
                $data['user_id'] = auth()->id();

                Crop::create($data);

                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failedRows[$rowNumber] = ['Terjadi kesalahan sistem: ' . $e->getMessage()];
                $failedInput[$index] = $row;
            }
        }

        // Semua baris berhasil
        if (empty($failedRows)) {
            return redirect()->route('admin.crops.index')
                ->with('success', "🌱 {$successCount} tanaman berhasil ditambahkan sekaligus!");
        }

        // Sebagian berhasil, sebagian gagal
        $failedCount = count($failedRows);
        $message = $successCount > 0
            ? "{$successCount} tanaman berhasil disimpan, tapi {$failedCount} baris gagal. Perbaiki baris yang ditandai merah di bawah."
            : "Semua baris gagal disimpan. Periksa kembali data yang ditandai merah di bawah.";

        return back()
            ->with($successCount > 0 ? 'warning' : 'error', $message)
            ->with('failedRows', $failedRows)
            ->withInput(['crops' => array_values($failedInput)]);
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
        // Saat edit, "Dipanen" tetap boleh dipilih (admin menyelesaikan siklus manual).
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

        $validated['name'] = $this->generateCropName($validated);

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