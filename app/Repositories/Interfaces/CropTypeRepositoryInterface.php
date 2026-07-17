<?php

namespace App\Repositories\Interfaces;

use App\Models\CropType;
use App\DTOs\CropTypeDTO;

interface CropTypeRepositoryInterface
{
    public function getDataCropTypes();
    
    public function find(int $id);
    
    public function createCropType(CropTypeDTO $data);

    public function updateCropType(CropType $cropType, CropTypeDTO $data);

    public function delete(CropType $cropType);
}