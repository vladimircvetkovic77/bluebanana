<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'User email is already verified.'
            ], config('responses.OK.code'));
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Verification email sent.'
        ], config('responses.OK.code'));
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
            'message' => 'Verification link is invalid.'
            ], config('responses.BAD_REQUEST.code'));
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
            'message' => 'User email is already verified.'
            ], config('responses.OK.code'));
        }
        $user->markEmailAsVerified();
        return response()->json([
              'message' => 'User email is verified.'
        ], config('responses.OK.code'));
    }
}
