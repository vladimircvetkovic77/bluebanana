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
            ], 200);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Verification email sent.'
        ], 200);
    }

    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
            'message' => 'Verification link is invalid.'
            ], 400);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
            'message' => 'User email is already verified.'
            ], 200);
        }
        $user->markEmailAsVerified();
        return response()->json([
              'message' => 'User email is verified.'
        ], 200);
    }
}
