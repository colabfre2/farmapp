<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\Kandang;
use App\Http\Requests\LivestockStoreBulkRequest;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $livestocks = Livestock::with('livestockType', 'kandang')
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
        // Sertakan current_occupancy agar view bisa tampilkan info kapasitas real-time
        $kandangs = Kandang::withSum('livestocks', 'quantity')->get();
        return view('admin.livestock.create', compact('livestockTypes', 'kandangs'));
    }

    // ── Helper: ambil kandang + validasi kapasitas ────────────────────────────

    private function getKandangAndCheckCapacity(int $kandangId, int $quantity, ?int $excludeLivestockId = null): Kandang
    {
        $kandang = Kandang::withSum('livestocks', 'quantity')->findOrFail($kandangId);

        if ($kandang->capacity !== null) {
            // Kalau update (pindah kandang), stok livestock yang sedang diedit
            // tidak ikut dihitung sebagai "terisi" kandang baru
            $terisi = $kandang->livestocks_sum_quantity ?? 0;
            $sisa = $kandang->capacity - $terisi;

            if ($quantity > $sisa) {
                throw ValidationException::withMessages([
                    'quantity' => "Kapasitas kandang \"{$kandang->name}\" tidak mencukupi! Sisa ruang: {$sisa} ekor.",
                ]);
            }
        }

        return $kandang;
    }

    // ── Store Single ──────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            // kandang_id WAJIB — livestock_type_id akan di-derive dari kandang,
            // bukan dipercaya dari input mentah (anti-tamper via inspect element).
            'kandang_id'    => 'required|exists:kandangs,id',
            'name'          => 'required|string|max:255',
            'arrival_date'  => 'required|date',
            'quantity'      => 'required|integer|min:1',
            'avg_weight'    => 'nullable|numeric',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'         => 'nullable|string',
        ]);

        try {
            $kandang = $this->getKandangAndCheckCapacity(
                $validated['kandang_id'],
                $validated['quantity']
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('error', 'Kapasitas kandang tidak mencukupi!');
        }

        // Derive livestock_type_id dari kandang — bukan dari dropdown jenis hewan
        $validated['livestock_type_id'] = $kandang->livestock_type_id;
        $validated['user_id'] = auth()->id();

        DB::beginTransaction();
        try {
            $livestock = Livestock::create($validated);

            if ($validated['quantity'] > 0) {
                \App\Models\LivestockMovement::create([
                    'livestock_id' => $livestock->id,
                    'user_id'      => auth()->id(),
                    'type'         => 'in',
                    'quantity'     => $validated['quantity'],
                    'date'         => $validated['arrival_date'],
                    'reason'       => 'Data Awal',
                    'notes'        => 'Otomatis tercatat saat data awal dibuat',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.livestock.index')
                ->with('success', 'Data ternak berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    // ── Store Bulk ────────────────────────────────────────────────────────────

    public function storeBulk(LivestockStoreBulkRequest $request)
    {
        $rows = $request->validated()['livestocks'];

        DB::beginTransaction();
        try {
            // Tracking kapasitas kandang di memori — mencegah 2 baris bulk yang masing-masing
            // valid tapi gabungannya melebihi kapasitas kandang yang sama.
            $kandangTracking = [];

            foreach ($rows as $index => $data) {
                $kandangId = $data['kandang_id'];

                if (!isset($kandangTracking[$kandangId])) {
                    $kandang = Kandang::withSum('livestocks', 'quantity')->findOrFail($kandangId);
                    $kandangTracking[$kandangId] = [
                        'capacity'           => $kandang->capacity,
                        'terisi'             => $kandang->livestocks_sum_quantity ?? 0,
                        'name'               => $kandang->name,
                        'livestock_type_id'  => $kandang->livestock_type_id,
                    ];
                }

                if ($kandangTracking[$kandangId]['capacity'] !== null) {
                    $sisa = $kandangTracking[$kandangId]['capacity'] - $kandangTracking[$kandangId]['terisi'];
                    $qty  = (int) $data['quantity'];

                    if ($qty > $sisa) {
                        throw ValidationException::withMessages([
                            "livestocks.{$index}.quantity" =>
                                "Kapasitas \"{$kandangTracking[$kandangId]['name']}\" tidak cukup! Sisa: {$sisa} ekor.",
                        ]);
                    }

                    $kandangTracking[$kandangId]['terisi'] += $qty;
                }

                // Derive livestock_type_id dari kandang, bukan dari input
                $livestock = Livestock::create([
                    'user_id'           => auth()->id(),
                    'livestock_type_id' => $kandangTracking[$kandangId]['livestock_type_id'],
                    'kandang_id'        => $kandangId,
                    'name'              => $data['name'],
                    'arrival_date'      => $data['arrival_date'],
                    'avg_weight'        => $data['avg_weight'] ?? null,
                    'quantity'          => $data['quantity'],
                    'health_status'     => $data['health_status'],
                    'notes'             => $data['notes'] ?? null,
                ]);

                if ($data['quantity'] > 0) {
                    \App\Models\LivestockMovement::create([
                        'livestock_id' => $livestock->id,
                        'user_id'      => auth()->id(),
                        'type'         => 'in',
                        'quantity'     => $data['quantity'],
                        'date'         => $data['arrival_date'],
                        'reason'       => 'Data Awal',
                        'notes'        => 'Otomatis tercatat dari input bulk',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.livestock.index')
                ->with('success', count($rows) . ' data ternak berhasil ditambahkan!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput()
                ->with('error', 'Gagal menyimpan! Periksa kembali input kapasitas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Livestock $livestock)
    {
        $livestock->load('livestockType', 'kandang', 'movements', 'feedLogs.feed.unit', 'medicineLogs.medicine.unit', 'user');
        return view('admin.livestock.show', compact('livestock'));
    }

    // ── Edit & Update ─────────────────────────────────────────────────────────

    public function edit(Livestock $livestock)
    {
        $livestockTypes = LivestockType::all();
        $kandangs = Kandang::withSum('livestocks', 'quantity')->get();
        return view('admin.livestock.edit', compact('livestock', 'livestockTypes', 'kandangs'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        $validated = $request->validate([
            'kandang_id'    => 'required|exists:kandangs,id',
            'name'          => 'required|string|max:255',
            'arrival_date'  => 'required|date',
            'avg_weight'    => 'nullable|numeric',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'         => 'nullable|string',
            // quantity TIDAK divalidasi/diupdate — hanya lewat LivestockMovement
        ]);

        // Cek kapasitas hanya kalau kandang berubah
        if ((int) $validated['kandang_id'] !== (int) $livestock->kandang_id) {
            try {
                $kandang = $this->getKandangAndCheckCapacity(
                    $validated['kandang_id'],
                    $livestock->quantity // stok yang akan pindah ke kandang baru
                );
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            }
            $validated['livestock_type_id'] = $kandang->livestock_type_id;
        } else {
            // Kandang tidak berubah — derive livestock_type_id dari kandang yang sama
            $validated['livestock_type_id'] = $livestock->kandang->livestock_type_id;
        }

        $livestock->update($validated);

        return redirect()->route('admin.livestock.index')
            ->with('success', 'Data ternak berhasil diperbarui!');
    }

    // ── Destroy, Trash, Restore, ForceDelete ─────────────────────────────────

    public function destroy(Livestock $livestock)
    {
        $livestock->delete();
        return redirect()->route('admin.livestock.index')
            ->with('success', 'Data dipindahkan ke tempat sampah!');
    }

    public function trash()
    {
        // FIX: tambah 'kandang' agar $livestock->kandang->name tidak N+1 / null di view trash
        $livestocks = Livestock::onlyTrashed()->with('livestockType', 'kandang')->latest()->get();
        return view('admin.livestock.trash', compact('livestocks'));
    }

    public function restore($id)
    {
        Livestock::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.livestock.trash')
            ->with('success', 'Data ternak berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        Livestock::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.livestock.trash')
            ->with('success', 'Data ternak dihapus permanen!');
    }
}