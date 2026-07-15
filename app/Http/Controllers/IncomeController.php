<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class FinanceController extends Controller
{
    // ── Income ───────────────────────────────────────────────

    public function incomeIndex(Request $request)
    {
        $query = $request->input('q');
        $incomes = Income::when($query, fn($q) => $q->where('source', 'like', "%{$query}%"))
            ->latest()->get();
        $totalIncome = Income::sum('amount');
        return view('admin.finance.income.index', compact('incomes', 'query', 'totalIncome'));
    }

    public function incomeCreate()
    {
        return view('admin.finance.income.create');
    }

    public function incomeStore(Request $request)
    {
        $request->validate([
            'date'   => 'required|date',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes'  => 'nullable|string',
        ]);

        Income::create([
            'user_id' => auth()->id(),
            'date'    => $request->date,
            'source'  => $request->source,
            'amount'  => $request->amount,
            'notes'   => $request->notes,
        ]);

        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    public function incomeEdit(Income $income)
    {
        return view('admin.finance.income.edit', compact('income'));
    }

    public function incomeUpdate(Request $request, Income $income)
    {
        $request->validate([
            'date'   => 'required|date',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes'  => 'nullable|string',
        ]);

        $income->update($request->all());

        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil diperbarui!');
    }

    public function incomeDestroy(Income $income)
    {
        $income->delete();
        return redirect()->route('admin.finance.income.index')->with('success', 'Pemasukan berhasil dihapus!');
    }

    // ── Expense ──────────────────────────────────────────────

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

        return redirect()->route('admin.finance.expense.index')->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function expenseEdit(Expense $expense)
    {
        $expenseCategories = ExpenseCategory::all();
        return view('admin.finance.expense.edit', compact('expense', 'expenseCategories'));
    }

    public function expenseUpdate(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date'                => 'required|date',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()->route('admin.finance.expense.index')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

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
}