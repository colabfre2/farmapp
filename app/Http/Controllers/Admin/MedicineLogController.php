<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicineLog;
use App\Models\Medicine;
use App\Models\Livestock;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class MedicineLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $logs = MedicineLog::with('medicine', 'livestock', 'user')
            ->when($query, fn($q) => $q->whereHas('medicine', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.medicine-logs.index', compact('logs', 'query'));
    }

    public function create()
    {
        $medicines  = Medicine::all();
        $livestocks = Livestock::all();
        return view('admin.medicine-logs.create', compact('medicines', 'livestocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_id'  => 'required|exists:medicines,id',
            'livestock_id' => 'required|exists:livestocks,id',
            'dose'         => 'required|numeric|min:0',
            'given_at'     => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $medicine = Medicine::find($request->medicine_id);

        // Kurangi stok obat
        $medicine->decrement('stock', $request->dose);

        // Catat log
        MedicineLog::create([
            'user_id'     => auth()->id(),
            'medicine_id' => $request->medicine_id,
            'livestock_id'=> $request->livestock_id,
            'dose'        => $request->dose,
            'given_at'    => $request->given_at,
            'reason'      => $request->reason,
            'notes'       => $request->notes,
        ]);

        // Catat otomatis ke pengeluaran
        $category = ExpenseCategory::where('name', 'Obat-obatan')->first();
        if ($category) {
            Expense::create([
                'user_id'             => auth()->id(),
                'expense_category_id' => $category->id,
                'date'                => $request->given_at,
                'description'         => 'Pemberian obat ' . $medicine->name . ' ke ternak',
                'amount'              => $medicine->price_per_unit * $request->dose,
                'notes'               => $request->notes,
            ]);
        }

        return redirect()->route('admin.medicine-logs.index')->with('success', 'Log pemberian obat berhasil dicatat!');
    }
}