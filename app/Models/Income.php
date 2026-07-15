<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'income_source_id',
        'date',
        'source',
        'amount',
        'notes',
    ];

    public function incomeSource()
    {
        return $this->belongsTo(IncomeSource::class);
    }
}