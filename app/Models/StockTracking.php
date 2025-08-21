<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTracking extends Model
{
    use HasFactory;
    protected $fillable = ['product_code','product_name','total_qty','from_branch','status','location_id']; 

 public function stockTrackingRecords()
    {
        return $this->hasMany(StockTrackingRecord::class, 'stock_tracking_id');
    }

public function location(){
    return $this->belongsTo(Location::class,'location_id', 'id');
}


}
