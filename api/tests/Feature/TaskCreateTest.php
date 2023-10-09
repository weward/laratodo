<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PrioritiesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskCreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     * When creating a task with a valid
     * title, description, due date, and priority,
     * I want to receive a 200 response with the task data.
     */
    public function test_user_creates_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        $res = $this->postJson(route('tasks.store'), [
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
     * When creating a task
     *  due date format must be YYYY-MM-DD
     *  must be today or future
     */
    public function test_user_creates_task_with_valid_due_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';
        $now = now()->format('Y-m-d');

        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
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
     * When creating a task
     *  due date format must be YYYY-MM-DD
     *  must be today or future
     */
    public function test_user_creates_task_with_invalid_due_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->postJson(route('tasks.store'), [
            'title' => 'Sample Title',
            'description' => 'This is a sample Description.',
            'due_date' => now()->subDays(10)->format('Y-m-d'),
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['due_date']);
    }

    /**
     * As an authenticated user,
     * When creating a task
     *  Task priority must be either Urgent, High, Normal, or Low.
     */
    public function test_user_creates_task_with_valid_priority()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $title = 'Sample Title';
        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'priority_id' => 1,
        ]);

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.title', $title)
                ->where('data.priority_id', 1)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     * When creating a task
     *  Task priority must be either Urgent, High, Normal, or Low.
     */
    public function test_user_creates_task_with_an_invalid_priority()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $title = 'Sample Title';
        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'priority_id' => 99,
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['priority_id']);
    }

    /**
     * As an authenticated user,
     * When creating a task with tags
     * I want to receive a 200 response with the task data.
     */
    public function test_user_creates_task_with_valid_tags()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'Sample Title';

        $res = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'description' => 'This is a sample Description.',
            'tags' => ['tag-1', 'tag-2'],
        ]);

        $res->assertStatus(200);
        $res->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
            ->where('data.title', $title)
            ->where('data.tags', ['tag-1', 'tag-2'])
            ->etc()
        );
    }
}
