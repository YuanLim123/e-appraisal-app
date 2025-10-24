<?php

namespace App\Services\V1\HR;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function store(array $attributes): User
    {
        $attributes['password'] = Hash::make($attributes['password']);
        //$attributes['username'] = 'Asj#'.$attributes['employee_no'];

        $user = User::create($attributes);

        if (! empty($attributes['department_ids'])) {
            foreach ($attributes['department_ids'] as $departmentId) {
                $user->departments()->attach($departmentId);
            }
        }

        return $user;
    }
}
