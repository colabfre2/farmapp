<?php

namespace App\Services;

use App\Repositories\Interfaces\CropTypeRepositoryInterface;
use App\DTOs\CropTypeDTO;
use App\Models\CropType;

class CropTypeService
{
    protected CropTypeRepositoryInterface $repository;

    public function __construct(CropTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getFiltered(?string $query)
    {
        return CropType::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    }

    public function getAll()
    {
        return $this->repository->getDataCropTypes();
    }

    public function createCropType(CropTypeDTO $request)
    {
        return $this->repository->createCropType($request);
    }

    public function updateCropType(CropType $cropType, CropTypeDTO $request)
    {
        return $this->repository->updateCropType($cropType, $request);
    }

    public function deleteCropType(CropType $cropType)
    {
        return $this->repository->delete($cropType);
    }   
}