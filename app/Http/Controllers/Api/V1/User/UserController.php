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
            ->when($request->employee_no, function ($query) use ($request) {
                $query->where('employee_no', 'like', '%' . $request->employee_no . '%');
            })
            ->when($request->name, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $trimmedName = preg_replace('/\s+/', '', $request->name);
                    $q->where('first_name', 'like', '%' . $request->name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->name . '%')
                        ->orWhereRaw("first_name || last_name LIKE ?", ['%' . $trimmedName . '%']);
                });
            })
            ->when($request->department_id, function ($query) use ($request) {
                $query->whereHas('departments', function ($q) use ($request) {
                    $q->where('departments.id', $request->department_id);
                });
            })
            ->when($request->position_id, function ($query) use ($request) {
                $query->where('position_id',  $request->position_id);
            })
            ->when($request->role_id, function ($query) use ($request) {
                $query->where('role_id', $request->role_id);
            })
            ->when($request->join_after, function ($query) use ($request) {
                $query->where('join_at', '>', $request->join_after);
            })
            ->when($request->join_before, function ($query) use ($request) {
                $query->where('join_at', '<', $request->join_before);
            })
            ->when($request->sortBy, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder ?? 'asc');
            })
            ->orderBy('created_at', 'asc')
            ->whereNull('resign_at')
            ->paginate(10)
            ->withQueryString();

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        $user->load(['departments', 'role', 'role.position', 'appraisalRecordsAsAppraisee', 'appraisalRecordsAsAppraisee.appraiser', 'appraisalRecordsAsAppraisee.currentApprover']);
        return new UserResource($user);
    }
}
