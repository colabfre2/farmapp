<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeedLog extends Model
{
    protected $fillable = ['user_id', 'feed_id', 'livestock_id', 'amount', 'fed_at', 'time_of_day', 'notes'];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
}