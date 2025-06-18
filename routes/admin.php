<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Auth::routes();
// Route::group(['middleware' => ['auth']], function () {



    Route::group(['prefix' => 'admins'], function(){
        Route::get('/',function()
        {
            return view('admins.layouts.home');
        });
        Route::get('/login',[UserController::class,'login'])->name('admins.login');
        Route::post('/check',[UserController::class,'checkLogin'])->name('check');
        Route::get('/logout',[UserController::class,'login'])->name('logout');      

    });
// });


