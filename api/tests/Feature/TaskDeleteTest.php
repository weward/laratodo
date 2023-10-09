<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     * When deleting a single task,
     *  I want to receive a 204 response
     */
    public function test_user_deletes_own_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        $res = $this->deleteJson(route('tasks.destroy', ['task' => $task->id]));

        $res->assertStatus(204);
    }

    /**
     * As an authenticated user,
     * When deleting a non-existing task,
     *  I want to receive a 404 response
     */
    public function test_user_deletes_non_existing_task_returns_404()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        $res = $this->deleteJson(route('tasks.destroy', ['task' => 9999]));

        $res->assertStatus(404);
    }

    /**
     * As an authenticated user,
     * When deleting a task I do not own,
     *  I want to receive a 401 response
     */
    public function test_user_deletes_task_of_another_user_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task of another user ($user at index = 1)
        $task = Task::factory()->user($user[1]->id)->create();

        $res = $this->deleteJson(route('tasks.destroy', ['task' => $task->id]));

        $res->assertStatus(401);
    }
}
