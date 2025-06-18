<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Bay;
use App\Models\Row;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Branch;
use App\Models\Location;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use App\Http\Resources\BayResource;
use App\Http\Resources\RowResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;
use App\Http\Resources\LevelResource;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
public function index()
{
    $zones = Zone::get();
    $rows = Row::get();
    $bays = Bay::get();
    $levels = Level::get();

    $branches = UserBranch::with('branch')
        ->where('user_id', auth()->id())
        ->get()
        ->pluck('branch')
        ->unique('id')
        ->values();

    return response()->json([
        'status' => 'success',
        'data' => [
            'zones' => ZoneResource::collection($zones),
            'rows' => RowResource::collection($rows),
            'bays' => BayResource::collection($bays),
            'levels' => LevelResource::collection($levels),
            'branches' => $branches->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->branch_name,
                    'short_name' => $branch->branch_short_name
                ];
            }),
        ]
    ]);
}


    public function store(Request $request)
    {
        
    $validate = Validator::make($request->all(), [
        'branch_id' => 'required|exists:branches,id',
        'zone_id' => 'required|exists:zones,id',
        'row_id' => 'required|exists:rows,id',
        'bay_id' => 'required|exists:bays,id',
        'level_id' => 'required|exists:levels,id',
    ]);

    if ($validate->fails()) {
        return response()->json(['status' => 'error', 'data' => $validate->errors()]);
    }

    // Fetch name parts
    $branch = Branch::findOrFail($request->branch_id);
    $zone = Zone::findOrFail($request->zone_id);
    $row = Row::findOrFail($request->row_id);
    $bay = Bay::findOrFail($request->bay_id);
    $level = Level::findOrFail($request->level_id);

    // Create location name
    $locationName = "{$branch->branch_short_name}_{$zone->name}_{$row->name}_{$bay->name}_{$level->name}";

    // Save only location_name
    $location = new Location();
    $location->location_name = $locationName;
    $location->save();

    return response()->json(['status' => 'success', 'data' => 'Location successfully saved!']);
    }


    public function showAll(Request $request){
          $query = Location::query();

            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
              $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
            }

            $locations = $query->orderBy('created_at', 'desc')->paginate(5);

            return response()->json([
                'status' => 'success',
                'data' => $locations
            ]);
    }
}
