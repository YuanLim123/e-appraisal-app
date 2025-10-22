<?php

namespace App\Http\Resources;

use App\Enums\AppraisalRecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin \App\Models\AppraisalRecord
 * @property AppraisalRecordStatus $status
 * @property Carbon|null $review_from
 * @property Carbon|null $review_to
 * @property Carbon|null $employee_agreed_at
 * @property Carbon|null $supervisor_agreed_at
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
            'status' => $this->status->label(),
            'total' => $this->total,
            'grade' => $this->grade,
            'grade_description' => $this->grade_description,
            'review_from' => $this->review_from?->format('d-m-Y'),
            'review_to' => $this->review_to?->format('d-m-Y'),
            'answer' => $this->answer ?? [],
            'feedback' => $this->feedback ?? [],
            'appraiser' => new UserResource($this->whenLoaded('appraiser')),
            'appraisee' => new UserResource($this->whenLoaded('appraisee')),
            'current_approver' => $this->current_approver_id ? new UserResource($this->whenLoaded('currentApprover')) : null,
            'current_step' => $this->current_step ?? null,
            'completed_at' => $this->completed_at ?? null,
            'rejected_at' => $this->rejected_at ?? null,
            'employee_agreed_at' => $this->employee_agreed_at?->format('d-m-Y') ?? null,
            'supervisor_agreed_at' => $this->supervisor_agreed_at?->format('d-m-Y') ?? null,
            'approvers_comment' => RecordApproverResource::collection($this->whenLoaded('approvers')),
        ];
    }
}
