<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Http\Requests\RegisteredUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisteredUserController extends Controller
{
    public function __invoke(RegisteredUserRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'office_phone' => $request->office_phone,
            'employee_no' => $request->employee_no,
            'password' => Hash::make($request->password),
            'join_at' => $request->join_at,
            'resign_at' => $request->resign_at,
            'is_appraiser' => $request->is_appraiser,
            'is_enabled' => $request->is_enabled,
        ]);

        event(new Registered($user));

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json([
            'access_token' => $user->createToken($device)->plainTextToken,
        ], Response::HTTP_CREATED);
    }
}
