<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\PrioritiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     *  When editing a task with valid inputs,
     *  I want to receive a 200 response with the updated task data
     */
    public function test_user_updates_own_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->future()->create();
        $oldTitle = $task->title;
        $title = 'Edited Title';

        $this->assertNotEquals($title, $oldTitle);

        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.title', $title)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When updating a task
     *  due date format must be YYYY-MM-DD
     *  must be today or future
     */
    public function test_user_updates_task_with_valid_due_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        $title = 'Edited Title';
        $now = now()->format('Y-m-d');

        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample description -- edited.',
            'due_date' => $now,
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.title', $title)
                ->where('data.due_date', $now)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When updating a task
     *  due date format must be YYYY-MM-DD
     *  must be today or future
     */
    public function test_user_updates_task_with_invalid_due_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        $title = 'Edited Title';
        $dueDate = now()->subDays(10)->format('Y-m-d');

        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample description -- edited.',
            'due_date' => $dueDate,
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['due_date']);
    }

    /**
     * As an authenticated user,
     * When updating a task
     *  Task priority must be either Urgent, High, Normal, or Low
     */
    public function test_user_updates_task_with_valid_priority()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $title = 'Sample Title';
        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'priority_id' => 1,
        ]);

        $res->assertStatus(200);
        $res->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.title', $title)
                ->where('data.priority_id', 1)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When updating a task
     *  Task priority must be either Urgent, High, Normal, or Low
     */
    public function test_user_updates_task_with_an_invalid_priority()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->create();

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $title = 'Sample Title';
        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'priority_id' => 999,
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['priority_id']);
    }

    /**
     * As an authenticated user,
     * When updating a task with valid tags
     *  I want to receive a 200 response with the updated task data
     */
    public function test_user_updates_task_with_valid_tags()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $task = Task::factory()->user($user->id)->tags(['tag-1', 'tag-2'])->create();

        $title = 'Sample Title';
        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'tags' => ['tag-2x', 'tag-3'], // removed, edited and added
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.title', $title)
                ->where('data.tags', ['tag-2x', 'tag-3'])
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When updating a non-existing task
     *  I want to receive a 404 response
     */
    public function test_user_updates_a_non_existing_task_returns_404()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';
        $res = $this->putJson(route('tasks.update', ['task' => 999]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
        ]);

        $res->assertStatus(404);
    }

    /**
     * As an authenticated user,
     * When updating a task I do not own,
     *  I want to receive a 401 response
     */
    public function test_user_updates_task_of_another_user_returns_401()
    {
        $user = User::factory()->count(2)->create();
        Sanctum::actingAs($user[0]);

        // Task is owned by another user (user at index = 1)
        $task = Task::factory()->user($user[1]->id)->create();

        $title = 'Sample Title';
        $res = $this->putJson(route('tasks.update', ['task' => $task->id]), [
            'title' => $title,
            'description' => 'This is a sample Description.',
        ]);

        $res->assertStatus(401);
    }

}
