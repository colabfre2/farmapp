<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\UpdateLivestockHealthRequest;
use App\Http\Resources\LivestockResource;
use App\Models\Livestock;

class LivestockController extends BaseApiController
{
    // Read-only: admin di mobile hanya boleh melihat data.
    // Create/update penuh/hapus tetap wajib lewat web (kandang, kapasitas, movement, dsb
    // punya validasi kompleks yang sengaja hanya dikelola dari web).

    public function index()
    {
        $livestocks = Livestock::with(['livestockType', 'kandang', 'user'])->latest()->paginate(10);

        return $this->success(
            LivestockResource::collection($livestocks),
            'Livestock retrieved successfully.'
        );
    }

    public function show(Livestock $livestock)
    {
        $livestock->load(['livestockType', 'kandang', 'user']);

        return $this->success(
            new LivestockResource($livestock),
            'Livestock retrieved successfully.'
        );
    }

    // Update terbatas: hanya status kesehatan & catatan yang boleh diubah dari mobile.
    public function updateHealth(UpdateLivestockHealthRequest $request, Livestock $livestock)
    {
        $livestock->update($request->validated());

        return $this->success(
            new LivestockResource($livestock->fresh()->load(['livestockType', 'kandang', 'user'])),
            'Status kesehatan berhasil diperbarui.'
        );
    }
}