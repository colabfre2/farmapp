<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
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
        'shipping_district',
        'province',
        'destination_id',
        'courier',
        'courier_service',
        'tracking_number',
        'shipping_cost',
        'fee',
        'payment_method',
    ];

    /**
     * Hitung fee layanan harian berjalan: mulai Rp100 di hari itu,
     * lalu naik Rp1 setiap ada order baru pada hari yang sama.
     * Otomatis reset ke Rp100 setiap gonta hari (dihitung dari created_at hari ini).
     */
    public static function nextDailyFee(): float
    {
        $todayOrderCount = static::withTrashed()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return 100 + $todayOrderCount;
    }

    // 🚀 Route model binding pakai order_number, bukan id
    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}