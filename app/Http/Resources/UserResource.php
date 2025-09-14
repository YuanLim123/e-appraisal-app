<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'employee_no' => $this->employee_no,
            'join_at' => $this->join_at->format('d-m-Y'),
            'role' => new RoleResource($this->whenLoaded('role')),
            'department' => DepartmentResource::collection($this->whenLoaded('departments')),
        ];
    }
}
