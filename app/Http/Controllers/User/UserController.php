<?php

namespace App\Http\Controllers\User;

use App\Contracts\UserRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserSelfResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getSelf(Request $request)
    {
        $user = $request->user();
        return new UserSelfResource($user);
    }

    public function updateSelf(UserRepositoryContract $userRepository, Request $request)
    {
        $user = $request->user();
        $user = $userRepository->update($user, ['name' => $request->input('name')]);
        return new UserSelfResource($user);
    }
}
