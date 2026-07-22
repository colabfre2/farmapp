<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Livestock;
use App\Models\LivestockType;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $livestocks = Livestock::with('livestockType')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->get();

        return view('admin.livestock.index', compact('livestocks', 'query'));
    }

    public function create()
    {
        $livestockTypes = LivestockType::all();
        return view('admin.livestock.create', compact('livestockTypes'));
    }

    public function store(Request $request)
{
    // SAAT CREATE: Quantity (stok awal) dan Arrival Date (tanggal masuk) wajib diisi
    $validated = $request->validate([
        'livestock_type_id' => 'required|exists:livestock_types,id',
        'name' => 'required|string|max:255',
        'arrival_date' => 'required|date', // Tangkap tanggal masuk
        'quantity' => 'required|integer|min:0', // Stok awal
        'avg_weight' => 'nullable|numeric', // Ganti ke numeric biar gak asal ketik huruf
        'health_status' => 'required|in:Sehat,Pemantauan,Sakit', // Sesuaikan enum DB
        'notes' => 'nullable|string',
    ]);

    $validated['user_id'] = auth()->id();

    $livestock = Livestock::create($validated);

    // Catat otomatis ke Ternak Masuk kalau quantity awal > 0
    if ($validated['quantity'] > 0) {
        \App\Models\LivestockMovement::create([
            'livestock_id' => $livestock->id,
            'user_id'      => auth()->id(),
            'type'         => 'in',
            'quantity'     => $validated['quantity'],
            'date'         => $validated['arrival_date'], // pakai tanggal masuk asli, bukan hari ini
            'reason'       => 'Data Awal',
            'notes'        => 'Otomatis tercatat saat kandang dibuat',
        ]);
    }

    return redirect()->route('admin.livestock.index')->with('success', 'Kandang ternak berhasil ditambahkan!');
}

    public function edit(Livestock $livestock)
    {
        $livestockTypes = LivestockType::all();
        return view('admin.livestock.edit', compact('livestock', 'livestockTypes'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        // SAAT UPDATE: HAPUS validasi 'quantity' biar gak ada celah buat diedit!
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name' => 'required|string|max:255',
            'arrival_date' => 'required|date', // Tangkap tanggal masuk
            'avg_weight' => 'nullable|numeric',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes' => 'nullable|string',
        ]);

        // PENTING: Gunakan $validated, JANGAN gunakan $request->all() 
        // Biar kalau ada yang maksa ngirim field 'quantity' dari inspect element, bakal diabaikan sama Laravel.
        $livestock->update($validated);

        return redirect()->route('admin.livestock.index')->with('success', 'Data kandang berhasil diperbarui!');
    }

    public function destroy(Livestock $livestock)
    {
        $livestock->delete();
        return redirect()->route('admin.livestock.index')->with('success', 'Data dipindahkan ke tempat sampah!');
    }

    public function trash()
    {
        $livestocks = Livestock::onlyTrashed()->with('livestockType')->latest()->get();
        return view('admin.livestock.trash', compact('livestocks'));
    }

    public function restore($id)
    {
        $livestock = Livestock::onlyTrashed()->findOrFail($id);
        $livestock->restore();

        return redirect()->route('admin.livestock.trash')->with('success', 'Data ternak berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        $livestock = Livestock::onlyTrashed()->findOrFail($id);
        $livestock->forceDelete();

        return redirect()->route('admin.livestock.trash')->with('success', 'Data ternak dihapus permanen!');
    }
}