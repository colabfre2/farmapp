<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeedLog;
use App\Models\Feed;
use App\Models\Livestock;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class FeedLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $logs = FeedLog::with('feed', 'livestock', 'user')
            ->when($query, fn($q) => $q->whereHas('feed', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.feed-logs.index', compact('logs', 'query'));
    }

    public function create()
    {
        $feeds      = Feed::all();
        $livestocks = Livestock::all();
        return view('admin.feed-logs.create', compact('feeds', 'livestocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feed_id'      => 'required|exists:feeds,id',
            'livestock_id' => 'required|exists:livestocks,id',
            'amount'       => 'required|numeric|min:0',
            'fed_at'       => 'required|date',
            'time_of_day'  => 'required|in:Pagi,Siang,Sore,Malam',
            'notes'        => 'nullable|string',
        ]);

        $feed = Feed::find($request->feed_id);

        // Kurangi stok pakan
        $feed->decrement('stock', $request->amount);

        // Catat log
        FeedLog::create([
            'user_id'      => auth()->id(),
            'feed_id'      => $request->feed_id,
            'livestock_id' => $request->livestock_id,
            'amount'       => $request->amount,
            'fed_at'       => $request->fed_at,
            'time_of_day'  => $request->time_of_day,
            'notes'        => $request->notes,
        ]);

        // Catat otomatis ke pengeluaran
        $category = ExpenseCategory::where('name', 'Pakan')->first();
        if ($category) {
            Expense::create([
                'user_id'             => auth()->id(),
                'expense_category_id' => $category->id,
                'date'                => $request->fed_at,
                'description'         => 'Pemberian pakan ' . $feed->name,
                'amount'              => $feed->price_per_unit * $request->amount,
                'notes'               => $request->notes,
            ]);
        }

        return redirect()->route('admin.feed-logs.index')->with('success', 'Log pemberian pakan berhasil dicatat!');
    }
}