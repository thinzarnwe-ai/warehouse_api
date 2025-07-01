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
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('logout',[AuthController::class,'logout']);
    Route::post('location',[LocationController::class, 'store']);
    Route::get('location',[LocationController::class, 'index']);
    Route::get('stock_active',[StockTrackingController::class, 'show']);
    Route::get('show_all_stock',[StockTrackingController::class, 'showAll']);
    Route::get('locations',[LocationController::class,'showAll']);
    Route::post('stock_tracking_in',[StockTrackingController::class, 'store']);
    Route::get('stock_tracking_in',[StockTrackingController::class, 'stock_in_show']);
    Route::get('user-branch',[StockTrackingController::class, 'branch']);
    Route::get('product/{pcode}', [StockTrackingController::class, 'getPcode']);
    Route::get('product/{pcode}/{branch}',[StockTrackingController::class,'getStockPcode']);
    Route::post('stock_tracking_out',[StockTrackingController::class,'statusOutStore']);
    Route::get('stock_tracking_out', [StockTrackingController::class,'stock_out_show']);
    Route::post('stock_tracking_transfer',[StockTrackingController::class,'statusTransferStore']);
    Route::get('stock_tracking_transfer',[StockTrackingController::class, 'stock_transfer_show']);
});




// Route::post('/store-document', [DocumentController::class, 'store']);