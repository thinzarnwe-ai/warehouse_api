<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use App\Models\StockTracking;
use Illuminate\Support\Facades\DB;
use App\Models\StockTrackingRecord;
use App\Http\Controllers\Controller;

class StockTrackingController extends Controller
{
    public function branch(Request $request)
    {
        $branches = UserBranch::with('branch')
            ->where('user_id', auth()->id())
            ->get()
            ->pluck('branch')
            ->unique('id')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $branches->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->branch_name,
                    'short_name' => $branch->branch_short_name
                ];
            }),

        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'location_name' => 'required',
            'product_code' => 'required',
            'product_name' => 'required',
            'qty' => 'required',
            'remark' => 'required',
        ]);

        try {
            $stock_tracking = StockTracking::where('from_branch', $request->from_branch)
                ->where('location_name', $request->location_name)
                ->where('product_code', $request->product_code)
                ->first();
            if ($stock_tracking) {
                $stock_tracking->update(['total_qty' => $stock_tracking->total_qty + $request->qty]);
            } else {
                $stock_tracking = new StockTracking();
                $stock_tracking->product_code = $request->product_code;
                $stock_tracking->product_name = $request->product_name;
                $stock_tracking->location_name = $request->location_name;
                $stock_tracking->total_qty = $request->qty;
                $stock_tracking->from_branch = $request->from_branch;
                $stock_tracking->save();
            }

            $detail = new StockTrackingRecord();
            $detail->stock_tracking_id = $stock_tracking->id;
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

    public function getPcode($pcode)
    {

        $productName = DB::connection('pg_master')
            ->table('master_data.master_product')
            ->select('product_code', 'product_name1')
            ->where('product_code', $pcode)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'product_name' => $productName,
            ]

        ]);
    }

    public function show(Request $request)
    {
        $query = StockTracking::with('stockTrackingRecords');

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        if ($request->product_code) {
            $query->where('product_code', $request->product_code);
        }

        $results = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function stock_in_show(Request $request)
    {
           $query = StockTrackingRecord::with('stockTracking')
                ->where('status', 'in');

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
            }

            if ($request->product_code) {
                $query->whereHas('stockTracking', function ($q) use ($request) {
                    $q->where('product_code', $request->product_code);
                });
            }

            $results = $query->latest()->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);
    }

    public function statusOutStore(Request $request)
    {
        // return response()->json(['message'=>'hello world']);
        $request->validate([
            'location_name' => 'required',
            'product_code' => 'required',
            'product_name' => 'required',
            'qty' => 'required',
            'reduce_qty' => 'required',
            'remark' => 'required',
        ]);

        try {
            $stock_tracking = StockTracking::where('from_branch', $request->from_branch)
                ->where('location_name', $request->location_name)
                ->where('product_code', $request->product_code)
                ->first();
            if ($stock_tracking) {
                $stock_tracking->update(['total_qty' => $stock_tracking->total_qty - $request->reduce_qty]);
            } 

            $detail = new StockTrackingRecord();
            $detail->stock_tracking_id = $stock_tracking->id;
            $detail->qty = $request->reduce_qty;
            $detail->status = 'out';
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


    public function getStockPcode($pcode, $branch)
        {

            $stockItem = StockTracking::where('product_code', $pcode)
                ->where('from_branch', $branch)
                ->select('product_name', 'location_name', 'total_qty')
                ->first();
            if (!$stockItem) {
                return response()->json(['status' => 'error', 'data' => 'Product Not Found!'], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => [
                    $stockItem,
                ],
            ]);
        }


    public function stock_out_show(Request $request)
        {
            $query = StockTrackingRecord::with('stockTracking')
                    ->where('status', 'out');

                if ($request->filled('from_date')) {
                    $query->whereDate('created_at', '>=', $request->from_date);
                }

                if ($request->filled('to_date')) {
                    $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
                }

                if ($request->product_code) {
                    $query->whereHas('stockTracking', function ($q) use ($request) {
                        $q->where('product_code', $request->product_code);
                    });
                }

                $results = $query->latest()->paginate(10);

                return response()->json([
                    'status' => 'success',
                    'data' => $results
                ]);
        }


public function statusTransferStore(Request $request)
    {
        // return response()->json(['message'=>'hello world']);
        $request->validate([
            'location_name' => 'required',
            'product_code' => 'required',
            'product_name' => 'required',
            'qty' => 'required',
            'transfer_qty' => 'required',
            'remark' => 'required',
            'transfer_location' => 'required',
        ]);

        try {
            $stock_tracking = StockTracking::where('from_branch', $request->from_branch)
                ->where('location_name', $request->location_name)
                ->where('product_code', $request->product_code)
                ->first();
            // if ($stock_tracking) {
            //     $stock_tracking->update(['total_qty' => $stock_tracking->total_qty - $request->reduce_qty]);
            // } 

            $detail = new StockTrackingRecord();
            $detail->stock_tracking_id = $stock_tracking->id;
            $detail->qty = $request->transfer_qty;
            $detail->status = 'transfer';
            $detail->user_id = getAuthUser()->id;
            $detail->remark = $request->remark;
            $detail->transfer_location = $request->transfer_location;
            $detail->save();

            return response()->json([
                'success' => true,
                'message' => 'Records saved successfully.',
                'data' => [
                    // 'stock_tracking' => $stock_tracking,
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


        public function stock_transfer_show(Request $request)
        {
            $query = StockTrackingRecord::with('stockTracking')
                    ->where('status', 'transfer');

                if ($request->filled('from_date')) {
                    $query->whereDate('created_at', '>=', $request->from_date);
                }

                if ($request->filled('to_date')) {
                    $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
                }

                if ($request->product_code) {
                    $query->whereHas('stockTracking', function ($q) use ($request) {
                        $q->where('product_code', $request->product_code);
                    });
                }

                $results = $query->latest()->paginate(5);

                return response()->json([
                    'status' => 'success',
                    'data' => $results
                ]);
        }

}
