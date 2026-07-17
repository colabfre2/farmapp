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

    public function storeCropType(CropTypeDTO $request){
        return $this->repository->createCropType($request);
    }

    public function updateCropType(CropType $cropType, CropTypeDTO $request)
    {
        // Pastikan Anda mengirim 2 argumen: $cropType DAN $dto
        return $this->repository->updateCropType($cropType, $request->toDTO());
    }

    public function deleteCropType(CropType $cropType)
    {
        // Pastikan Anda mengirim 1 argumen: $cropType
        return $this->repository->delete($cropType);
    }
}