<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use Carbon\Carbon;

class TimeLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_timer_if_assigned_to_task()
    {
        $user = User::factory()->create();
        $project = Project::create(['title' => 'P1']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'T1']);
        $task->users()->attach($user->id);

        $response = $this->actingAs($user)->postJson('/time-logs/start', [
            'task_id' => $task->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('time_logs', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'duration_minutes' => 0
        ]);
    }

    public function test_user_cannot_start_two_timers()
    {
        $user = User::factory()->create();
        $project = Project::create(['title' => 'P1']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'T1']);
        $task->users()->attach($user->id);

        $this->actingAs($user)->postJson('/time-logs/start', ['task_id' => $task->id])->assertStatus(201);
        
        $response = $this->actingAs($user)->postJson('/time-logs/start', ['task_id' => $task->id]);
        $response->assertStatus(422)->assertJson(['error' => 'Masz już uruchomiony stoper.']);
    }

    public function test_user_can_stop_timer()
    {
        $user = User::factory()->create();
        $project = Project::create(['title' => 'P1']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'T1']);
        $task->users()->attach($user->id);

        Carbon::setTestNow(Carbon::parse('2023-01-01 10:00:00'));
        $startResponse = $this->actingAs($user)->postJson('/time-logs/start', ['task_id' => $task->id]);
        
        $logId = $startResponse->json('time_log_id');

        Carbon::setTestNow(Carbon::parse('2023-01-01 10:30:00'));
        $stopResponse = $this->actingAs($user)->postJson("/time-logs/{$logId}/stop");

        $stopResponse->assertStatus(200);
        $this->assertDatabaseHas('time_logs', [
            'id' => $logId,
            'duration_minutes' => 30
        ]);
    }

    public function test_manual_time_logging()
    {
        $user = User::factory()->create();
        $project = Project::create(['title' => 'P1']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'T1']);
        $task->users()->attach($user->id);

        $response = $this->actingAs($user)->postJson('/time-logs', [
            'task_id' => $task->id,
            'date' => '2023-01-01',
            'duration_minutes' => 120
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('time_logs', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'duration_minutes' => 120
        ]);
    }

    public function test_user_cannot_log_time_for_unassigned_task()
    {
        $user = User::factory()->create();
        $project = Project::create(['title' => 'P1']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'T1']);

        $response = $this->actingAs($user)->postJson('/time-logs', [
            'task_id' => $task->id,
            'date' => '2023-01-01',
            'duration_minutes' => 120
        ]);

        $response->assertStatus(403);
    }
}
