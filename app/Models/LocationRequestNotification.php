<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequestNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_request_id',
        'user_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function locationRequest()
    {
        return $this->belongsTo(LocationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
