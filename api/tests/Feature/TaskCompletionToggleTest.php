<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskCompletionToggleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     *  I want to mark a task as completed.
     *  I want to receive a 200 response with the updated task data.
     */
    public function test_user_marks_task_as_completed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $now = now()->format('Y-m-d');

        $task = Task::factory()->user($user->id)->future()->create();

        $this->assertEquals($task->completed_at, null);

        $res = $this->postJson(route('tasks.toggle-completion', ['task' => $task->id]));

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.completed_at', $now)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  When marking a non-existent task as completed,
     *  I want to receive a 404 response.
     */
    public function test_user_marks_a_non_existent_task_as_completed_returns_404()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->postJson(route('tasks.toggle-completion', ['task' => 9999]));

        $res->assertStatus(404);
    }

    /**
     * As an authenticated user,
     *  When marking a task I do not own as completed,
     *  I want to receive a 401 response.
     */
    public function test_user_marks_task_of_another_user_as_completed_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task of another user
        $task = Task::factory()->user($user[1]->id)->future()->create();

        $this->assertEquals($task->completed_at, null);

        $res = $this->postJson(route('tasks.toggle-completion', ['task' => $task->id]));

        $res->assertStatus(401);
    }

    /**
     * As an authenticated user,
     *  I want to mark a task as incomplete/todo.
     *  I want to receive a 200 response with the updated task data.
     */
    public function test_user_marks_completed_task_back_to_todo()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->completed()->create();

        $this->assertNotEquals($task->completed_at, null);

        $res = $this->postJson(route('tasks.toggle-completion', ['task' => $task->id]));

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.completed_at', '')
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  When marking a task I do not own as incomplete,
     *  I want to receive a 401 response.
     */
    public function test_user_marks_task_of_another_user_from_completed_task_back_to_todo_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task of another user
        $task = Task::factory()->user($user[1]->id)->completed()->create();

        $this->assertNotEquals($task->completed_at, null);

        $res = $this->postJson(route('tasks.toggle-completion', ['task' => $task->id]));

        $res->assertStatus(401);
    }
}
