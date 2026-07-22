<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LivestockMovement;
use App\Models\Livestock;

class LivestockMovementController extends Controller
{
    // ── Ternak Masuk ─────────────────────────────────────────

    public function inIndex(Request $request)
    {
        $query = $request->input('q');
        $movements = LivestockMovement::with('livestock', 'user')
            ->where('type', 'in')
            ->when($query, fn($q) => $q->whereHas('livestock', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.livestock-movements.in.index', compact('movements', 'query'));
    }

    public function inCreate()
    {
        $livestocks = Livestock::all();
        return view('admin.livestock-movements.in.create', compact('livestocks'));
    }

    public function inStore(Request $request)
    {
        $request->validate([
            'livestock_id' => 'required|exists:livestocks,id',
            'quantity'     => 'required|integer|min:1',
            'date'         => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $livestock = Livestock::findOrFail($request->livestock_id);

        LivestockMovement::create([
            'livestock_id' => $request->livestock_id,
            'user_id'      => auth()->id(),
            'type'         => 'in',
            'quantity'     => $request->quantity,
            'date'         => $request->date,
            'reason'       => $request->reason,
            'notes'        => $request->notes,
        ]);

        $livestock->increment('quantity', $request->quantity);

        return redirect()->route('admin.livestock-movements.in.index')->with('success', 'Ternak masuk berhasil dicatat!');
    }

    // ── Ternak Keluar ─────────────────────────────────────────

    public function outIndex(Request $request)
    {
        $query = $request->input('q');
        $movements = LivestockMovement::with('livestock', 'user')
            ->where('type', 'out')
            ->when($query, fn($q) => $q->whereHas('livestock', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.livestock-movements.out.index', compact('movements', 'query'));
    }

    public function outCreate()
    {
        $livestocks = Livestock::all();
        return view('admin.livestock-movements.out.create', compact('livestocks'));
    }

    public function outStore(Request $request)
    {
        $request->validate([
            'livestock_id' => 'required|exists:livestocks,id',
            'quantity'     => 'required|integer|min:1',
            'date'         => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $livestock = Livestock::findOrFail($request->livestock_id);

        if ($livestock->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Jumlah ternak tidak cukup! Jumlah saat ini: ' . $livestock->quantity])->withInput();
        }

        LivestockMovement::create([
            'livestock_id' => $request->livestock_id,
            'user_id'      => auth()->id(),
            'type'         => 'out',
            'quantity'     => $request->quantity,
            'date'         => $request->date,
            'reason'       => $request->reason,
            'notes'        => $request->notes,
        ]);

        $livestock->decrement('quantity', $request->quantity);

        return redirect()->route('admin.livestock-movements.out.index')->with('success', 'Ternak keluar berhasil dicatat!');
    }
}