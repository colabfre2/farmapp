<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlantCare extends Model
{
    protected $fillable = ['name', 'type', 'unit', 'stock', 'price_per_unit', 'description'];

    public function logs()
    {
        return $this->hasMany(PlantCareLog::class);
    }
}