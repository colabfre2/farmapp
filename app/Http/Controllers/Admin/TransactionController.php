<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;


class TransactionController extends Controller
{
    //
    public function index (Request $request)
    {
        $query = $request->input('q');
        $status = $request->input('status');

        $orders = Order::with('user', 'items')->when($query, fn($q) => $q->where('order_number', 'like', "%{$query}%")
        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$query}%")))->when($status, fn($q) => $q->where('status', $status))->latest()->get();

        return view ('admin.transactions.index', compact('orders', 'query', 'status'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items');
        return view ('admin.transactions.show', compact('order'));
    }
    public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:Pending,Processing,Shipped,Completed,Cancelled',
    ]);

    $oldStatus = $order->status;
    $order->update(['status' => $request->status]);

    // Catat pemasukan otomatis saat status jadi Completed
    // INI HARUS DI LUAR FOREACH
    if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
        $incomeSource = \App\Models\IncomeSource::where('name', 'Penjualan Marketplace')->first();

        \App\Models\Income::create([
            'user_id'          => auth()->id(),
            'income_source_id' => $incomeSource?->id,
            'date'             => now()->toDateString(),
            'amount'           => $order->total_amount,
            'notes'            => 'Otomatis dari Order #' . $order->order_number . ' - ' . $order->user->name,
        ]);
    }

        return redirect()->route('admin.transactions.show', $order)->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function updateTracking(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $order->update(['tracking_number' => $request->tracking_number]);

        return redirect()->route('admin.transactions.show', $order)->with('success', 'Nomor resi berhasil disimpan!');
    }

    public function exportExcel()
    {
        return Excel::download(new TransactionsExport, 'Data-Transaksi-' . date('Y-m-d') . '.xlsx');
    }
}