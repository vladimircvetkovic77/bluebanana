<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;


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

    public function login(UserLoginRequest $request): UserResource|JsonResponse
    {
        $credentials = $request->only(['email', 'password', 'remember_me']);

        //TODO: implement firebase login
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'error' => config('responses.BAD_REQUEST.message')
            ], config('responses.BAD_REQUEST.code'));
        }

        return new UserResource(auth()->user());
    }

    /**
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], config('responses.OK.code'));
    }
}
