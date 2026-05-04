<?php

namespace App\Http\Controllers\Api;
use App\Models\ProductNameChangeLog;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Location;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use App\Models\StockTracking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\StockTrackingRecord;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Psy\Readline\Hoa\Console;

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
        'qty' => 'required|numeric|min:1',
        'remark' => 'required',
        'from_branch' => 'required',
    ]);

    $authUser = getAuthUser();
    $rawIdempotencyKey = $request->header('Idempotency-Key');
    $idempotencyFingerprint = implode('|', [
        $authUser->id,
        $request->from_branch,
        $request->location_name,
        $request->product_code,
        $request->product_name,
        $request->qty,
        $request->remark,
    ]);
    $idempotencyKey = $rawIdempotencyKey ?: hash('sha256', $idempotencyFingerprint);
    $responseCacheKey = "stock_tracking:store:response:{$idempotencyKey}";
    $lock = Cache::lock("stock_tracking:store:lock:{$idempotencyKey}", 10);


    if (Cache::has($responseCacheKey)) {
        return response()->json([
            'success' => false,
            'message' => 'Duplicate request. This data was already submitted.',
        ], 409);
    }

    if (!$lock->get()) {
        return response()->json([
            'success' => false,
            'message' => 'Duplicate request is already being processed.',
        ], 429);
    }

    try {
        $responsePayload = DB::transaction(function () use ($request, $authUser) {
        $locationExists = Location::where('location_name', $request->location_name)->exists();

        if (!$locationExists) {
            return [
                'status' => 'error',
                'message' => 'သတ်မှတ်ထားသော Location Name မရှိသေးပါ သဖြင့် Location Name သတ်မှတ်ပေးပါရန်လိုအပ်ပါသည်',
                'data' => [],
                '__http_status' => 404,
            ];
        }

    
        $stockTracking = StockTracking::where('from_branch', $request->from_branch)
            ->where('location_name', $request->location_name)
            ->where('product_code', $request->product_code)
            ->first();

        if ($stockTracking) {
            $stockTracking->increment('total_qty', $request->qty);
        } else {
            $userRole = $authUser->getRoleNames()->first();

            $stockTracking = StockTracking::create([
                'product_code'  => $request->product_code,
                'product_name'  => $request->product_name,
                'location_name' => $request->location_name,
                'total_qty'     => $request->qty,
                'from_branch'   => $request->from_branch,
                'status'        => $userRole,
            ]);
        }

        $detail = StockTrackingRecord::create([
            'stock_tracking_id' => $stockTracking->id,
            'qty'               => $request->qty,
            'status'            => 'in',
            'user_id'           => $authUser->id,
            'remark'            => $request->remark,
        ]);

        return [
            'success' => true,
            'message' => 'Records saved successfully.',
            'data' => [
                'stock_tracking' => $stockTracking,
                'detail' => $detail
            ],
            '__http_status' => 201,
        ];
        });

        $httpStatus = $responsePayload['__http_status'] ?? 201;
        unset($responsePayload['__http_status']);

        if ($httpStatus === 201) {
            Cache::put($responseCacheKey, true, now()->addMinutes(2));
        }

        return response()->json($responsePayload, $httpStatus);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        optional($lock)->release();
    }
}


