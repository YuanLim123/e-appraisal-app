<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersListRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index(UsersListRequest $request)
    {
        $users = User::query()
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

        $users->load(['departments', 'role', 'role.position']);

        return UserResource::collection($users);
    }
}
