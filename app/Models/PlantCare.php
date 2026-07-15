<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlantCare extends Model
{
    protected $fillable = ['name', 'type', 'unit_id', 'stock', 'price_per_unit', 'description'];

    public function logs()
    {
        return $this->hasMany(PlantCareLog::class);
    }
    public function unit() {
    return $this->belongsTo(\App\Models\Unit::class);
}
}