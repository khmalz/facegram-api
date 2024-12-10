<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong username or password',
            ], 401);
        }

        $token =  $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))
            ->additional([
                'message' => 'Login success',
                'token' => $token
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success'
        ], 200);
    }
}
