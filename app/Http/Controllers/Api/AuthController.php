<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        if (!Auth::attempt($data)) {
            return response([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('warehouse')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }


    public function logout(Request $request)
        {
            try {
                $user = $request->user();

                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not authenticated',
                        'token' => $request->bearerToken(),
                    ], 401);
                }

                $token = $user->currentAccessToken();

                if ($token) {
                    $token->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }


    public function updateBranch(Request $request)
        {
            $request->validate([
                'branch_id' => 'required|integer|exists:branches,id',
            ]);

            $user = $request->user();
            $user->branch_id = $request->branch_id;
            $user->save();

            return response()->json([
                'message' => 'Branch updated successfully',
                'branch_id' => $user->branch_id,
            ]);
        }
}
