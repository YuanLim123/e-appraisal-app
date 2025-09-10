<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UsersListRequest;
use Illuminate\Http\Request;


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
                if (
                    !in_array($request->sortBy, ['last_name', 'first_name','join_at', 'email'])
                    || (!in_array($request->sortOrder, ['asc', 'desc']))
                ) {
                    return;
                }
                $query->orderBy($request->sortBy, $request->sortOrder);
            })
            ->orderBy('created_at', 'desc')
            ->whereNull('resign_at')
            ->paginate(10);

        $users->load(['departments', 'role', 'role.position']);

        return UserResource::collection($users);
    }
}
