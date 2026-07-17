<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Harvest extends Model
{
    

protected $table = 'harvests';
    
protected $fillable = [
    'user_id',
    'crop_id',
    'harvested_at',
    'quantity',
    'unit_id',
    'selling_price',
    'notes',
];

public function getTotalValueAttribute(): float
{
    return $this->quantity * $this->selling_price;
}

public function crop()
{
    return $this->belongsTo(Crop::class);
}

public function unit()
{
    return $this->belongsTo(Unit::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
}
