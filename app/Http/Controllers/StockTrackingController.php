<?php

namespace App\Http\Controllers;
use App\Models\StockTrackingRecord;
use App\Models\StockTracking;
use Illuminate\Http\Request;

class StockTrackingController extends Controller
{
  public function store(Request $request)
{
    $request->validate([
        'location' => 'required',
        'product_code' => 'required',
        'product_name' => 'required',
        'qty' => 'required',
        'remark' => 'required',
    ]);

    try {
        $stock_tracking = StockTracking::where('from_brach', $request->from_branch_id)
            ->firstOrFail();
            
        if($stock_tracking) {
            $stock_tracking->update(['total_qty' => $stock_tracking->total_qty + $request->qty]);
        } else {
            $stock_tracking = new StockTracking();
            $stock_tracking->product_code = $request->product_code;
            $stock_tracking->product_name = $request->product_name;
            $stock_tracking->location_id = $request->location_id;
            $stock_tracking->total_qty = $request->qty;
            $stock_tracking->from_branch = $request->from_branch;
            $stock_tracking->save();
        }
        
        $detail = new StockTrackingRecord();
        $detail->stocking_tracking_id = $stock_tracking->id;
        $detail->qty = $request->qty;
        $detail->status = 'in';
        $detail->user_id = getAuthUser()->id;
        $detail->remark = $request->remark;
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Records saved successfully.',
            'data' => [
                'stock_tracking' => $stock_tracking,
                'detail' => $detail
            ]
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
