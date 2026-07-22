<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\StoreLivestockRequest;
use App\Http\Requests\Api\UpdateLivestockRequest;
use App\Http\Resources\LivestockResource;
use App\Models\Livestock;
use App\Models\LivestockMovement;
use Illuminate\Support\Facades\Auth;

class LivestockController extends BaseApiController
{
    public function index()
    {
        $livestocks = Livestock::with(['livestockType', 'user'])->latest()->paginate(10);

        return $this->success(
            LivestockResource::collection($livestocks),
            'Livestock retrieved successfully.'
        );
    }

    public function show(Livestock $livestock)
    {
        $livestock->load(['livestockType', 'user']);

        return $this->success(
            new LivestockResource($livestock),
            'Livestock retrieved successfully.'
        );
    }

    public function store(StoreLivestockRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $livestock = Livestock::create($data);

        // Catat otomatis ke Ternak Masuk kalau quantity awal > 0
        if ($data['quantity'] > 0) {
            LivestockMovement::create([
                'livestock_id' => $livestock->id,
                'user_id'      => Auth::id(),
                'type'         => 'in',
                'quantity'     => $data['quantity'],
                'date'         => $data['arrival_date'] ?? now()->toDateString(),
                'reason'       => 'Data Awal',
                'notes'        => 'Otomatis tercatat saat kandang dibuat via API',
            ]);
        }

        return $this->success(
            new LivestockResource($livestock->load('livestockType', 'user')),
            'Livestock created successfully.',
            201
        );
    }

    public function update(UpdateLivestockRequest $request, Livestock $livestock)
    {
        $livestock->update($request->validated());

        return $this->success(
            new LivestockResource($livestock->fresh()->load('livestockType', 'user')),
            'Livestock updated successfully.'
        );
    }

    public function destroy(Livestock $livestock)
    {
        $livestock->delete();

        return $this->success(null, 'Livestock deleted successfully.');
    }
}