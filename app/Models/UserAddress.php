<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'province',
        'city',
        'district',
        'subdistrict',
        'destination_id',
        'address_detail',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format alamat lengkap untuk ditampilkan.
     */
    public function getFullAddressAttribute(): string
    {
        return $this->address_detail . ', ' . $this->district . ', ' . $this->city . ', ' . $this->province
            . ($this->postal_code ? ' ' . $this->postal_code : '');
    }
}