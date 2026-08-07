<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Product;
use App\Exports\StockInExport;
use App\Exports\StockOutExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    // ── Barang Masuk ─────────────────────────────────────────

    public function inIndex(Request $request)
    {
        $query = $request->input('q');
        $movements = StockMovement::with('product', 'user')
            ->where('type', 'in')
            ->when($query, fn($q) => $q->whereHas('product', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.stock.in.index', compact('movements', 'query'));
    }

    public function inCreate()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.stock.in.create', compact('products'));
    }

    public function inStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'reason'     => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        StockMovement::create([
            'product_id' => $request->product_id,
            'user_id'    => auth()->id(),
            'type'       => 'in',
            'quantity'   => $request->quantity,
            'reason'     => $request->reason,
            'notes'      => $request->notes,
        ]);

        $product->increment('stock', $request->quantity);

        return redirect()->route('admin.stock.in.index')->with('success', 'Barang masuk berhasil dicatat!');
    }

    /**
     * Simpan banyak barang masuk sekaligus (borongan/bulk)
     */
    public function inStoreBulk(Request $request)
    {
        $request->validate([
            'movements'               => 'required|array|min:1',
            'movements.*.product_id'  => 'required|exists:products,id',
            'movements.*.quantity'    => 'required|integer|min:1',
            'movements.*.reason'      => 'nullable|string|max:255',
            'movements.*.notes'       => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->movements as $row) {
                $product = Product::findOrFail($row['product_id']);

                StockMovement::create([
                    'product_id' => $row['product_id'],
                    'user_id'    => auth()->id(),
                    'type'       => 'in',
                    'quantity'   => $row['quantity'],
                    'reason'     => $row['reason'] ?? null,
                    'notes'      => $row['notes'] ?? null,
                ]);

                $product->increment('stock', $row['quantity']);
            }

            DB::commit();

            return redirect()->route('admin.stock.in.index')->with('success', 'Semua barang masuk berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses data: ' . $e->getMessage())->withInput();
        }
    }

    // ── Barang Keluar ─────────────────────────────────────────

    public function outIndex(Request $request)
    {
        $query = $request->input('q');
        $movements = StockMovement::with('product', 'user')
            ->where('type', 'out')
            ->when($query, fn($q) => $q->whereHas('product', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.stock.out.index', compact('movements', 'query'));
    }

    public function outCreate()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.stock.out.create', compact('products'));
    }

    public function outStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'reason'     => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup! Stok saat ini: ' . $product->stock])->withInput();
        }

        StockMovement::create([
            'product_id' => $request->product_id,
            'user_id'    => auth()->id(),
            'type'       => 'out',
            'quantity'   => $request->quantity,
            'reason'     => $request->reason,
            'notes'      => $request->notes,
        ]);

        $product->decrement('stock', $request->quantity);

        return redirect()->route('admin.stock.out.index')->with('success', 'Barang keluar berhasil dicatat!');
    }

    /**
     * Simpan banyak barang keluar sekaligus (borongan/bulk)
     */
    public function outStoreBulk(Request $request)
    {
        $request->validate([
            'movements'               => 'required|array|min:1',
            'movements.*.product_id'  => 'required|exists:products,id',
            'movements.*.quantity'    => 'required|integer|min:1',
            'movements.*.reason'      => 'nullable|string|max:255',
            'movements.*.notes'       => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Cek dulu semua baris sebelum eksekusi apapun, biar gak ada
            // yang setengah-setengah kepotong kalau salah satu produk stoknya kurang.
            foreach ($request->movements as $i => $row) {
                $product = Product::lockForUpdate()->find($row['product_id']);

                if (!$product) {
                    throw new \Exception("Produk pada baris #" . ($i + 1) . " tidak ditemukan.");
                }

                if ($product->stock < $row['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup! Stok saat ini: {$product->stock}, diminta: {$row['quantity']}.");
                }
            }

            foreach ($request->movements as $row) {
                $product = Product::findOrFail($row['product_id']);

                StockMovement::create([
                    'product_id' => $row['product_id'],
                    'user_id'    => auth()->id(),
                    'type'       => 'out',
                    'quantity'   => $row['quantity'],
                    'reason'     => $row['reason'] ?? null,
                    'notes'      => $row['notes'] ?? null,
                ]);

                $product->decrement('stock', $row['quantity']);
            }

            DB::commit();

            return redirect()->route('admin.stock.out.index')->with('success', 'Semua barang keluar berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses data: ' . $e->getMessage())->withInput();
        }
    }

        
    public function exportInExcel()
    {
        return Excel::download(new StockInExport, 'Barang-Masuk-' . date('Y-m-d') . '.xlsx');
    }

    public function exportOutExcel()
    {
        return Excel::download(new StockOutExport, 'Barang-Keluar-' . date('Y-m-d') . '.xlsx');
    }
}