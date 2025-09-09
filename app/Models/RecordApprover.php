<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordApprover extends Model
{
    protected $fillable = [
        'sequence',
        'comment',
        'approved_at',
        'rejected_at',
        'user_id',
        'appraisal_record_id',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'date',
            'rejected_at' => 'date',
        ];
    }

    

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appraisalRecord(): BelongsTo
    {
        return $this->belongsTo(AppraisalRecord::class);
    }

}
