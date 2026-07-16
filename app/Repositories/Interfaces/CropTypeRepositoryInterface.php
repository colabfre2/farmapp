<?php

namespace App\Repositories\Interfaces;

use App\DTOs\CropTypeDTO;

interface CropTypeRepositoryInterface
{
    public function all();

    public function find($id);

    public function store(CropTypeDTO $dto);

    public function update($id, CropTypeDTO $dto);

    public function delete($id);
}