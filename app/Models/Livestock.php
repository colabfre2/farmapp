<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livestock extends Model
{
    use SoftDeletes;

    protected $table = 'livestocks';

    protected $fillable = [
        'user_id',
        'kandang_id',
        'livestock_type_id',
        'arrival_date',
        'name',
        'quantity',
        'avg_weight',
        'health_status',
        'notes',
    ];

    protected $casts = [
        'arrival_date' => 'date',
    ];

    public function livestockType()
    {
        return $this->belongsTo(LivestockType::class);
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    public function movements()
    {
        return $this->hasMany(LivestockMovement::class);
    }

    public function feedLogs()
    {
        return $this->hasMany(FeedLog::class);
    }

    public function medicineLogs()
    {
        return $this->hasMany(MedicineLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}