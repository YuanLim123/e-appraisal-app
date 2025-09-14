<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'order',
        'name',
        'position_id',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
