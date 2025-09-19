<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AppraisalRecord
 */
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
            'grade_description' => $this->grade_description,
            'review_from' => $this->review_from->format('d-m-Y'),
            'review_to' => $this->review_to->format('d-m-Y'),
            'answer' => $this->answer ?? [],
            'feedback' => $this->feedback ?? [],
            'appraiser' => new UserResource($this->whenLoaded('appraiser')),
            'appraisee' => new UserResource($this->whenLoaded('appraisee')),
            'current_approver' => $this->current_approver_id ? new UserResource($this->whenLoaded('currentApprover')) : null,
            'current_step' => $this->current_step ?? null,
            'completed_at' => $this->completed_at ?? null,
            'rejected_at' => $this->rejected_at ?? null,
        ];
    }
}
