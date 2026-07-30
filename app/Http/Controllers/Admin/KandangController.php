<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kandang;
use App\Models\LivestockType;

class KandangController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $kandangs = Kandang::with('livestockType')
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->withCount('livestocks')
            ->latest()
            ->get();
        return view('admin.kandangs.index', compact('kandangs', 'query'));
    }

    public function create()
    {
        $livestockTypes = LivestockType::all();
        return view('admin.kandangs.create', compact('livestockTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name'        => 'required|string|max:255',
            'capacity'    => 'nullable|integer|min:0',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Kandang::create($request->all());

        return redirect()->route('admin.kandangs.index')->with('success', 'Kandang berhasil ditambahkan!');
    }

    public function show(Kandang $kandang)
    {
        $kandang->load('livestockType', 'livestocks.livestockType');
        return view('admin.kandangs.show', compact('kandang'));
    }

    public function edit(Kandang $kandang)
    {
        $livestockTypes = LivestockType::all();
        return view('admin.kandangs.edit', compact('kandang', 'livestockTypes'));
    }

    public function update(Request $request, Kandang $kandang)
    {
        $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name'        => 'required|string|max:255',
            'capacity'    => 'nullable|integer|min:0',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $kandang->update($request->all());

        return redirect()->route('admin.kandangs.index')->with('success', 'Kandang berhasil diperbarui!');
    }

    public function destroy(Kandang $kandang)
    {
        if ($kandang->livestocks()->exists()) {
            return back()->with('error', 'Kandang tidak bisa dihapus karena masih ada ternak yang menempatinya. Pindahkan ternak ke kandang lain terlebih dahulu.');
        }

        $kandang->delete();
        return redirect()->route('admin.kandangs.index')->with('success', 'Kandang berhasil dihapus!');
    }
}