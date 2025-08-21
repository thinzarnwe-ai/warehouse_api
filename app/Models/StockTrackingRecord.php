<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTrackingRecord extends Model
{
    use HasFactory;
    protected $fillable = ['stock_tracking_id','status','qty','user_id','remark','transfer_location_id'];

   public function stockTracking()
    {
        return $this->belongsTo(StockTracking::class, 'stock_tracking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(){
    return $this->belongsTo(Location::class,'transfer_location_id', 'id');
}

}
