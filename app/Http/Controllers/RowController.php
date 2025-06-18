<?php

namespace App\Http\Controllers;

use App\Models\Row;
use Illuminate\Http\Request;
use App\Http\Resources\RowResource;

class RowController extends Controller
{
       public function index(Request $request)
        {
            $rows = Row::get();

            return response()->json(['status'=>'success','data' => RowResource::collection($rows)]);
        }
}
