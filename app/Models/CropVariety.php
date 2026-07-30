<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropVariety extends Model
{
    protected $fillable = ['crop_type_id', 'name', 'description'];

    public function cropType()
    {
        return $this->belongsTo(CropType::class);
    }

    public function crops()
    {
        return $this->hasMany(Crop::class);
    }
}