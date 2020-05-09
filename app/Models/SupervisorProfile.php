<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorProfile extends Model
{
    public function supervisor()
    {
        return $this->belongsTo(User::class);
    }

    public function scholars()
    {
        return $this->hasMany(Scholar::class, 'supervisor_profile_id');
    }
}
