<?php

namespace App\Models;

use App\Enums\AppraisalRecordGrade;
use App\Enums\AppraisalRecordStatus;
use App\Enums\AppraisalRecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'purpose',
        'grade',
        'total',
        'status',
        'role_id',
        'position_id',
        'current_step',
        'answer',
        'feedback',
        'grade_description',
        'review_from',
        'review_to',
        'appraiser_id',
        'appraisee_id',
        'current_approver_id',
        'season_id',
        'completed_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AppraisalRecordType::class,
            'grade' => AppraisalRecordGrade::class,
            'answer' => 'array',
            'feedback' => 'array',
            'status' => AppraisalRecordStatus::class,
            'review_from' => 'date:Y-m-d',
            'review_to' => 'date:Y-m-d',
            'completed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

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
        return $this->hasMany(RecordApprover::class, 'appraisal_record_id')
            ->orderBy('sequence');
    }
}
