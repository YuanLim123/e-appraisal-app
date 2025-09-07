<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalRecord extends Model
{
    protected $fillable = [
        'type',
        'purpose',
        'grade',
        'total',
        'position_period',
        'status',
        'role_id',
        'position_id',
        'current_step',
        'answer',
        'feedback',
        'description',
        'review_form',
        'review_to',
        'appraiser_id',
        'appraisee_id',
        'current_approver_id',
        'season_id',
        'completed_at',
        'rejected_at',
    ];

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appraiser_id');
    }

    public function appraisee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appraisee_id');
    }

    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function approvers(): HasMany
    {
        return $this->hasMany(RecordApprover::class, 'appraisal_record_id');
    }

}
