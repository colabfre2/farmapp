<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\StoreHarvestRequest;
use App\Http\Requests\Api\UpdateHarvestRequest;
use App\Http\Resources\HarvestResource;
use App\Models\Harvest;
use Illuminate\Support\Facades\Auth;

class HarvestController extends BaseApiController
{
    public function index()
    {
        $harvests = Harvest::with([
            'crop',
            'unit',
            'user'
        ])->latest()->paginate(10);

        return $this->success(
            HarvestResource::collection($harvests),
            'Harvest retrieved successfully.'
        );
    }

    public function show(Harvest $harvest)
    {
        $harvest->load([
            'crop',
            'unit',
            'user'
        ]);

        return $this->success(
            new HarvestResource($harvest),
            'Harvest detail retrieved successfully.'
        );
    }

    public function store(StoreHarvestRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        $harvest = Harvest::create($data);

        return $this->success(
            new HarvestResource(
                $harvest->load('crop', 'unit', 'user')
            ),
            'Harvest created successfully.',
            201
        );
    }

    public function update(UpdateHarvestRequest $request, Harvest $harvest)
    {
        $harvest->update($request->validated());

        return $this->success(
            new HarvestResource(
                $harvest->fresh()->load('crop', 'unit', 'user')
            ),
            'Harvest updated successfully.'
        );
    }

    public function destroy(Harvest $harvest)
    {
        $harvest->delete();

        return $this->success(
            null,
            'Harvest deleted successfully.'
        );
    }
}