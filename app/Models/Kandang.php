<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kandang extends Model
{
    protected $fillable = [
        'livestock_type_id',
        'name',
        'capacity',
        'location',
        'description',
    ];

    public function livestockType()
    {
        return $this->belongsTo(LivestockType::class);
    }

    public function livestocks()
    {
        return $this->hasMany(Livestock::class);
    }

    /**
     * Total ekor ternak yang sedang menempati kandang ini.
     */
    public function getCurrentOccupancyAttribute(): int
    {
        return $this->livestocks()->sum('quantity');
    }
}