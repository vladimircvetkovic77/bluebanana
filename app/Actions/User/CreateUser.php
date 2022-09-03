<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Http\Requests\UserRegisterRequest;
use App\Repositories\ORM\Contracts\OrmUserRepositoryInterface;

class CreateUser
{
    use AsAction;
    public function __construct(private OrmUserRepositoryInterface $userRepository)
    {
      //
    }
    public function handle(UserRegisterRequest $request)
    {
        $user = $this->userRepository->create([
            'username' => $request->username,
            'user_type' => $request->user_type,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'parent_id' => $request->parent_id,
        ]);
        return $user;
    }
}
