<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function kandangs()
    {
        return $this->hasMany(Kandang::class);
    }
}