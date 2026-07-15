<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlantCareLog;
use App\Models\PlantCare;
use App\Models\Crop;
use App\Models\Expense;
use App\Models\ExpenseCategory;

class PlantCareLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $logs = PlantCareLog::with('plantCare', 'crop', 'user')
            ->when($query, fn($q) => $q->whereHas('crop', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->latest()->get();
        return view('admin.plant-care-logs.index', compact('logs', 'query'));
    }

    public function create()
    {
        $plantCares = PlantCare::all();
        $crops      = Crop::where('status', '!=', 'Harvested')->get();
        return view('admin.plant-care-logs.create', compact('plantCares', 'crops'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plant_care_id' => 'required|exists:plant_cares,id',
            'crop_id'       => 'required|exists:crops,id',
            'amount'        => 'nullable|numeric|min:0',
            'cared_at'      => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $plantCare = PlantCare::find($request->plant_care_id);

        // Kurangi stok kalau ada amount
        if ($request->amount && $plantCare->stock > 0) {
            $plantCare->decrement('stock', $request->amount);
        }

        // Catat log
        PlantCareLog::create([
            'user_id'       => auth()->id(),
            'plant_care_id' => $request->plant_care_id,
            'crop_id'       => $request->crop_id,
            'amount'        => $request->amount,
            'cared_at'      => $request->cared_at,
            'notes'         => $request->notes,
        ]);

        // Catat otomatis ke pengeluaran kalau ada harga
        if ($request->amount && $plantCare->price_per_unit > 0) {
            $category = ExpenseCategory::where('name', 'Pupuk')->first();
            if ($category) {
                Expense::create([
                    'user_id'             => auth()->id(),
                    'expense_category_id' => $category->id,
                    'date'                => $request->cared_at,
                    'description'         => $plantCare->type . ' - ' . $plantCare->name,
                    'amount'              => $plantCare->price_per_unit * $request->amount,
                    'notes'               => $request->notes,
                ]);
            }
        }

        return redirect()->route('admin.plant-care-logs.index')->with('success', 'Log perawatan tanaman berhasil dicatat!');
    }
}