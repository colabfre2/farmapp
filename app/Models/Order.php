<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(Orderitem::class);
    }
}
