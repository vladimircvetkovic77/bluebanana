<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Repositories\ORM\Eloquent\EloquentUserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
//    create constructor
    public function __construct(private EloquentUserRepository $userRepository) {}
    public function sendVerificationEmail(Request $request): JsonResponse
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

    public function verify(Request $request): JsonResponse
    {
        $user = $this->userRepository->find($request->route('id'));
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
