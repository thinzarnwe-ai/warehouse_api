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
        if(!Auth::attempt($data)) {
            return response( [
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
    if (!$request->user()) {
        return response()->json([
            'message' => 'User is not authenticated',
            'token' => $request->bearerToken(),
        ], 401);
    }

    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out Successfully.']);
}


}
