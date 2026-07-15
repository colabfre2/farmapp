<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $fillable = ['name', 'type', 'unit', 'stock', 'price_per_unit', 'description'];

    public function logs()
    {
        return $this->hasMany(FeedLog::class);
    }
    

}