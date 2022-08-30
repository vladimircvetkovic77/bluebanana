<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'parent_id' => $request->parent_id,
        ]);

        $user->sendEmailVerificationNotification();

        return new UserResource($user);
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password', 'remember_me']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                  'error' => config('responses.BAD_REQUEST.message')
            ], config('responses.BAD_REQUEST.code'));
        }
        return new UserResource(auth()->user());
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ], config('responses.OK.code'));
    }
}
