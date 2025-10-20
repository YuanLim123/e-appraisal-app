<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()
            ->with('position')
            ->get();

        return RoleResource::collection($roles);
    }
}
