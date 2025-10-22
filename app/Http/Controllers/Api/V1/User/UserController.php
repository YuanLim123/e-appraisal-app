<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersListRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index(UsersListRequest $request)
    {
        $users = User::query()
            ->with(['departments', 'role', 'role.position'])
            ->when($request->joinAfter, function ($query) use ($request) {
                $query->where('join_at', '>', $request->joinAfter);
            })
            ->when($request->joinBefore, function ($query) use ($request) {
                $query->where('join_at', '<', $request->joinBefore);
            })
            ->when($request->sortBy, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder ?? 'asc');
            })
            ->orderBy('created_at', 'desc')
            ->whereNull('resign_at')
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        $user->load(['departments', 'role', 'role.position']);
        return new UserResource($user);
    }

}
