<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectFolder extends Model
{
    protected $fillable = ['name', 'color', 'user_id', 'position'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pobierz projekty przypisane do tego folderu (przez tabelę piwotową).
     */
    public function assignedProjects()
    {
        return Project::whereIn('id', function ($query) {
            $query->select('project_id')
                ->from('project_folder_assignments')
                ->where('folder_id', $this->id);
        });
    }
}
