<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordApproverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sequence' => $this->sequence,
            'approver_name' => $this->whenLoaded('user')->full_name,
            'approvered_at' => $this->approved_at?->format('d-m-Y') ?? null,
            'rejected_at' => $this->rejected_at?->format('d-m-Y') ?? null,
            'comment' => $this->comment,
        ];
    }
}
