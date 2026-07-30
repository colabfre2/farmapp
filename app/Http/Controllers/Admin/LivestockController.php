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

        // Hanya tampilkan kandang yang KOSONG (total quantity semua livestock di kandang = 0).
        // Kandang yang masih terisi harus ditambah lewat menu Ternak Masuk, bukan form ini.
        $semuaKandang = Kandang::withSum('livestocks', 'quantity')->get();
        $kandangs = $semuaKandang->filter(fn($k) => ($k->livestocks_sum_quantity ?? 0) == 0)->values();
        $kandangsTerisi = $semuaKandang->count() - $kandangs->count();

        return view('admin.livestock.create', compact('livestockTypes', 'kandangs', 'kandangsTerisi'));
    }

    // ── Helper: pastikan kandang benar-benar kosong (khusus form Tambah Ternak Baru) ──

    private function getKandangAndCheckEmpty(int $kandangId): Kandang
    {
        $kandang = Kandang::withSum('livestocks', 'quantity')->findOrFail($kandangId);
        $terisi = $kandang->livestocks_sum_quantity ?? 0;

        if ($terisi > 0) {
            throw ValidationException::withMessages([
                'kandang_id' => "Kandang \"{$kandang->name}\" sudah terisi ({$terisi} ekor). "
                    . "Gunakan menu Ternak Masuk untuk menambah populasi ke kandang yang sudah ada.",
            ]);
        }

        return $kandang;
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
            // Form ini ("Tambah Ternak Baru") cuma untuk kandang kosong.
            // Kandang yang sudah terisi harus lewat menu Ternak Masuk.
            $this->getKandangAndCheckEmpty($validated['kandang_id']);

            $kandang = $this->getKandangAndCheckCapacity(
                $validated['kandang_id'],
                $validated['quantity']
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()
                ->with('error', 'Kandang tidak valid untuk pendaftaran baru!');
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
                    // Form ini ("Tambah Ternak Baru") cuma untuk kandang kosong.
                    // Kandang yang sudah terisi harus lewat menu Ternak Masuk.
                    try {
                        $kandang = $this->getKandangAndCheckEmpty($kandangId);
                    } catch (ValidationException $e) {
                        throw ValidationException::withMessages([
                            "livestocks.{$index}.kandang_id" => collect($e->errors())->flatten()->first(),
                        ]);
                    }
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

            // Kelompokkan error per index baris (livestocks.{index}.field => pesan)
            // supaya form bisa menandai baris mana yang bermasalah.
            $failedRows = [];
            foreach ($e->errors() as $field => $messages) {
                if (preg_match('/^livestocks\.(\d+)\./', $field, $m)) {
                    $rowNumber = ((int) $m[1]) + 1; // 1-based, sesuai label "Ternak #N"
                    $failedRows[$rowNumber] = array_merge($failedRows[$rowNumber] ?? [], $messages);
                }
            }

            return back()->withErrors($e->errors())->withInput()
                ->with('failedRows', $failedRows)
                ->with('error', 'Gagal menyimpan! Periksa kembali baris yang ditandai merah di bawah.');
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
        // Jenis hewan terkunci (tidak bisa diganti), jadi kandang tujuan pindah
        // WAJIB sejenis dengan livestock ini. Tampilkan: kandang asal livestock ini
        // + kandang kosong lain yang sejenis DAN kapasitasnya cukup untuk menampung
        // seluruh populasi livestock ini (kandang kosong tapi kapasitasnya kurang
        // dari quantity tidak ditampilkan, supaya tidak ada pilihan yang pasti gagal).
        $kandangs = Kandang::withSum('livestocks', 'quantity')
            ->where('livestock_type_id', $livestock->livestock_type_id)
            ->get()
            ->filter(function ($k) use ($livestock) {
                if ($k->id === $livestock->kandang_id) {
                    return true; // kandang asal livestock ini selalu ditampilkan
                }
                $kosong = ($k->livestocks_sum_quantity ?? 0) == 0;
                $cukup = $k->capacity === null || $k->capacity >= $livestock->quantity;
                return $kosong && $cukup;
            })
            ->values();

        return view('admin.livestock.edit', compact('livestock', 'kandangs'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        $validated = $request->validate([
            'kandang_id'    => 'required|exists:kandangs,id',
            'arrival_date'  => 'required|date',
            'avg_weight'    => 'nullable|numeric',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'         => 'nullable|string',
            // name TIDAK divalidasi/diambil dari input — auto-generate dari jenis + kandang di bawah.
            // livestock_type_id TIDAK divalidasi/diupdate — dikunci di sisi view (hidden input)
            // dan di sini di-derive ulang dari data livestock, tidak pernah dipercaya dari request.
            // quantity TIDAK divalidasi/diupdate — hanya lewat LivestockMovement
        ]);

        // Jenis hewan dikunci — selalu pakai jenis livestock ini, tidak pernah dari input.
        $validated['livestock_type_id'] = $livestock->livestock_type_id;

        // Cek kapasitas hanya kalau kandang berubah
        if ((int) $validated['kandang_id'] !== (int) $livestock->kandang_id) {
            $kandangTujuan = Kandang::findOrFail($validated['kandang_id']);

            // Kandang tujuan wajib sejenis dengan livestock ini — mencegah tamper
            // via inspect element mengirim kandang_id dari jenis hewan yang berbeda.
            if ((int) $kandangTujuan->livestock_type_id !== (int) $livestock->livestock_type_id) {
                return back()->withErrors([
                    'kandang_id' => 'Kandang tujuan harus untuk jenis hewan yang sama.',
                ])->withInput();
            }

            try {
                $this->getKandangAndCheckCapacity(
                    $validated['kandang_id'],
                    $livestock->quantity // stok yang akan pindah ke kandang baru
                );
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput()
                    ->with('error', 'Gagal memindahkan kandang! Periksa kapasitas kandang tujuan.');
            }
        } else {
            $kandangTujuan = $livestock->kandang;
        }

        // Nama kelompok otomatis mengikuti jenis hewan + kandang tujuan, sama seperti form Tambah Ternak Baru.
        // Tidak dipercaya dari input manual, supaya nama selalu konsisten dengan kandang saat ini.
        $bulanTahun = \Carbon\Carbon::parse($validated['arrival_date'])->translatedFormat('F Y');
        $validated['name'] = $livestock->livestockType->name . ' - ' . $kandangTujuan->name . ' (' . $bulanTahun . ')';

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