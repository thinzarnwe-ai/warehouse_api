<?php

namespace App\Http\Controllers;

use App\Http\Resources\BayResource;
use App\Models\Bay;
use Illuminate\Http\Request;

class BayController extends Controller
{
   public function index(Request $request)
        {
            $bays = Bay::get();

            return response()->json(['status'=>'success','data' => BayResource::collection($bays)]);
        }
}
