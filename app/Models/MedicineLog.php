<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MedicineLog extends Model
{
    protected $fillable = ['user_id', 'medicine_id', 'livestock_id', 'dose', 'given_at', 'reason', 'notes'];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
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