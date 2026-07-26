<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favoritable', 'favorites');
    }

    public function pendingInvitations()
    {
        return $this->morphMany(PendingInvitation::class, 'invitable');
    }
}
