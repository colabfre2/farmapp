<?php

namespace App\Repositories;

use App\Models\CropType;
use App\DTOs\CropTypeDTO;
use App\Repositories\Interfaces\CropTypeRepositoryInterface;

class CropTypeRepository implements CropTypeRepositoryInterface
{
    public function getDataCropTypes()
    {
        return CropType::all(); // Gunakan 'all()' (lowercase) agar standar
    }

    public function find(int $id)
    {
        return CropType::findOrFail($id);
    }

    public function createCropType(CropTypeDTO $data)
    {
        return CropType::create([
            'name' => $data->name,
            'description' => $data->description
        ]);
    }

    public function updateCropType(CropType $cropType, CropTypeDTO $data)
    {
        $cropType->update([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        return $cropType;
    }

    public function delete(CropType $cropType)
    {
        return $cropType->delete();
    }
}