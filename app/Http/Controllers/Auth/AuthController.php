<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Actions\User\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;

class AuthController extends Controller
{
    /**
     * @param UserRegisterRequest $request
     * @return UserResource
     */
    public function register(UserRegisterRequest $request): UserResource
    {
        $user = CreateUser::run($request);
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
