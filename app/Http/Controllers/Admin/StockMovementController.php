<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Product;
use App\Exports\StockInExport;
use App\Exports\StockOutExport;
use Maatwebsite\Excel\Facades\Excel;

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

        
    public function exportInExcel()
    {
        return Excel::download(new StockInExport, 'Barang-Masuk-' . date('Y-m-d') . '.xlsx');
    }

    public function exportOutExcel()
    {
        return Excel::download(new StockOutExport, 'Barang-Keluar-' . date('Y-m-d') . '.xlsx');
    }
}