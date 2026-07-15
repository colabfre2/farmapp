<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $fillable = ['name', 'type', 'unit_id', 'stock', 'price_per_unit', 'description'];

    public function logs()
    {
        return $this->hasMany(FeedLog::class);
    }
    public function unit() {
    return $this->belongsTo(\App\Models\Unit::class);
}

}