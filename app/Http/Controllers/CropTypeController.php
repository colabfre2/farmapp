<?php

namespace App\Http\Controllers;

use App\Http\Requests\; // Ensure this file exists at app/Http/Requests/StoreCropTypeRequest.php
use App\Models\CropType;
use App\Services\CropTypeService;
use Illuminate\Http\Request;

class CropTypeController extends Controller
{
    public CropTypeService $service;

    public function __construct(CropTypeService $service)
    {
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
        // Convert the FormRequest to a DTO before passing to service
        $this->service->createCropType($request->toDTO());

        return redirect()->route('admin.crop-types.index')->with('success', 'Created!');
    }

    public function update(StoreCropTypeRequest $request, CropType $cropType)
    {
        // Fixed: pass $cropType and $request->toDTO()
        $this->service->updateCropType($cropType, $request->toDTO());

        return redirect()->route('admin.crop-types.index')->with('success', 'Updated!');
    }

    public function destroy(CropType $cropType)
    {
        $this->service->deleteCropType($cropType);

        return redirect()->route('admin.crop-types.index')->with('success', 'Deleted!');
    }
}