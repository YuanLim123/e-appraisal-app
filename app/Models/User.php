<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'office_phone',
        'employee_no',
        'position_id',
        'role_id',
        'is_enabled',
        'is_appraiser',
        'join_at',
        'resign_at',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'join_at' => 'date',
            'resign_at' => 'date',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return ucfirst($this->first_name.' '.$this->last_name);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->BelongsToMany(Department::class, 'department_user');
    }

    public function appraisalAsAppraisee(): HasOne
    {
        return $this->hasOne(Appraisal::class, 'appraisee_id');
    }

    public function appraisalAsAppraiser(): HasMany
    {
        return $this->hasMany(Appraisal::class, 'appraiser_id');
    }

    public function appraisalRecordsAsAppraisee(): HasMany
    {
        return $this->hasMany(AppraisalRecord::class, 'appraisee_id');
    }

    public function appraisalRecordsAsAppraiser(): HasMany
    {
        return $this->hasMany(AppraisalRecord::class, 'appraiser_id');
    }

    public function recordApprovers(): HasMany
    {
        return $this->hasMany(RecordApprover::class, 'user_id');
    }
}
