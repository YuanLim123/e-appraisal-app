<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->paginate(10);

        $users->load(['departments', 'role', 'role.position']);
        
        return UserResource::collection($users);
    }
}
