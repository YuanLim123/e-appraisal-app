<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        $attributes = $request->validated();
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['username'] = 'Asj#' . $attributes['employee_no'];

        $user = User::create($attributes);
        
        if (!empty($attributes['departments'])) {
            foreach ($attributes['departments'] as $departmentId) {
                $user->departments()->attach($departmentId);
            }
        }

        return new UserResource($user);
    }
}
