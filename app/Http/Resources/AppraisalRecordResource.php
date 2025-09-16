<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppraisalRecordResource extends JsonResource
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
            'status' => $this->status,
            'total' => $this->total,
            'grade' => $this->grade,
            'description' => $this->description,
            'review_from' => $this->review_from,
            'review_to' => $this->review_to,
            'position_period' => $this->position_period,
            'answer' => $this->answer,
            'feedback' => $this->feedback,
            'appraiser' => new UserResource($this->whenLoaded('appraiser')),
            'appraisee' => new UserResource($this->whenLoaded('appraisee')),
            'current_approver' => new UserResource($this->whenLoaded('currentApprover')),
            'current_step' => $this->current_step,
            'completed_at' => $this->completed_at,
            'rejected_at' => $this->rejected_at,
        ];
    }
}
