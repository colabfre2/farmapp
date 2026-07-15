<?php

namespace App\Http\Controllers;

use App\Models\LivestockType;
use Illuminate\Http\Request;

class LivestockTypeController extends Controller
{

        public function index(Request $request)
        {
             $query = $request->input('q');
    $livestockTypes = \App\Models\LivestockType::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    return view('admin.livestock-types.index', compact('livestockTypes', 'query'));
    
        }

        public function create()
        {
            return view('admin.livestock-types.create');
        }

        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            LivestockType::create($request->all());

            return redirect()->route('admin.livestock-types.index')->with('success', 'Livestock Type created successfully!');
        }

        public function edit(LivestockType $livestockType)
        {
            return view('admin.livestock-types.edit', compact('livestockType'));
        }

        public function update(Request $request, LivestockType $livestockType)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $livestockType->update($request->all());

            return redirect()->route('admin.livestock-types.index')->with('success', 'Livestock Type updated successfully!');
        }

        public function destroy(LivestockType $livestockType)
        {
            $livestockType->delete();

            return redirect()->route('admin.livestock-types.index')->with('success', 'Livestock Type deleted successfully!');
        }
}
