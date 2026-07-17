<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\CropResource;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CropController extends BaseApiController
{
    public function index()
{
    $crops = Crop::with([
        'cropType',
        'user'
    ])->latest()->paginate(10);

    return $this->success(
        CropResource::collection($crops),
        'Crop retrieved successfully.'
    );
}

    public function show(Crop $crop)
{
    $crop->load([
        'cropType',
        'user'
    ]);

    return $this->success(
        new CropResource($crop),
        'Crop detail retrieved successfully.'
    );
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'crop_type_id' => 'required|exists:crop_types,id',
        'name' => 'required|string|max:255',
        'planted_at' => 'required|date',
        'expected_harvest_at' => 'required|date',
        'actual_harvest_at' => 'nullable|date',
        'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
        'notes' => 'nullable|string',
    ]);

    $validated['user_id'] = auth::user()->id;

    $crop = Crop::create($validated);

    return $this->success(
        new CropResource($crop->load('cropType', 'user')),
        'Crop created successfully.',
        201
    );
}

    public function update(Request $request, Crop $crop)
{
    $validated = $request->validate([
        'crop_type_id' => 'required|exists:crop_types,id',
        'name' => 'required|string|max:255',
        'planted_at' => 'required|date',
        'expected_harvest_at' => 'required|date',
        'actual_harvest_at' => 'nullable|date',
        'status' => 'required|in:Bibit,Pertumbuhan,Dipanen',
        'notes' => 'nullable|string',
    ]);

    $crop->update($validated);

    return $this->success(
        new CropResource(
            $crop->fresh()->load('cropType', 'user')
        ),
        'Crop updated successfully.'
    );
}

    public function destroy(Crop $crop)
    {
        $crop->delete();

        return $this->success(
            null,
            'Crop deleted successfully.'
        );
    }
}