<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\CropType;
use App\Models\LivestockType;
use App\Models\ExpenseCategory;
use App\Models\Livestock;
use App\Models\Crop;
use App\Models\Harvest;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        // Mencegah error kalau user pencet cari tapi form-nya kosong
        if (empty($query)) {
            return back();
        }

        $categories = Category::where('name', 'like', "%{$query}%")->get();
        $units = Unit::where('name', 'like', "%{$query}%")->get();
        $cropTypes = CropType::where('name', 'like', "%{$query}%")->get();
        $livestockTypes = LivestockType::where('name', 'like', "%{$query}%")->get();
        $expenseCategories = ExpenseCategory::where('name', 'like', "%{$query}%")->get();
        $livestocks = Livestock::where('name', 'like', "%{$query}%")->get();
        $crops = Crop::where('name', 'like', "%{$query}%")->get();
        
        // BUG FIX: Ganti 'crop_id' jadi 'product_name' karena ini pencarian teks
        $harvests = Harvest::where('crop_id', 'like', "%{$query}%")->get(); 
        
        $products = Product::where('name', 'like', "%{$query}%")->get();

        return view('admin.search', compact(
            'query', 'categories', 'units', 'cropTypes', 'livestockTypes',
            'expenseCategories', 'livestocks', 'crops', 'harvests', 'products'
        ));
    }
}