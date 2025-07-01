<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTracking extends Model
{
    use HasFactory;
    protected $fillable = ['product_code','product_name','location_id','total_qty','form_branch','status']; 

 public function stockTrackingRecords()
    {
        return $this->hasMany(StockTrackingRecord::class, 'stock_tracking_id');
    }


}
