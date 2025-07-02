<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use App\Models\StockTracking;
use Illuminate\Support\Facades\DB;
use App\Models\StockTrackingRecord;
use Illuminate\Support\Facades\Log;
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

            //   return response()->json([
            //     'success' => true,
            //     'message' => 'Records saved successfully.',
            //     'data' => [
            //       getAuthUser()->id
           
            //     ]
            // ], 201);
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
        $userBranchIds = auth()->user()->user_branches()->pluck('branch_id');
        $userRole = getAuthUser()->getRoleNames()->first();
        $query = StockTracking::with('stockTrackingRecords')
            ->whereIn('from_branch', $userBranchIds)
            ->where('status', $userRole);

        if ($request->location_name) {
            $query->whereRaw('LOWER(location_name) LIKE ?', ['%' . strtolower($request->location_name) . '%']);
        }

        if ($request->product_code) {
            $query->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
        }

        $results = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function showAll(Request $request)
    {
        $userBranchIds = auth()->user()->user_branches()->pluck('branch_id');
        $userRole = getAuthUser()->getRoleNames()->first();
       
        $query = StockTrackingRecord::with('stockTracking')
            ->whereHas('stockTracking', function ($q) use ($userBranchIds,$userRole) {
                $q->whereIn('from_branch', $userBranchIds)
                  ->where('status', $userRole);
            });

       if ($request->product_code && $request->status) {
            $query->where('status', $request->status)
                ->whereHas('stockTracking', function ($q) use ($request) {
                    $q->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
                });
        } elseif ($request->product_code) {
            $query->whereHas('stockTracking', function ($q) use ($request) {
                $q->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
            });
        } elseif ($request->status) {
            $query->where('status', $request->status);
        }

        $results = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function stock_in_show(Request $request)
    {
        $userBranchIds = auth()->user()->user_branches()->pluck('branch_id');
        $userRole = getAuthUser()->getRoleNames()->first();

        $query = StockTrackingRecord::with('stockTracking')
            ->where('status', 'in')
            ->whereHas('stockTracking', function ($q) use ($userBranchIds,$userRole) {
                $q->whereIn('from_branch', $userBranchIds)
                ->where('status', $userRole);
            });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        if ($request->product_code) {
            $query->whereHas('stockTracking', function ($q) use ($request) {
                $q->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
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
        $userRole = getAuthUser()->getRoleNames()->first();
        $stockItem = StockTracking::where('product_code', $pcode)
            ->where('from_branch', $branch)
            ->where('status',$userRole)
            ->select('product_name', 'location_name', 'total_qty')
            ->get();
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
        $userBranchIds = auth()->user()->user_branches()->pluck('branch_id');
        $userRole = getAuthUser()->getRoleNames()->first();

        $query = StockTrackingRecord::with('stockTracking')
            ->where('status', 'out')
            ->whereHas('stockTracking', function ($q) use ($userBranchIds,$userRole) {
                $q->whereIn('from_branch', $userBranchIds)
                ->where('status', $userRole);
            });


        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        if ($request->product_code) {
            $query->whereHas('stockTracking', function ($q) use ($request) {
                $q->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
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
        Log::info(['request'=>$request->all()]);
        $request->validate([
            'location_name' => 'required',
            'product_code' => 'required',
            'product_name' => 'required',
            'qty' => 'required',
            'transfer_qty' => 'required|numeric|min:1',
            'remark' => 'required',
            'transfer_location' => 'required',
            'from_branch' => 'required',
        ]);

        try {
            $authUserId = getAuthUser()->id;
            $fromBranch = $request->from_branch;
            $productCode = $request->product_code;
            $transferQty = $request->transfer_qty;
            $locationName = $request->location_name;
            $transferLocation = $request->transfer_location;

            // 1. Reduce stock from original location
            $stockFrom = StockTracking::where('from_branch', $fromBranch)
                ->where('location_name', $locationName)
                ->where('product_code', $productCode)
                ->first();

            if (!$stockFrom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original stock record not found.',
                ], 404);
            }

            $stockFrom->decrement('total_qty', $transferQty);

            $inRecord = new StockTrackingRecord([
                'stock_tracking_id' => $stockFrom->id,
                'qty' => $transferQty,
                'status' => 'Transfer In',
                'user_id' => $authUserId,
                'remark' => $request->remark,
                'transfer_location' => $transferLocation,
            ]);

            $inRecord->save();
             Log::info(['stockTo not null'=>$inRecord]);
            // 2. Increase or create stock at transfer location
            $stockTo = StockTracking::where('from_branch', $fromBranch)
                ->where('location_name', $transferLocation)
                ->where('product_code', $productCode)
                ->first();
           
            if ($stockTo != null) {
               
                $stockTo->increment('total_qty', $transferQty);
            } else {
                
                $userRole = getAuthUser()->getRoleNames()->first();
                $stockTo = StockTracking::create([
                    'location_name' => $transferLocation,
                    'from_branch' => $fromBranch,
                    'product_code' => $productCode,
                    'product_name' => $request->product_name,
                    'total_qty' => $transferQty,
                    'status' => $userRole,
                ]);
               
            }
            Log::info(['not work'=>$stockTo]);
        //    Log::info($stockTo);
            $outRecord = new StockTrackingRecord([
                'stock_tracking_id' => $stockTo->id,
                'qty' => $transferQty,
                'status' => 'Transfer Out',
                'user_id' => $authUserId,
                'remark' => $request->remark,
                'transfer_location' => $locationName,
            ]);
            $outRecord->save();

            return response()->json([
                'success' => true,
                'message' => 'Stock transfer completed successfully.',
                'data' => [
                    'in_record' => $inRecord,
                    'out_record' => $outRecord,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function stock_transfer_show(Request $request)
    {

        $userBranchIds = auth()->user()->user_branches()->pluck('branch_id');
        $userRole = getAuthUser()->getRoleNames()->first();
        $query = StockTrackingRecord::with('stockTracking')
            ->whereIn('status',  ['Transfer In', 'Transfer Out'])
            ->whereHas('stockTracking', function ($q) use ($userBranchIds,$userRole) {
                $q->whereIn('from_branch', $userBranchIds)
                ->where('status', $userRole);
            });


        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
        }

        if ($request->product_code) {
            $query->whereHas('stockTracking', function ($q) use ($request) {
                $q->whereRaw('LOWER(product_code) LIKE ?', ['%' . strtolower($request->product_code) . '%']);
            });
        }

        $results = $query->latest()->paginate(5);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}
