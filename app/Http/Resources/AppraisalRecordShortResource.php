<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppraisalRecordShortResource extends JsonResource
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
            'status' => $this->status->label(),
            'type' => $this->type->label(),
            'purpose' => $this->purpose->label(),
            'appraiser' => $this->appraiser?->full_name,
            'current_approver' => $this->current_approver?->full_name,
        ];
    }
}
