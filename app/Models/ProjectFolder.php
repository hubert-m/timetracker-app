<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFolder extends Model
{
    protected $fillable = ['name', 'color', 'user_id', 'position'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'folder_id');
    }
}
