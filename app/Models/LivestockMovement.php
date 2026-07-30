<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockMovement extends Model
{ 
    protected $fillable = [
        'livestock_id',
        'user_id',
        'type',
        'quantity',
        'date',
        'reason',
        'notes',
    ];

    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}