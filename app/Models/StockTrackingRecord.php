<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTrackingRecord extends Model
{
    use HasFactory;
    protected $fillable = ['status','qty','user_id','remark','transfer_location'];

   public function stockTracking()
    {
        return $this->belongsTo(StockTracking::class, 'stock_tracking_id');
    }

}
