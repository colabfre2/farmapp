<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCropTypeRequest;
use App\Models\CropType;
use App\Services\CropTypeService;
use Illuminate\Http\Request;

class CropTypeController extends Controller
{
    public $service;
    public function __construct(CropTypeService $service) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = $request->input('q');
        $cropTypes = $this->service->getFiltered($query);
        return view('admin.crop-types.index', compact('cropTypes', 'query'));
    }

    public function store(StoreCropTypeRequest $request)
    {
        $this->service->create($request);
        return redirect()->route('admin.crop-types.index')->with('success', 'Created!');
    }

    public function update(StoreCropTypeRequest $request, CropType $cropType)
    {
        $this->service->update($request, $cropType);
        return redirect()->route('admin.crop-types.index')->with('success', 'Updated!');
    }

    public function destroy(CropType $cropType)
    {
        $this->service->delete($cropType);
        return redirect()->route('admin.crop-types.index')->with('success', 'Deleted!');
    }
}
