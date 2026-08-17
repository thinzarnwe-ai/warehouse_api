<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'branch_id',
        'location_category',
        'location_type',
        'zone_id',
        'row_id',
        'bay_id',
        'level_id',
        'side',
        'branch_short_name',
        'location_name',
        'status',
        'remark',
    ];

    public function document()
    {
        return $this->belongsTo(LocationRequestDocument::class, 'document_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function row()
    {
        return $this->belongsTo(Row::class);
    }

    public function bay()
    {
        return $this->belongsTo(Bay::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
