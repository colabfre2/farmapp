<?php

namespace App\Repositories;

use App\Models\CropType;
use App\DTOs\CropTypeDTO;
use App\Repositories\Interfaces\CropTypeRepositoryInterface;

class ProductRepository implements CropTypeRepositoryInterface
{
    public function all()
    {
        return CropType::All();
    }

    public function find($id)
    {
        return CropType::findOrFail($id);
    }

    public function store(CropTypeDTO $dto)
    {
        return CropType::create([
            'name' => $dto->name,
            'description'    
        ]);
    }

    public function update($id, CropTypeDTO $dto)
    {
        $product = CropType::findOrFail($id);

        $product->update([
            'name' => $dto->name,
            'price' => $dto->price,
            'stock' => $dto->stock,
        ]);

        return $product;
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();
    }
}