<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskArchivingToggleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     *  I want to mark a task as archived.
     *  I want to receive a 200 response with the updated task data.
     */
    public function test_user_marks_task_as_archived()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $now = now()->format('Y-m-d');

        $task = Task::factory()->user($user->id)->future()->create();

        $this->assertEquals($task->archived_at, null);

        $res = $this->postJson(route('tasks.toggle-archiving', ['task' => $task->id]));

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.archived_at', $now)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  When marking a non-existent task as archived,
     *  I want to receive a 404 response.
     */
    public function test_user_marks_a_non_existent_task_as_archived_returns_404()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->postJson(route('tasks.toggle-archiving', ['task' => 9999]));

        $res->assertStatus(404);
    }

    /**
     * As an authenticated user,
     *  When marking a task I do not own as archived,
     *  I want to receive a 401 response.
     */
    public function test_user_marks_task_of_another_user_as_archived_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task of another user
        $task = Task::factory()->user($user[1]->id)->future()->create();

        $this->assertEquals($task->archived_at, null);

        $res = $this->postJson(route('tasks.toggle-archiving', ['task' => $task->id]));

        $res->assertStatus(401);
    }

    /**
     * As an authenticated user,
     *  I want to restore an archived task
     *  I want to receive a 200 response with the updated task data.
     */
    public function test_user_restores_an_archived_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->archived()->create();

        $this->assertNotEquals($task->archived_at, null);

        $res = $this->postJson(route('tasks.toggle-archiving', ['task' => $task->id]));

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.archived_at', '')
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  When restoring an archived task I do not own,
     *  I want to receive a 401 response.
     */
    public function test_user_restores_an_archived_task_of_another_user_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task of another user
        $task = Task::factory()->user($user[1]->id)->archived()->create();

        $this->assertNotEquals($task->archived_at, null);

        $res = $this->postJson(route('tasks.toggle-archiving', ['task' => $task->id]));

        $res->assertStatus(401);
    }
}
