<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\V1\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(UserRequest $request, UserService $userService)
    {
        $user = $userService->store($request->validated());

        return new UserResource($user);
    }
}
