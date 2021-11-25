<?php

namespace App\Http\Controllers\User;

use App\Contracts\UserRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserSelfResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getSelf(UserRepositoryContract $userRepository)
    {
        return new UserSelfResource($userRepository->getById(6));
    }

    public function updateSelf(UserRepositoryContract $userRepository, Request $request)
    {
        $user = $userRepository->getById(6);
        $user->name = $request->input('name');
        $user->save();
        return new UserSelfResource($user);
    }
}
