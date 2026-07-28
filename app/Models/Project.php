<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'folder_id'];

    public function folder()
    {
        return $this->belongsTo(ProjectFolder::class, 'folder_id');
    }

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
