<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     * When viewing a single task,
     * I want to receive a 200 response with the task data
     */
    public function test_user_views_own_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        $res = $this->getJson(route('tasks.show', ['task' => $task->id]));

        $res->assertStatus(200);

    }

    /**
     * As an authenticated user,
     * When viewing a non-existent task,
     *  I want to receive a 404 response.
     */
    public function test_user_views_non_existing_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->getJson(route('tasks.show', ['task' => 999999]));

        $res->assertStatus(404);
    }

    /**
     * As an authenticated user,
     * When viewing all tasks,
     *  I want to receive a 200 response with an array of my tasks
     */
    public function test_user_views_all_owned_tasks_paginated()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tasks = Task::factory()->user($user->id)->count(2)->create();

        $res = $this->getJson(route('tasks.index'));

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.0.title', $tasks[1]->title) // order by id desc
                ->where('meta.current_page', 1)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When viewing all tasks,
     *  I want to receive a 200 even when tasks is empty
     */
    public function test_user_views_all_owned_tasks_paginated_but_empty()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->getJson(route('tasks.index'));

        $res->assertStatus(200);
        $res->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data', [])
                ->where('meta.current_page', 1)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When viewing a task I did not create,
     *  I want to receive a 401 response
     */
    public function test_user_views_task_of_another_user_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // task of another user
        $task = Task::factory()->user($user[1]->id)->create();

        $res = $this->getJson(route('tasks.show', ['task' => $task->id]));

        $res->assertStatus(401);
    }
}
