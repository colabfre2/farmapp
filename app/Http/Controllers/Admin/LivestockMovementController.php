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
            ->whereIn('type', ['in', 'transfer'])
            ->when($query, fn($q) => $q->whereHas('livestock', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.livestock-movements.in.index', compact('movements', 'query'));
    }

    public function inCreate()
    {
        $livestocks = Livestock::with('kandang')->latest()->get();
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

        $livestock = Livestock::with('kandang')->findOrFail($request->livestock_id);

        if ($livestock->kandang && $livestock->kandang->capacity !== null) {
            $totalTerisiKandang = Livestock::where('kandang_id', $livestock->kandang_id)->sum('quantity');
            $sisa = $livestock->kandang->capacity - $totalTerisiKandang;

            if ($request->quantity > $sisa) {
                return back()->withErrors([
                    'quantity' => "Kapasitas kandang \"{$livestock->kandang->name}\" tidak mencukupi! Sisa ruang: {$sisa} ekor.",
                ])->withInput();
            }
        }

        LivestockMovement::create([
            'livestock_id' => $request->livestock_id,
            'kandang_id'   => $livestock->kandang_id,
            'user_id'      => auth()->id(),
            'type'         => 'in',
            'quantity'     => $request->quantity,
            'date'         => $request->date,
            'reason'       => $request->reason,
            'notes'        => $request->notes,
        ]);

        $livestock->increment('quantity', $request->quantity);

        return redirect()->route('admin.livestock-movements.in.index')
            ->with('success', 'Ternak masuk berhasil dicatat!');
    }

    // ── Ternak Keluar ─────────────────────────────────────────

    public function outIndex(Request $request)
    {
        $query = $request->input('q');
        $movements = LivestockMovement::with('livestock', 'user')
            ->whereIn('type', ['out', 'transfer'])
            ->when($query, fn($q) => $q->whereHas('livestock', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.livestock-movements.out.index', compact('movements', 'query'));
    }

    public function outCreate()
    {
        $livestocks = Livestock::with('kandang')->latest()->get();
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
            return back()->withErrors([
                'quantity' => "Jumlah ternak tidak cukup! Populasi saat ini: {$livestock->quantity} ekor.",
            ])->withInput();
        }

        LivestockMovement::create([
            'livestock_id' => $request->livestock_id,
            'kandang_id'   => $livestock->kandang_id,
            'user_id'      => auth()->id(),
            'type'         => 'out',
            'quantity'     => $request->quantity,
            'date'         => $request->date,
            'reason'       => $request->reason,
            'notes'        => $request->notes,
        ]);

        $livestock->decrement('quantity', $request->quantity);

        return redirect()->route('admin.livestock-movements.out.index')
            ->with('success', 'Ternak keluar berhasil dicatat!');
    }
}