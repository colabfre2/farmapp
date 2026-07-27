<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = ['name', 'area_size', 'area_unit', 'description'];

    public function crops()
    {
        return $this->hasMany(Crop::class);
    }
}