<?php

namespace App\Models;

use App\Models\StockTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['location_name','branch_id'];

     public function stockTrackings()
        {
            return $this->hasMany(StockTracking::class, 'stock_tracking_id', 'id');
        }
}
