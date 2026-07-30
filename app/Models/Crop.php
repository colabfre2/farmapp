<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'user_id',
    'crop_type_id',
    'crop_variety_id',
    'farm_id',
    'name',
    'planted_at',
    'expected_harvest_at',
    'actual_harvest_at',
    'status',
    'notes',
];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cropType()
    {
        return $this->belongsTo(CropType::class);
    }

    public function cropVariety()
    {
        return $this->belongsTo(CropVariety::class);
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function plantCareLogs()
    {
        return $this->hasMany(PlantCareLog::class);
    }
}