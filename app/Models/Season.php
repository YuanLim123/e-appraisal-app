<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_at',
        'end_at',
    ];

    public function appraisalRecords(): HasMany
    {
        return $this->hasMany(AppraisalRecord::class);
    }
}
