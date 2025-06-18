<?php

namespace App\Http\Controllers;

use App\Http\Resources\BayResource;
use App\Models\Bay;
use App\Models\Row;
use App\Models\Zone;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Http\Resources\RowResource;
use App\Http\Resources\ZoneResource;
use App\Http\Resources\LevelResource;

class ZoneController extends Controller
{
    public function index(Request $request)
{
    $zones = Zone::get();
    $rows = Row::get();
    $bays = Bay::get();
    $levels = Level::get();

    return response()->json([
        'status' => 'success',
        'data' => [
            'zones' => ZoneResource::collection($zones),
            'rows' => RowResource::collection($rows),
            'bays' => BayResource::collection($bays),
            'levels' => LevelResource::collection($levels),
        ]
    ]);
}

}
