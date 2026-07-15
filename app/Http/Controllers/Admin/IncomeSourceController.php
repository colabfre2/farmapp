<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomeSource;

class IncomeSourceController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $incomeSources = IncomeSource::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
        return view('admin.income-sources.index', compact('incomeSources', 'query'));
    }

    public function create()
    {
        return view('admin.income-sources.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        IncomeSource::create($request->all());

        return redirect()->route('admin.income-sources.index')->with('success', 'Sumber pemasukan berhasil ditambahkan!');
    }

    public function edit(IncomeSource $incomeSource)
    {
        return view('admin.income-sources.edit', compact('incomeSource'));
    }

    public function update(Request $request, IncomeSource $incomeSource)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $incomeSource->update($request->all());

        return redirect()->route('admin.income-sources.index')->with('success', 'Sumber pemasukan berhasil diperbarui!');
    }

    public function destroy(IncomeSource $incomeSource)
    {
        $incomeSource->delete();
        return redirect()->route('admin.income-sources.index')->with('success', 'Sumber pemasukan berhasil dihapus!');
    }
}