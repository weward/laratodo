<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskGuestUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As a guest user,
     * When creating a task, I want to receive a 401 response
     */
    public function test_guest_user_creates_task_returns_401()
    {
        $res = $this->postJson(route('tasks.store'), [
            'title' => 'Sample Title',
            'description' => 'This is a sample Description.',
        ]);

        $res->assertStatus(401);
    }

    /**
     * As a guest user,
     * When updating a task, I want to receive a 401 response
     */
    public function test_guest_user_updates_task_returns_401()
    {
        $user = User::factory()->create();
        $task = Task::factory()->user($user->id)->create();

        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => 'Edited Title',
            'description' => 'This is a sample Description but edited.',
        ]);

        $res->assertStatus(401);
    }

    /**
     * As a guest user,
     * When viewing a task, I want to receive a 401 response
     */
    public function test_guest_user_views_a_task_returns_401()
    {
        $user = User::factory()->create();
        $task = Task::factory()->user($user->id)->create();

        $res = $this->getJson(route('tasks.show', ['task' => $task->id]));

        $res->assertStatus(401);
    }

    /**
     * As a guest user,
     * When deleting a task, I want to receive a 401 response
     */
    public function test_guest_user_deletes_a_task_returns_401()
    {
        $user = User::factory()->create();
        $task = Task::factory()->user($user->id)->create();

        $res = $this->deleteJson(route('tasks.destroy', ['task' => $task->id]));

        $res->assertStatus(401);
    }
}
