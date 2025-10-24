<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\V1\HR\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function store(UserRequest $request)
    {
        $this->userService->store($request->validated());

        return response()->json([
            'message' => 'User created successfully.',
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($request->validated(), $user);

        return response()->json([
            'message' => 'User updated successfully.',
        ], 201);
    }
}
