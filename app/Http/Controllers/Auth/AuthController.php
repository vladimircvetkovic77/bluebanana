<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;

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

        $token = $user -> createToken('myapptoken') -> plainTextToken;

        $user->sendEmailVerificationNotification();

        return response([
            'user' => $user,
            'token' => $token
        ], config('responses.CREATED.code'));
    }

    public function login(UserLoginRequest $request)
    {
        // TODO: implement login case when user is already logged in, not to reisue the token
        $credentials = $request->only(['email', 'password', 'remember_me']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                  'error' => config('responses.BAD_REQUEST.message')
            ], config('responses.BAD_REQUEST.code'));
        }
        return response()->json([
            'token' => auth()->user() -> createToken('myapptoken') -> plainTextToken,
            'user' => auth()->user()
        ], config('responses.OK.code'));
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'], config('responses.OK.code'));
    }
}
