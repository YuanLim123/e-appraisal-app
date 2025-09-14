<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appraisal extends Model
{
    protected $fillable = [
        'appraisee_id',
        'appraiser_id',
    ];

    public function appraisee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appraisee_id');
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appraiser_id');
    }

    public function approvers(): HasMany
    {
        return $this->hasMany(CurrentApprover::class, 'appraisal_id')
            ->orderBy('sequence');
    }
}
