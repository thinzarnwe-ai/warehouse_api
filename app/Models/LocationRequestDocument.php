<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequestDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'user_id',
        'branch_id',
        'branch_short_name',
        'status',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function lines()
    {
        return $this->hasMany(LocationRequest::class, 'document_id');
    }

    public function notifications()
    {
        return $this->hasMany(LocationRequestNotification::class, 'document_id');
    }
}
