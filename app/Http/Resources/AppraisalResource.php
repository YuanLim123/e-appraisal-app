<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Http\Resources\CurrentApproverResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppraisalResource extends JsonResource
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
            'appraiser' => new UserResource($this->whenLoaded('appraiser')),
            'appraisee' => new UserResource($this->whenLoaded('appraisee')),
            'approvers' => CurrentApproverResource::collection($this->whenLoaded('approvers')),
        ];
    }
}
