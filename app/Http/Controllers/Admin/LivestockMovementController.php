<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LivestockMovement;
use App\Models\Livestock;
use Illuminate\Support\Facades\DB;

class LivestockMovementController extends Controller
{
    // ── 1. TERNAK MASUK (IN) ─────────────────────────────────

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
            'reason'       => 'required|string|max:255',
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

        DB::transaction(function () use ($request, $livestock) {
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
        });

        return redirect()->route('admin.livestock-movements.in.index')
            ->with('success', 'Ternak masuk berhasil dicatat!');
    }

    public function inStoreBulk(Request $request)
    {
        $request->validate([
            'movements'                 => 'required|array|min:1',
            'movements.*.livestock_id'  => 'required|exists:livestocks,id',
            'movements.*.quantity'      => 'required|integer|min:1',
            'movements.*.date'          => 'required|date',
            'movements.*.reason'        => 'required|string|max:255',
            'movements.*.notes'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->movements as $data) {
                $livestock = Livestock::with('kandang')->findOrFail($data['livestock_id']);

                if ($livestock->kandang && $livestock->kandang->capacity !== null) {
                    $totalTerisiKandang = Livestock::where('kandang_id', $livestock->kandang_id)->sum('quantity');
                    $sisa = $livestock->kandang->capacity - $totalTerisiKandang;

                    if ($data['quantity'] > $sisa) {
                        throw new \Exception("Kapasitas kandang untuk kelompok '{$livestock->name}' tidak mencukupi! Sisa ruang: {$sisa} ekor.");
                    }
                }

                LivestockMovement::create([
                    'livestock_id' => $data['livestock_id'],
                    'kandang_id'   => $livestock->kandang_id,
                    'user_id'      => auth()->id(),
                    'type'         => 'in',
                    'quantity'     => $data['quantity'],
                    'date'         => $data['date'],
                    'reason'       => $data['reason'],
                    'notes'        => $data['notes'] ?? null,
                ]);

                $livestock->increment('quantity', $data['quantity']);
            }

            DB::commit();
            return redirect()->route('admin.livestock-movements.in.index')
                ->with('success', 'Mutasi ternak masuk borongan berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ── 2. TERNAK KELUAR (OUT) ───────────────────────────────

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
            'reason'       => 'required|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $livestock = Livestock::findOrFail($request->livestock_id);

        if ($livestock->quantity < $request->quantity) {
            return back()->withErrors([
                'quantity' => "Jumlah ternak tidak cukup! Populasi saat ini: {$livestock->quantity} ekor.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $livestock) {
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
        });

        return redirect()->route('admin.livestock-movements.out.index')
            ->with('success', 'Ternak keluar berhasil dicatat!');
    }

    public function outStoreBulk(Request $request)
    {
        $request->validate([
            'movements'                 => 'required|array|min:1',
            'movements.*.livestock_id'  => 'required|exists:livestocks,id',
            'movements.*.quantity'      => 'required|integer|min:1',
            'movements.*.date'          => 'required|date',
            'movements.*.reason'        => 'required|string|max:255',
            'movements.*.notes'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->movements as $data) {
                $livestock = Livestock::findOrFail($data['livestock_id']);

                if ($livestock->quantity < $data['quantity']) {
                    throw new \Exception("Stok kelompok ternak '{$livestock->name}' tidak cukup! Populasi saat ini: {$livestock->quantity} ekor.");
                }

                LivestockMovement::create([
                    'livestock_id' => $data['livestock_id'],
                    'kandang_id'   => $livestock->kandang_id,
                    'user_id'      => auth()->id(),
                    'type'         => 'out',
                    'quantity'     => $data['quantity'],
                    'date'         => $data['date'],
                    'reason'       => $data['reason'],
                    'notes'        => $data['notes'] ?? null,
                ]);

                $livestock->decrement('quantity', $data['quantity']);
            }

            DB::commit();
            return redirect()->route('admin.livestock-movements.out.index')
                ->with('success', 'Mutasi keluar / penjualan offline borongan berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}