<?php

namespace App\Http\Controllers;

use App\Http\Resources\LevelResource;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
        {
            $levels = Level::get();

            return response()->json(['status'=>'success','data' => LevelResource::collection($levels)]);
        }
}
