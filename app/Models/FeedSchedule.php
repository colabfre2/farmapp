<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    protected $fillable = [
        'time',
        'label',
        'is_active',
        'last_notified_at',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'last_notified_at'  => 'date',
    ];
}