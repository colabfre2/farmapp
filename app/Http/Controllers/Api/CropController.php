<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\UpdateCropStatusRequest;
use App\Http\Resources\CropResource;
use App\Models\Crop;

class CropController extends BaseApiController
{
    // Read-only: admin di mobile hanya boleh melihat data.
    // Create/update penuh/hapus tetap wajib lewat web (auto-generate nama,
    // relasi Farm & Varian, logic harvest_type, dsb hanya dikelola dari web).

    public function index()
    {
        $crops = Crop::with(['cropType', 'cropVariety', 'farm', 'user'])->latest()->paginate(10);

        return $this->success(
            CropResource::collection($crops),
            'Crop retrieved successfully.'
        );
    }

    public function show(Crop $crop)
    {
        $crop->load(['cropType', 'cropVariety', 'farm', 'harvests', 'plantCareLogs.plantCare', 'user']);

        return $this->success(
            new CropResource($crop),
            'Crop detail retrieved successfully.'
        );
    }

    // Update terbatas: hanya status pertumbuhan & catatan yang boleh diubah dari mobile.
    // Kalau status diubah jadi "Dipanen", ini artinya siklus tanam ditandai selesai
    // (sama seperti keputusan manual admin di web).
    public function updateStatus(UpdateCropStatusRequest $request, Crop $crop)
    {
        $crop->update($request->validated());

        return $this->success(
            new CropResource($crop->fresh()->load(['cropType', 'cropVariety', 'farm', 'user'])),
            'Status tanaman berhasil diperbarui.'
        );
    }
}