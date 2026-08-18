<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\BayController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\Api\StockTrackingController;
use App\Http\Controllers\ZoneController;
use App\Models\StockTracking;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
     return response()->json([
        'user' => $user,
        'roles' => $user->getRoleNames(),
    ]);
});
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('logout',[AuthController::class,'logout']);
    Route::put('user/branch',[AuthController::class, 'updateBranch']);
    Route::post('location',[LocationController::class, 'store']);
    Route::post('location-request',[LocationController::class, 'storeRequest']);
    Route::post('location-request-documents',[LocationController::class, 'storeDocument']);
    Route::get('location-request-documents',[LocationController::class, 'indexDocuments']);
    Route::get('location-request-documents/{id}',[LocationController::class, 'showDocument']);
    Route::delete('location-request-documents/{id}/lines/{lineId}',[LocationController::class, 'destroyDocumentLine']);
    Route::put('location-request-documents/{id}/lines/{lineId}',[LocationController::class, 'updateDocumentLine']);
    Route::post('location-request-documents/{id}/approve',[LocationController::class, 'approveDocument']);
    Route::post('location-request-documents/{id}/reject',[LocationController::class, 'rejectDocument']);
    Route::get('location-requests',[LocationController::class, 'indexRequests']);
    Route::get('location-requests/{id}',[LocationController::class, 'showRequest']);
    Route::post('location-requests/{id}/approve',[LocationController::class, 'approveRequest']);
    Route::post('location-requests/{id}/reject',[LocationController::class, 'rejectRequest']);
    Route::get('location-check',[LocationController::class, 'checkExists']);
    Route::post('location-check-bulk',[LocationController::class, 'bulkCheckExists']);
    Route::get('notifications',[LocationController::class, 'indexNotifications']);
    Route::patch('notifications/{id}/read',[LocationController::class, 'markNotificationRead']);
    Route::get('location',[LocationController::class, 'index']);
    Route::get('stock_active',[StockTrackingController::class, 'show']);
    Route::get('show_all_stock',[StockTrackingController::class, 'showAll']);
    Route::get('locations',[LocationController::class,'showAll']);
    Route::post('locations/bulk-delete',[LocationController::class,'destroyMany']);
    Route::delete('locations/{id}',[LocationController::class,'destroy']);
    Route::post('stock_tracking_in',[StockTrackingController::class, 'store']);
    Route::get('stock_tracking_in',[StockTrackingController::class, 'stock_in_show']);
    Route::get('user-branch',[StockTrackingController::class, 'branch']);
    Route::get('branches',[LocationController::class, 'allBranches']);
    Route::get('get-product/{pcode}/{branch}', [StockTrackingController::class, 'getPcode']);
    Route::get('product_name/{pname}', [StockTrackingController::class,'getPname']);
    Route::get('product/{pcode}/{branch}',[StockTrackingController::class,'getStockPcode']);
    Route::get('product_name/{pname}/{branch}', [StockTrackingController::class,'getStockPname']);
    Route::post('stock_tracking_out',[StockTrackingController::class,'statusOutStore']);
    Route::get('stock_tracking_out', [StockTrackingController::class,'stock_out_show']);
    Route::post('stock_tracking_transfer',[StockTrackingController::class,'statusTransferStore']);
    Route::get('stock_tracking_transfer',[StockTrackingController::class, 'stock_transfer_show']);
    Route::get('detail/{id}',[StockTrackingController::class, 'detail']);
    Route::get('stock_detail/{id}',[StockTrackingController::class, 'stockDetail']);
    Route::delete('/deleteStockIn/{id}',[StockTrackingController::class, 'destoryStockIn']);
    Route::delete('/deleteStockOut/{id}',[StockTrackingController::class, 'destoryStockOut']);
});
