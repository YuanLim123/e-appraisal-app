<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\V1\HR\UserService;

class UserController extends Controller
{
    public function store(UserRequest $request, UserService $userService)
    {
        $user = $userService->store($request->validated());

        return new UserResource($user);
    }
}
