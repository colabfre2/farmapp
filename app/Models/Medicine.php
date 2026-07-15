<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = ['name', 'type', 'unit_id', 'stock', 'price_per_unit', 'description'];

    public function logs()
    {
        return $this->hasMany(MedicineLog::class);
    }
    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}
public function unit() {
    return $this->belongsTo(\App\Models\Unit::class);
}

}