<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\IncomeSource; 
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    // ── Income ───────────────────────────────────────────────

    public function incomeIndex(Request $request)
    {
        $query = $request->input('q');
        
        // PERBAIKAN 1 & 2: Tambahkan Eager Loading dan perbaiki logika pencarian
        $incomes = Income::with('incomeSource')
            ->when($query, function($q) use ($query) {
                $q->whereHas('incomeSource', function($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%{$query}%");
                })->orWhere('notes', 'like', "%{$query}%"); // Tambahkan opsi cari di catatan
            })
            ->latest()
            ->get();
            
        $totalIncome = Income::sum('amount');
        return view('admin.finance.income.index', compact('incomes', 'query', 'totalIncome'));
    }

    public function incomeCreate()
    {
        // PERBAIKAN 4: Gunakan class yang sudah di-import
        $incomeSources = IncomeSource::all();
        return view('admin.finance.income.create', compact('incomeSources'));
    }

    public function incomeStore(Request $request)
    {
        $request->merge([
        'amount' => preg_replace('/[^0-9]/', '', $request->amount)
    ]);
        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'date'             => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        Income::create([
            'user_id'          => auth()->id(),
            'income_source_id' => $validated['income_source_id'],
            'date'             => $validated['date'],
            'amount'           => $validated['amount'],
            'notes'            => $validated['notes'],
        ]);

        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    public function incomeEdit(Income $income)
    {
        $incomeSources = IncomeSource::all();
        return view('admin.finance.income.edit', compact('income', 'incomeSources'));
    }

    public function expenseIndex(Request $request)
    {
        $query = $request->input('q');
        $expenses = Expense::with('expenseCategory')
            ->when($query, fn($q) => $q->where('description', 'like', "%{$query}%"))
            ->latest()->get();
        $totalExpense = Expense::sum('amount');
        return view('admin.finance.expense.index', compact('expenses', 'query', 'totalExpense'));
    }

    public function expenseCreate()
    {
        $expenseCategories = ExpenseCategory::all();
        return view('admin.finance.expense.create', compact('expenseCategories'));
    }

    public function expenseStore(Request $request)
    {
        $request->merge([
        'amount' => preg_replace('/[^0-9]/', '', $request->amount)
    ]);
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date'                => 'required|date',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        Expense::create([
            'user_id'             => auth()->id(),
            'expense_category_id' => $request->expense_category_id,
            'date'                => $request->date,
            'description'         => $request->description,
            'amount'              => $request->amount,
            'notes'               => $request->notes,
        ]);

        return redirect()->route('admin.finance.expense.index')->with('success', 'Expense added successfully!');
    }

    public function expenseEdit(Expense $expense)
    {
        $expenseCategories = ExpenseCategory::all();
        return view('admin.finance.expense.edit', compact('expense', 'expenseCategories'));
    }

    public function incomeUpdate(Request $request, Income $income)
    {
        // ... (Tidak ada perubahan, kodenya sudah aman)
        $request->merge([
        'amount' => preg_replace('/[^0-9]/', '', $request->amount)
    ]);
        $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'date'             => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        $income->update([
            'income_source_id' => $request->income_source_id,
            'date'             => $request->date,
            'amount'           => $request->amount,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil diperbarui!');
    }

    public function incomeDestroy(Income $income)
    {
        $income->delete();
        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil dihapus!');
    }

    // ── Expense ──────────────────────────────────────────────

    // ... (expenseIndex, expenseCreate, expenseStore tidak ada masalah mayor, biarkan seperti semula)
    
    // Saya lampirkan hanya yang perlu diubah:

    public function expenseUpdate(Request $request, Expense $expense)
    {
        // PERBAIKAN 3: Simpan hasil validasi ke variabel, lalu gunakan untuk update
        
        $request->merge([
        'amount' => preg_replace('/[^0-9]/', '', $request->amount)
    ]);
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date'                => 'required|date',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        // Gunakan $validated, bukan $request->all() agar lebih aman
        $expense->update($validated); 

        return redirect()->route('admin.finance.expense.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    // ── Profit & Loss ─────────────────────────────────────────
    // ... (Kode untuk profit & loss sudah bagus)


    public function expenseDestroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.finance.expense.index')->with('success', 'Pengeluaran berhasil dihapus!');
    }

    // ── Profit & Loss ─────────────────────────────────────────

    public function profitLoss(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $monthlyIncome = Income::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpense = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $income  = $monthlyIncome[$i]  ?? 0;
            $expense = $monthlyExpense[$i] ?? 0;
            $months[] = [
                'month'   => date('F', mktime(0, 0, 0, $i, 1)),
                'income'  => $income,
                'expense' => $expense,
                'profit'  => $income - $expense,
            ];
        }

        $totalIncome  = Income::whereYear('date', $year)->sum('amount');
        $totalExpense = Expense::whereYear('date', $year)->sum('amount');
        $netProfit    = $totalIncome - $totalExpense;

        return view('admin.finance.profit-loss', compact('months', 'totalIncome', 'totalExpense', 'netProfit', 'year'));
    }

    public function profitLossExportPdf(Request $request)
{
    $year = $request->input('year', date('Y'));

    $monthlyIncome = Income::selectRaw('MONTH(date) as month, SUM(amount) as total')
        ->whereYear('date', $year)->groupBy('month')->pluck('total', 'month')->toArray();

    $monthlyExpense = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
        ->whereYear('date', $year)->groupBy('month')->pluck('total', 'month')->toArray();

    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $income  = $monthlyIncome[$i]  ?? 0;
        $expense = $monthlyExpense[$i] ?? 0;
        $months[] = [
            'month'   => date('F', mktime(0, 0, 0, $i, 1)),
            'income'  => $income,
            'expense' => $expense,
            'profit'  => $income - $expense,
        ];
    }

    $totalIncome  = Income::whereYear('date', $year)->sum('amount');
    $totalExpense = Expense::whereYear('date', $year)->sum('amount');
    $netProfit    = $totalIncome - $totalExpense;

    $pdf = Pdf::loadView('admin.finance.profit-loss-pdf', compact('months', 'totalIncome', 'totalExpense', 'netProfit', 'year'));

    // Preview di browser (bukan langsung download)
    return $pdf->stream('Laporan-Laba-Rugi-' . $year . '.pdf');
}
}