<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Models\Department;
use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::query()->get();

        return new DepartmentResource($departments);
    }
}
