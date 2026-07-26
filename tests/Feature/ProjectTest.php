<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\PendingInvitation;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_project_and_is_attached()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/projects', [
            'title' => 'Test Project',
            'description' => 'Test Description'
        ]);

        $response->assertStatus(201);
        
        $project = Project::first();
        $this->assertEquals('Test Project', $project->title);
        
        // Assert user is attached to project
        $this->assertTrue($project->users->contains($user));
    }

    public function test_user_can_view_only_their_projects()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $project1 = Project::create(['title' => 'Project 1']);
        $project1->users()->attach($user1->id);

        $project2 = Project::create(['title' => 'Project 2']);
        $project2->users()->attach($user2->id);

        $response = $this->actingAs($user1)->getJson('/projects');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Project 1']);
        $response->assertJsonMissing(['title' => 'Project 2']);
    }

    public function test_user_can_invite_existing_user_to_project()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(['email' => 'test@example.com']);

        $project = Project::create(['title' => 'Project 1']);
        $project->users()->attach($user1->id);

        $response = $this->actingAs($user1)->postJson('/invitations', [
            'email' => 'test@example.com',
            'resource_type' => 'Project',
            'resource_id' => $project->id,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($project->users->contains($user2));
    }

    public function test_user_can_invite_non_existing_user_creates_pending_invitation()
    {
        $user1 = User::factory()->create();
        
        $project = Project::create(['title' => 'Project 1']);
        $project->users()->attach($user1->id);

        $response = $this->actingAs($user1)->postJson('/invitations', [
            'email' => 'nonexisting@example.com',
            'resource_type' => 'Project',
            'resource_id' => $project->id,
        ]);

        $response->assertStatus(200);
        
        $pending = PendingInvitation::where('email', 'nonexisting@example.com')->first();
        $this->assertNotNull($pending);
        $this->assertEquals($project->id, $pending->invitable_id);
    }
}
