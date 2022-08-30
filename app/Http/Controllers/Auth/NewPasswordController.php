<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;

class NewPasswordController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        switch ($status) {
            case Password::RESET_LINK_SENT:
                return response()->json([
                      'message' => 'Password reset link sent successfully.'
                ], config('responses.OK.code'));
            case Password::INVALID_USER:
                return response()->json([
                      'message' => config('responses.NOT_FOUND.message')
                ], config('responses.NOT_FOUND.code'));
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->only(
            'email',
            'password',
            'token'
        ), function ($user) use ($request) {
            $user->password = Hash::make($request->password);
            $user->save();
            event(new PasswordReset($user));
        });
        switch ($status) {
            case Password::PASSWORD_RESET:
                return response()->json([
                      'message' => 'Password reset successfully.',
                ], config('responses.OK.code'));
            default:
                return response()->json([
                      'message' => config('responses.BAD_REQUEST.message')
                ], config('responses.BAD_REQUEST.code'));
        }
    }
    public function changePassword(ChangePasswordRequest $request)
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
    public function notice()
    {
        return response()->json([
                'message' => 'Your email needs to be verified.'
        ], config('responses.UNAUTHORIZED.code'));
    }
    public function setKeyRedis()
    {
        $user = auth()->user();

        $key = 'random_key';
        Redis::set('test-key', $key);
        return response()->json([
                'message' => 'Key set successfully.'
        ], config('responses.OK.code'));
    }
}
