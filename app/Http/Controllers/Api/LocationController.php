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
use App\Models\LocationHd;
use App\Models\LocationRequest;
use App\Models\LocationRequestNotification;
use App\Models\User;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BayResource;
use App\Http\Resources\RowResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;
use App\Http\Resources\LevelResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    private const LOCATION_APPROVER_EMP_ID = '000-000167';

    private function isLocationApprover($user = null): bool
    {
        $user = $user ?? getAuthUser();

        return $user && $user->emp_id === self::LOCATION_APPROVER_EMP_ID;
    }

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
            'location_type' => 'nullable|in:Sale,Warehouse,S,W',
            'side_id' => 'nullable|exists:sides,id',
            'side' => 'nullable|string|in:F,B,Natural',
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

        // Prefer explicit side name from frontend; fallback to side_id lookup
        if ($request->filled('side')) {
            $side_name = $request->side === 'Natural' ? '' : $request->side;
        } elseif ($request->filled('side_id')) {
            $side_name = Side::findOrFail($request->side_id)?->name ?? '';
        } else {
            $side_name = '';
        }
        Log::info([
            'side_name' => $side_name
        ]);
        // Create location name
        // If frontend provides location_type, use it; otherwise fallback to user role.
        $locationType = $request->input('location_type');
        $isSale = in_array($locationType, ['Sale', 'S'], true)
            ? true
            : (in_array($locationType, ['Warehouse', 'W'], true)
                ? false
                : getAuthUser()->getRoleNames()->contains('Sale'));

        if ($isSale) {
            if ($side_name !== '') {
                $locationName = "{$branch->branch_short_name}S_{$zone->name}_{$row->name}_{$side_name}_{$bay->name}_{$level->name}";
            } else {
                $locationName = "{$branch->branch_short_name}S_{$zone->name}_{$row->name}_{$bay->name}_{$level->name}";
            }
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

    public function storeRequest(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'location_category' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'row_id' => 'required|exists:rows,id',
            'bay_id' => 'required|exists:bays,id',
            'level_id' => 'required|exists:levels,id',
            'location_type' => 'nullable|in:Sale,Warehouse,S,W',
            'side' => 'nullable|string|in:F,B,Natural',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()]);
        }

        $branch = Branch::findOrFail($request->branch_id);
        $zone = Zone::findOrFail($request->zone_id);
        $row = Row::findOrFail($request->row_id);
        $bay = Bay::findOrFail($request->bay_id);
        $level = Level::findOrFail($request->level_id);

        $side_name = '';
        if ($request->filled('side')) {
            $side_name = $request->side === 'Natural' ? '' : $request->side;
        }

        $locationType = $request->input('location_type');
        $isSale = in_array($locationType, ['Sale', 'S'], true)
            ? true
            : (in_array($locationType, ['Warehouse', 'W'], true)
                ? false
                : getAuthUser()->getRoleNames()->contains('Sale'));

        $locationName = $this->buildLocationName(
            $branch,
            $zone,
            $row,
            $bay,
            $level,
            $isSale,
            $side_name
        );

        if (
            Location::where('location_name', $locationName)->exists() ||
            LocationRequest::where('location_name', $locationName)
                ->where('status', 'request')
                ->exists()
        ) {
            if (Location::where('location_name', $locationName)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This location code already exists in locations.',
                    'duplicate_in' => 'locations',
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'A pending request for this location code already exists.',
                'duplicate_in' => 'location_requests',
            ], 409);
        }

        $locationRequest = LocationRequest::create([
            'user_id' => auth()->id(),
            'branch_id' => $request->branch_id,
            'location_category' => $request->location_category,
            'location_type' => $isSale ? 'S' : 'W',
            'zone_id' => $request->zone_id,
            'row_id' => $request->row_id,
            'bay_id' => $request->bay_id,
            'level_id' => $request->level_id,
            'side' => $request->input('side'),
            'branch_short_name' => $branch->branch_short_name,
            'location_name' => $locationName,
            'status' => 'request',
        ]);

        $approver = User::where('emp_id', self::LOCATION_APPROVER_EMP_ID)->first();
        if ($approver) {
            LocationRequestNotification::create([
                'location_request_id' => $locationRequest->id,
                'user_id' => $approver->id,
                'message' => "New location request: {$locationName}",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Location request submitted successfully!',
            'data' => $locationRequest,
        ]);
    }

    private function buildLocationName($branch, $zone, $row, $bay, $level, $isSale, $side_name)
    {
        if ($isSale) {
            if ($side_name !== '') {
                return "{$branch->branch_short_name}S_{$zone->name}_{$row->name}_{$side_name}_{$bay->name}_{$level->name}";
            }

            return "{$branch->branch_short_name}S_{$zone->name}_{$row->name}_{$bay->name}_{$level->name}";
        }

        return "{$branch->branch_short_name}W_{$zone->name}_{$row->name}_{$bay->name}_{$level->name}";
    }

    public function checkExists(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()]);
        }

        $locationName = $request->location_name;
        $inLocations = Location::where('location_name', $locationName)->exists();
        $inRequests = LocationRequest::where('location_name', $locationName)
            ->where('status', 'request')
            ->exists();

        return response()->json([
            'status' => 'success',
            'exists' => $inLocations || $inRequests,
            'in_locations' => $inLocations,
            'in_requests' => $inRequests,
        ]);
    }

    public function indexRequests(Request $request)
    {
        $userBranchIds = getAuthUser()->branch_id;

        $query = LocationRequest::with([
            'user:id,name,emp_id',
            'branch:id,branch_name,branch_short_name',
        ])
            ->where('branch_id', $userBranchIds)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $requests,
        ]);
    }

    public function showRequest($id)
    {
        $query = LocationRequest::with([
            'user:id,name,emp_id',
            'branch:id,branch_name,branch_short_name',
            'zone:id,name',
            'row:id,name',
            'bay:id,name',
            'level:id,name',
        ]);

        if (!$this->isLocationApprover()) {
            $query->where('branch_id', getAuthUser()->branch_id);
        }

        $locationRequest = $query->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $locationRequest,
        ]);
    }

    public function approveRequest($id)
    {
        if (!$this->isLocationApprover()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to approve location requests.',
            ], 403);
        }

        $locationRequest = LocationRequest::findOrFail($id);

        if ($locationRequest->status !== 'request') {
            return response()->json([
                'status' => 'error',
                'message' => 'This location request has already been processed.',
            ], 409);
        }

        if (Location::where('location_name', $locationRequest->location_name)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This location code already exists in locations.',
            ], 409);
        }

        $branch = Branch::findOrFail($locationRequest->branch_id);
        $branchCode = $branch->branch_code;

        if (
            LocationHd::where('location_code', $locationRequest->location_name)
                ->where('branch_code', $branchCode)
                ->exists()
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'This location code already exists in location_hd.',
            ], 409);
        }

        $approvedAt = now();
        $locationHdId = null;

        try {
            $locationHdId = $this->insertLocationHd($locationRequest, $branchCode, $approvedAt);

            DB::transaction(function () use ($locationRequest) {
                Location::create([
                    'location_name' => $locationRequest->location_name,
                    'branch_id' => $locationRequest->branch_id,
                ]);

                $locationRequest->update(['status' => 'completed']);

                LocationRequestNotification::where('location_request_id', $locationRequest->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            });
        } catch (\Throwable $e) {
            if ($locationHdId) {
                LocationHd::where('location_id', $locationHdId)->delete();
            }

            Log::error('Location approve failed', [
                'location_request_id' => $locationRequest->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve location request.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Location request approved and added to locations.',
        ]);
    }

    private function insertLocationHd(LocationRequest $locationRequest, string $branchCode, $approvedAt): int
    {
        $typeCode = $this->normalizeLocationTypeCode($locationRequest->location_type);
        $nextId = ((int) LocationHd::max('location_id')) + 1;

        LocationHd::create([
            'location_id' => $nextId,
            'location_code' => $locationRequest->location_name,
            'location_date' => $approvedAt,
            'branch_code' => $branchCode,
            'location_status_code' => 'N',
            'location_type_sub_code' => $typeCode,
            'location_bank_code' => null,
            'location_remark' => null,
            'location_shelf_id' => $locationRequest->row_id,
            'location_bay_id' => $locationRequest->bay_id,
            'location_empsave' => 'AUTO',
            'savetime' => $approvedAt,
            'category_code' => null,
            'type_code' => null,
            'flag_new' => false,
            'code_new' => null,
            'level_qty' => null,
            'update_time' => $approvedAt,
            'local_short_name' => null,
            'group_code' => null,
        ]);

        return $nextId;
    }

    private function normalizeLocationTypeCode(?string $locationType): string
    {
        if (in_array($locationType, ['S', 'Sale'], true)) {
            return 'S';
        }

        if (in_array($locationType, ['W', 'Warehouse'], true)) {
            return 'W';
        }

        return $locationType ?: 'W';
    }

    public function rejectRequest(Request $request, $id)
    {
        if (!$this->isLocationApprover()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to reject location requests.',
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'remark' => 'required|string|max:1000',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()]);
        }

        $locationRequest = LocationRequest::findOrFail($id);

        if ($locationRequest->status !== 'request') {
            return response()->json([
                'status' => 'error',
                'message' => 'This location request has already been processed.',
            ], 409);
        }

        DB::transaction(function () use ($locationRequest, $request) {
            $locationRequest->update([
                'status' => 'cancel',
                'remark' => $request->remark,
            ]);

            LocationRequestNotification::where('location_request_id', $locationRequest->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Location request rejected.',
        ]);
    }

    public function indexNotifications()
    {
        $notifications = LocationRequestNotification::with([
            'locationRequest:id,user_id,location_name,status,branch_id',
            'locationRequest.user:id,name,emp_id',
        ])
            ->where('user_id', auth()->id())
            ->whereHas('locationRequest', function ($query) {
                $query->where('status', 'request');
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }

    public function markNotificationRead($id)
    {
        $notification = LocationRequestNotification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read.',
        ]);
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
