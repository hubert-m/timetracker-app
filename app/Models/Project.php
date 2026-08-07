<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description'];

    /**
     * Pobierz folder przypisany do tego projektu dla danego użytkownika.
     */
    public function folderAssignmentForUser($userId)
    {
        return \DB::table('project_folder_assignments')
            ->where('project_id', $this->id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Relacja Eloquent do katalogów.
     */
    public function folders()
    {
        return $this->belongsToMany(ProjectFolder::class, 'project_folder_assignments', 'project_id', 'folder_id')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(
            'role',
            'can_add_members',
            'can_remove_members',
            'can_edit_project',
            'can_create_tasks',
            'can_edit_tasks',
            'can_add_task_members',
            'can_remove_task_members'
        );
    }

    /**
     * Sprawdza, czy użytkownik jest właścicielem projektu.
     */
    public function isOwner($userId)
    {
        $pivot = $this->users()->where('user_id', $userId)->first()?->pivot;
        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Pobiera uprawnienia użytkownika. Zwraca true dla wszystkich, jeśli jest właścicielem.
     */
    public function userPermissions($userId)
    {
        $user = $this->users()->where('user_id', $userId)->first();
        if (!$user) return null;
        
        $pivot = $user->pivot;
        $isOwner = $pivot->role === 'owner';

        return [
            'is_owner' => $isOwner,
            'can_add_members' => $isOwner || $pivot->can_add_members,
            'can_remove_members' => $isOwner || $pivot->can_remove_members,
            'can_edit_project' => $isOwner || $pivot->can_edit_project,
            'can_create_tasks' => $isOwner || $pivot->can_create_tasks,
            'can_edit_tasks' => $isOwner || $pivot->can_edit_tasks,
            'can_add_task_members' => $isOwner || $pivot->can_add_task_members,
            'can_remove_task_members' => $isOwner || $pivot->can_remove_task_members,
        ];
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

