<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Harvest extends Model
{
    
protected $fillable = [
    'user_id',
    'product_name',
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
}
