<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppraisalRecord extends Model
{
    protected $fillable = [
        'admin_id',
        'appraisee_id',
        'current_approver_id',
        'season_id',
        'type',
        'purpose',
        'grade',
        'total',
        'position_period',
        'role_id',
        'position_id',
        'step',
        'department_ids',
        'comment',
        'feedback',
        'approver_ids',
        'description',
        'reason',
        'review_form',
        'review_to',
    ];
}
