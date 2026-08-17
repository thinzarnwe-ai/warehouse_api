<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequestNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'location_request_id',
        'user_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(LocationRequestDocument::class, 'document_id');
    }

    public function locationRequest()
    {
        return $this->belongsTo(LocationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
