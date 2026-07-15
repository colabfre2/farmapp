<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlantCareLog extends Model
{
    protected $fillable = ['user_id', 'plant_care_id', 'crop_id', 'amount', 'cared_at', 'notes'];

    public function plantCare()
    {
        return $this->belongsTo(PlantCare::class);
    }

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}