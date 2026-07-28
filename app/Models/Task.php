<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['project_id', 'title', 'description', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
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
