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
        Schema::create('project_folder_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->constrained('project_folders')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'project_id']);
        });

        // Migruj istniejące dane z projects.folder_id do nowej tabeli
        $projects = \DB::table('projects')->whereNotNull('folder_id')->get();
        foreach ($projects as $project) {
            $folder = \DB::table('project_folders')->where('id', $project->folder_id)->first();
            if ($folder) {
                \DB::table('project_folder_assignments')->insert([
                    'user_id' => $folder->user_id,
                    'project_id' => $project->id,
                    'folder_id' => $project->folder_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Usuń kolumnę folder_id z projects
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_folder_assignments');
    }
};
