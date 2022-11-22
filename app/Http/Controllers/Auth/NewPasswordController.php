<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redis;

class NewPasswordController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {

            return response()->json([
                'message' => 'Password reset link sent successfully.'
            ], config('responses.OK.code'));
        }

        return response()->json([
            'message' => config('responses.NOT_FOUND.message')
        ], config('responses.NOT_FOUND.code'));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset($request->only('email', 'password', 'token'), function ($user) use ($request) {
            $user->password = Hash::make($request->password);
            $user->save();
            event(new PasswordReset($user));
        });

        return match ($status) {
            Password::PASSWORD_RESET => response()->json([
                'message' => 'Password reset successfully.',
            ], config('responses.OK.code')),
            default => response()->json([
                'message' => config('responses.BAD_REQUEST.message')
            ], config('responses.BAD_REQUEST.code')),
        };
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {

            return response()->json([
                'message' => config('responses.UNAUTHORIZED.message')
            ], config('responses.UNAUTHORIZED.code'));
        }

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'Password changed successfully.'
            ], config('responses.OK.code'));
        }

        return response()->json([
            'message' => 'Old password is incorrect.'
        ], config('responses.BAD_REQUEST.code'));
    }

    public function notice(): JsonResponse
    {
        return response()->json([
            'message' => 'Your email needs to be verified.'
        ], config('responses.UNAUTHORIZED.code'));
    }

    public function setKeyRedis(): JsonResponse
    {
        $user = auth()->user();
        $key = 'random_key';

        Redis::set('test-key', $key);

        return response()->json([
            'message' => 'Key set successfully.'
        ], config('responses.OK.code'));
    }
}
