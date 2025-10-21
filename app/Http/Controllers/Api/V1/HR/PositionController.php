<?php

namespace App\Http\Controllers\Api\V1\HR;

use App\Models\Position;
use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Position::query()
            ->with('roles')
            ->get();

        return PositionResource::collection($roles);
    }
}
