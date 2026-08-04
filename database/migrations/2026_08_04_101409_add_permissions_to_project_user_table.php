<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->string('role', 20)->default('member')->after('user_id');
            $table->boolean('can_add_members')->default(false)->after('role');
            $table->boolean('can_remove_members')->default(false)->after('can_add_members');
            $table->boolean('can_edit_project')->default(false)->after('can_remove_members');
            $table->boolean('can_create_tasks')->default(false)->after('can_edit_project');
            $table->boolean('can_edit_tasks')->default(false)->after('can_create_tasks');
            $table->boolean('can_add_task_members')->default(false)->after('can_edit_tasks');
            $table->boolean('can_remove_task_members')->default(false)->after('can_add_task_members');
        });

        // Ustaw pierwszego członka każdego projektu (twórcę) jako ownera z pełnymi uprawnieniami
        $projects = \DB::table('projects')->get();
        foreach ($projects as $project) {
            $firstMember = \DB::table('project_user')
                ->where('project_id', $project->id)
                ->orderBy('id')
                ->first();
            if ($firstMember) {
                \DB::table('project_user')
                    ->where('id', $firstMember->id)
                    ->update([
                        'role' => 'owner',
                        'can_add_members' => true,
                        'can_remove_members' => true,
                        'can_edit_project' => true,
                        'can_create_tasks' => true,
                        'can_edit_tasks' => true,
                        'can_add_task_members' => true,
                        'can_remove_task_members' => true,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'can_add_members', 'can_remove_members', 'can_edit_project',
                'can_create_tasks', 'can_edit_tasks', 'can_add_task_members', 'can_remove_task_members'
            ]);
        });
    }
};
