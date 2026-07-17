<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\LivestockResource;
use App\Models\Livestock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockController extends BaseApiController
{
    public function index()
    {
        $livestocks = Livestock::with([
            'livestockType',
            'user'
        ])->latest()->paginate(10);

        return $this->success(
            LivestockResource::collection($livestocks),
            'Livestock retrieved successfully.'
        );
    }

    public function show(Livestock $livestock)
    {
        $livestock->load([
            'livestockType',
            'user'
        ]);

        return $this->success(
            new LivestockResource($livestock),
            'Livestock detail retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'avg_weight' => 'nullable|string|max:100',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $livestock = Livestock::create($validated);

        return $this->success(
            new LivestockResource(
                $livestock->load('livestockType', 'user')
            ),
            'Livestock created successfully.',
            201
        );
    }

    public function update(Request $request, Livestock $livestock)
    {
        $validated = $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'avg_weight' => 'nullable|string|max:100',
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes' => 'nullable|string',
        ]);

        $livestock->update($validated);

        return $this->success(
            new LivestockResource(
                $livestock->fresh()->load('livestockType', 'user')
            ),
            'Livestock updated successfully.'
        );
    }

    public function destroy(Livestock $livestock)
    {
        $livestock->delete();

        return $this->success(
            null,
            'Livestock deleted successfully.'
        );
    }
}