public function getPcode($pcode, $branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid branch ID',
        ], 400);
    }

    $connection = match ($branch->id) {
        1  => 'pos101_pgsql',
        2  => 'pos102_pgsql',
        3  => 'pos103_pgsql',
        4  => 'pos104_pgsql',
        5  => 'pos105_pgsql',
        6  => 'pos106_pgsql',
        7  => 'pos107_pgsql',
        8  => 'pos108_pgsql',
        9  => 'pos110_pgsql',
        10 => 'pos112_pgsql',
        11 => 'pos113_pgsql',
        12 => 'pos114_pgsql',
        13 => 'pos115_pgsql',
        14 => 'pos109_pgsql',
        15 => 'pos505_pgsql',
        16 => 'pos510_pgsql',
        17 => 'pos511_pgsql',
        default => null,
    };

    if (!$connection) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection not found',
        ], 400);
    }

    try {
        $query = DB::connection($connection)
            ->table('stockcard.vw_searchpricebycat')
            ->select('product_code', 'barcode_code', 'barcode_bill_name', 'unit_rate', 'unit_code');

        if (is_numeric($pcode)) {
            $query->where('barcode_code', $pcode);
        } else {
            $query->where('barcode_bill_name', 'ILIKE', '%' . $pcode . '%');
        }

        $products = $query->limit(10)->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);

    } catch (\Exception $e) {
        
        \Log::error('DB Connection Error: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}



 public function getPname($pname)
{
    $productName = DB::connection('pg_master')
        ->table('master_data.master_product')
        ->select('product_code', 'product_name1')
        ->where('product_name1', 'ILIKE', '%' . $pname . '%') 
        ->where('inactive_po','A')
        ->limit(10)
        ->get();

        
        return response()->json([
            'status' => 'success',
            'data' => [
                'product_name' => $productName,
            ]

        ]);
}

    

    public function show(Request $request)
    {
        $userBranchId = getAuthUser()->branch_id; 
        // $userRole = getAuthUser()->getRoleNames()->first();
        $query = StockTracking::with('stockTrackingRecords')
            ->where('from_branch', $userBranchId)
            // ->where('status', $userRole)
            ->where('total_qty', '!=', 0);
            Log::info($request->location_name);
            if ($request->location_name) {
            $query->where('location_name', $request->location_name);
                }

            if ($request->product_keyword) { 
                $keyword = strtolower($request->product_keyword); 
                $query->where(function ($q) use ($keyword) { $q->whereRaw('LOWER(product_code) LIKE ?', ["%{$keyword}%"]) ->orWhereRaw('LOWER(product_name) LIKE ?', ["%{$keyword}%"]); }); }


        $results = $query->orderBy('updated_at', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function showAll(Request $request)
    {
        $userBranchId = getAuthUser()->branch_id;
        // $userRole = getAuthUser()->getRoleNames()->first();
       
        $query = StockTrackingRecord::with('stockTracking')
            ->whereHas('stockTracking', function ($q) use ($userBranchId) {
                $q->where('from_branch', $userBranchId);
                //   ->where('status', $userRole);
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

    public function detail($id){

        $query = StockTrackingRecord::with('stockTracking','user')
                ->where('id', $id)
                ->get();

        if ($query->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found!',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query, 
        ]);
    }

    public function stockDetail($id){

        $query = StockTracking::with('stockTrackingRecords')
                ->where('id', $id)
                ->get();

        if ($query->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found!',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query, 
        ]);
    }

    public function stock_in_show(Request $request)
    {
        $userBranchId = getAuthUser()->branch_id;
        // $userRole = getAuthUser()->getRoleNames()->first();

        $query = StockTrackingRecord::with('stockTracking')
            ->where('status', 'in')
            ->whereHas('stockTracking', function ($q) use ($userBranchId) {
                $q->where('from_branch', $userBranchId);
                // ->where('status', $userRole);
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
    $request->validate([
        'location_name' => 'required',
        'product_code'  => 'required',
        'product_name'  => 'required',
        'qty'           => 'required|numeric',
        'reduce_qty'    => 'required|numeric',
        'remark'        => 'required',
    ]);

    $authUser = getAuthUser();
    $rawIdempotencyKey = $request->header('Idempotency-Key');
    $idempotencyFingerprint = implode('|', [
        $authUser->id,
        $request->from_branch,
        $request->location_name,
        $request->product_code,
        $request->product_name,
        $request->qty,
        $request->reduce_qty,
        $request->remark,
    ]);
    $idempotencyKey = $rawIdempotencyKey ?: hash('sha256', $idempotencyFingerprint);
    $responseCacheKey = "stock_tracking:status_out_store:response:{$idempotencyKey}";
    $lock = Cache::lock("stock_tracking:status_out_store:lock:{$idempotencyKey}", 10);

    if (Cache::has($responseCacheKey)) {
        return response()->json([
            'success' => false,
            'message' => 'Duplicate request. This data was already submitted.',
        ], 409);
    }

    if (!$lock->get()) {
        return response()->json([
            'success' => false,
            'message' => 'Duplicate request is already being processed.',
        ], 429);
    }

    try {
        $responsePayload = DB::transaction(function () use ($request, $authUser) {

        $stock_tracking = StockTracking::where('from_branch', $request->from_branch)
            ->where('location_name', $request->location_name)
            ->where('product_code', $request->product_code)
            ->first();

        if (!$stock_tracking) {
            return [
                'status'  => 'error',
                'message' => 'Stock not found',
                '__http_status' => 404,
            ];
        }

        if ($request->qty < $request->reduce_qty) {
            return [
                'status'  => 'error',
                'message' => 'Stock တွင် ရှိနေသော total qty ထက်ပိုထုပ်ရျ်မရပါ',
                'data'    => [],
                '__http_status' => 400,
            ];
        }

        $connection = match ($stock_tracking->from_branch) {
            1  => 'pos101_pgsql',
            2  => 'pos102_pgsql',
            3  => 'pos103_pgsql',
            4  => 'pos104_pgsql',
            5  => 'pos105_pgsql',
            6  => 'pos106_pgsql',
            7  => 'pos107_pgsql',
            8  => 'pos108_pgsql',
            9  => 'pos110_pgsql',
            10 => 'pos112_pgsql',
            11 => 'pos113_pgsql',
            12 => 'pos114_pgsql',
            13 => 'pos115_pgsql',
            14 => 'pos109_pgsql',
            15 => 'pos505_pgsql',
            16 => 'pos510_pgsql',
            17 => 'pos511_pgsql',
            default => null,
        };

        if (!$connection) {
            return [
                'status'  => 'error',
                'message' => 'Database connection not found',
                '__http_status' => 400,
            ];
        }

    $barcodeProduct = DB::connection($connection)
        ->table('stockcard.vw_searchpricebycat')
        ->select('barcode_bill_name')
        ->where(function ($query) use ($request) {
            $query->where('barcode_code', $request->product_code);
                //   ->orWhere('product_code', $request->product_code);
        })
        ->first();

         if ($barcodeProduct) {
               $barcodeName = $barcodeProduct->barcode_bill_name;
                $stockName   = $stock_tracking->product_name;
                if ($barcodeName !== $stockName) {
                // dd($barcodeName,$stockName);
                    $stock_tracking->update([
                        'product_name' => $barcodeName
                    ]);


            $productLog = new ProductNameChangeLog();
            $productLog->product_code     = $request->product_code;
            $productLog->old_product_name = $stockName;
            $productLog->new_product_name = $barcodeName;
            $productLog->user_id          = $authUser->id;
            $productLog->save();


        }
        }

        $stock_tracking->update([
            'total_qty' => $stock_tracking->total_qty - $request->reduce_qty
        ]);

        $detail = new StockTrackingRecord();
        $detail->stock_tracking_id = $stock_tracking->id;
        $detail->qty               = $request->reduce_qty;
        $detail->status            = 'out';
        $detail->user_id           = $authUser->id;
        $detail->remark            = $request->remark;
        $detail->save();

        return [
            'success' => true,
            'message' => 'Records saved successfully.',
            'data'    => [
                'stock_tracking' => $stock_tracking,
                'detail'         => $detail
            ],
            '__http_status' => 201,
        ];
        });

        $httpStatus = $responsePayload['__http_status'] ?? 201;
        unset($responsePayload['__http_status']);

        if ($httpStatus === 201) {
            Cache::put($responseCacheKey, true, now()->addMinutes(2));
        }

        return response()->json($responsePayload, $httpStatus);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
        ], 500);
    } finally {
        optional($lock)->release();
    }
}


 public function getStockPcode($pcode, $branch)
    {
        $userRole = getAuthUser()->getRoleNames()->first();

         if (is_numeric($pcode)) {
         $stockItem = StockTracking::where('product_code', $pcode)
            ->where('total_qty', '!=', 0)
            ->where('from_branch', $branch)
            // ->where('status', $userRole)                                                                                                     
            ->select('product_name', 'location_name', 'total_qty')
            ->orderBy('updated_at', 'desc')
            ->get();
    } else {
         $stockItem = DB::table('stock_trackings')
            ->selectRaw('DISTINCT ON (product_name) product_name, product_code, total_qty, location_name')
            ->where('product_name', 'ILIKE', '%' . $pcode . '%')
            ->where('total_qty', '!=', 0)
            ->where('from_branch', $branch)
            ->orderBy('product_name')         
            ->orderBy('created_at', 'asc')            
            ->limit(10)
            ->get();
    }

    // dd($stockItem);

        if ($stockItem->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product Not Found!',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $stockItem, 
        ]);
    }


public function getStockPname($pname, $branch)
{
    $userRole = getAuthUser()->getRoleNames()->first();

   $stockItem = DB::table('stock_trackings')
    ->selectRaw('DISTINCT ON (product_name) product_name, product_code, total_qty, location_name')
    ->where('product_name', 'ILIKE', '%' . $pname . '%')
    ->where('total_qty', '!=', 0)
    ->where('from_branch', $branch)
    ->orderBy('product_name')         
    ->orderBy('created_at', 'asc')            
    ->limit(10)
    ->get();

        

    if ($stockItem->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Product Not Found!',
            'data' => [],
        ], 404);
    }

      return response()->json([
            'status' => 'success',
            'data' => [
                'product_name' => $stockItem,
            ]

        ]);
}



    public function stock_out_show(Request $request)
    {
        $userBranchId = getAuthUser()->branch_id;
        // $userRole = getAuthUser()->getRoleNames()->first();

        $query = StockTrackingRecord::with('stockTracking')
            ->where('status', 'out')
            ->whereHas('stockTracking', function ($q) use ($userBranchId) {
                $q->where('from_branch', $userBranchId);
                // ->where('status', $userRole);
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

        $authUser = getAuthUser();
        $rawIdempotencyKey = $request->header('Idempotency-Key');
        $idempotencyFingerprint = implode('|', [
            $authUser->id,
            $request->from_branch,
            $request->location_name,
            $request->transfer_location,
            $request->product_code,
            $request->product_name,
            $request->qty,
            $request->transfer_qty,
            $request->remark,
        ]);
        $idempotencyKey = $rawIdempotencyKey ?: hash('sha256', $idempotencyFingerprint);
        $responseCacheKey = "stock_tracking:status_transfer_store:response:{$idempotencyKey}";
        $lock = Cache::lock("stock_tracking:status_transfer_store:lock:{$idempotencyKey}", 10);

        if (Cache::has($responseCacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate request. This data was already submitted.',
            ], 409);
        }

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate request is already being processed.',
            ], 429);
        }

        try {
            $responsePayload = DB::transaction(function () use ($request, $authUser) {
                if ($request->qty < $request->transfer_qty) {
                    return [
                        'status' => 'error',
                        'message' => 'Stock တွင် ရှိနေသော total qty ထက်ပိုထုပ်ရျ်မရပါ',
                        'data' => [],
                        '__http_status' => 404,
                    ];
                }
            $authUserId = $authUser->id;
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
                return [
                    'success' => false,
                    'message' => 'Original stock record not found.',
                    '__http_status' => 404,
                ];
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
                
                $userRole = $authUser->getRoleNames()->first();
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

            return [
                'success' => true,
                'message' => 'Stock transfer completed successfully.',
                'data' => [
                    'in_record' => $inRecord,
                    'out_record' => $outRecord,
                ],
                '__http_status' => 201,
            ];
        });

        $httpStatus = $responsePayload['__http_status'] ?? 201;
        unset($responsePayload['__http_status']);

        if ($httpStatus === 201) {
            Cache::put($responseCacheKey, true, now()->addMinutes(2));
        }

        return response()->json($responsePayload, $httpStatus);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        } finally {
            optional($lock)->release();
        }
    }



    public function stock_transfer_show(Request $request)
    {

        $userBranchId = getAuthUser()->branch_id;
        // $userRole = getAuthUser()->getRoleNames()->first();
        $query = StockTrackingRecord::with('stockTracking')
            ->whereIn('status',  ['Transfer In', 'Transfer Out'])
            ->whereHas('stockTracking', function ($q) use ($userBranchId) {
                $q->where('from_branch', $userBranchId);
                // ->where('status', $userRole);
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

public function destoryStockIn($id)
{
  try {
    $stock = StockTrackingRecord::find($id);

    if (!$stock) {
        return response()->json(['message' => 'Not found'], 404);
    }

    $stockTracking = StockTracking::find($stock->stock_tracking_id);

    if ($stockTracking) {
        if ($stockTracking->total_qty >= $stock->qty) {
            $stockTracking->decrement('total_qty', $stock->qty);
        } else {
            return response()->json([
                'message' => 'Delete failed',
                'error' => 'Total Qty must be greater than or equal to delete qty'
            ], 400);
        }
    }

    $stock->update([
        'deleted_at' => now()
    ]);

    // dd(now());
    // dd("hi");

    return response()->json(['message' => 'Deleted successfully']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Delete failed',
            'error' => $e->getMessage()
        ], 500);
    }

}

public function destoryStockOut($id)
{
    try {
        $stock = StockTrackingRecord::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $stockTracking = StockTracking::find($stock->stock_tracking_id);

        if ($stockTracking) {
         $stockTracking->increment('total_qty', $stock->qty);
        }

         $stock->update([
        'deleted_at' => now()
    ]);

        return response()->json(['message' => 'Deleted successfully']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Delete failed',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
