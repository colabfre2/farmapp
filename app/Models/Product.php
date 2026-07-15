<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    use SoftDeletes;
    
protected $fillable = [
    'user_id',
    'category_id',
    'unit_id',
    'name',
    'description',
    'price',
    'stock',
    'image',
    'is_active',
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function unit()
{
    return $this->belongsTo(Unit::class);
}
public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

}
