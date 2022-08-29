<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
                ], 200);
            case Password::INVALID_USER:
                return response()->json([
                      'message' => 'Something has gone wrong.'
                ], 404);
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
                ], 200);
            default:
                return response()->json([
                      'message' => 'Something has gone wrong.'
                ], 404);
        }
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'You are not authorized to change password.'
            ], 401);
        }
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'message' => 'Password changed successfully.'
            ], 200);
        }
        return response()->json([
                'message' => 'Old password is incorrect.'
        ], 404);
    }
    public function notice()
    {
        return response()->json([
                'message' => 'Your email needs to be verified.'
        ], 401);
    }
}
