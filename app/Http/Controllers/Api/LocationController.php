<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Bay;
use App\Models\Row;
use App\Models\Side;
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
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function index()
    {
        $zones = Zone::get();
        $rows = Row::get();
        $bays = Bay::get();
        $levels = Level::get();
        $sides = Side::get();

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
                'sides' => $sides,
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
            return response()->json(['status' => 'error', 'message' => $validate->errors()]);
        }
        Log::info(['validate' => $validate->validated()]);

        $branch = Branch::findOrFail($request->branch_id);
        $zone = Zone::findOrFail($request->zone_id);
        $row = Row::findOrFail($request->row_id);
        $bay = Bay::findOrFail($request->bay_id);
        $level = Level::findOrFail($request->level_id);
        $request_side = $request->side_id ?? null; 

        $side_name = $request->side_id === null ? '' : Side::findOrFail($request->side_id)?->name; 
        Log::info([

            'side_name' => $side_name
        ]);
        // Create location name
        if (getAuthUser()->getRoleNames()->contains('Sale')) {

            $locationName = "{$branch->branch_short_name}S_{$zone->name}_{$row->name}_{$side_name}_{$bay->name}_{$level->name}";
        } else {
            $locationName = "{$branch->branch_short_name}W_{$zone->name}_{$row->name}_{$bay->name}_{$level->name}";
        }

        if (Location::where('location_name', $locationName)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This location already exists.'
            ], 409);
        }
        // Save only location_name
        $location = new Location();
        $location->location_name = $locationName;
        $location->branch_id = $request->branch_id;
        $location->save();

        return response()->json(['status' => 'success', 'message' => 'Location successfully saved!']);
    }


    public function showAll(Request $request)
    {

        // dd(getAuthUser()->branch_id);
        $userBranchIds = getAuthUser()->branch_id;
        $roles = getAuthUser()->getRoleNames();

        $query = Location::where('branch_id', $userBranchIds);

        // dd($query->get());
        if ($request->filled('zone')) {
            $zone = strtoupper($request->zone);
            $query->whereRaw("(string_to_array(location_name, '_'))[2] = ?", [$zone]);
        }

        if ($request->filled('row')) {
            $query->whereRaw("(string_to_array(location_name, '_'))[3] = ?", [$request->row]);
        }

        if ($request->filled('bay')) {
            $query->whereRaw("(string_to_array(location_name, '_'))[4] = ?", [$request->bay]);
        }

        if ($request->filled('level')) {
            // Level is always the last segment (warehouse: 5 parts, sale: 6 parts)
            $query->whereRaw(
                "(string_to_array(location_name, '_'))[array_length(string_to_array(location_name, '_'), 1)] = ?",
                [$request->level]
            );
        }

        if ($roles->contains('Sale')) {
            $query->whereRaw("right((string_to_array(location_name, '_'))[1], 1) = 'S'");
        } elseif ($roles->contains('Warehouse')) {
            if (in_array($userBranchIds, [15, 16, 17])) {
                // dd("hello");
                //    $query->get();
            } else {
                $query->where(function ($q) {
                    $q->whereRaw("right((string_to_array(location_name, '_'))[1], 1) = 'W'")
                        ->orWhereRaw("right((string_to_array(location_name, '_'))[1], 1) = 'D'");
                });
            }
        } elseif (
            $roles->contains('Branch Manager') ||
            $roles->contains('Operation Analystis')

        ) {
        } else {

            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $locations = $query->orderBy('created_at')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $locations
        ]);
    }
}
