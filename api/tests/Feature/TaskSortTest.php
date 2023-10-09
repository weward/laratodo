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

class TaskSortTest extends TestCase
{
    use RefreshDatabase;



    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by title, description,
     *  due date, created date, completed date and priority
     *  Must be paginated.
     */
    public function test_user_sorts_own_tasks_by_description()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $first = 'Apple';
        $second = 'Banana';
        $sortBy = 'description';

        // Task to search
        Task::factory()->user($user)->create([
            'description' => $first
        ]);

        Task::factory()->user($user)->create([
            'description' => $second
        ]);

        $orderBy = 'asc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.description', $first)
                ->etc()
        );

        $orderBy = 'desc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.description', $second)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by title, description,
     *  due date, created date, completed date and priority
     *  Must be paginated.
     */
    public function test_user_sorts_own_tasks_by_due_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $first = '2023-09-15';
        $second = '2023-09-18';
        $sortBy = 'due_date';

        // Task to search
        Task::factory()->user($user)->create([
            'due_date' => $first
        ]);

        Task::factory()->user($user)->create([
            'due_date' => $second
        ]);

        $orderBy = 'asc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.due_date', $first)
                ->etc()
        );

        $orderBy = 'desc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.due_date', $second)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by title, description,
     *  due date, created date, completed date and priority
     *  Must be paginated.
     */
    public function test_user_sorts_own_tasks_by_created_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $first = '2023-09-15';
        $second = '2023-09-18';
        $sortBy = 'created_at';

        // Task to search
        Task::factory()->user($user)->create([
            'created_at' => $first
        ]);

        Task::factory()->user($user)->create([
            'created_at' => $second
        ]);

        $orderBy = 'asc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.created_at', $first)
                ->etc()
        );

        $orderBy = 'desc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.created_at', $second)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by title, description,
     *  due date, created date, completed date and priority
     *  Must be paginated.
     */
    public function test_user_sorts_own_tasks_by_completed_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $first = '2023-09-15';
        $second = '2023-09-18';
        $sortBy = 'completed_at';

        // Task to search
        Task::factory()->user($user)->create([
            'completed_at' => $first
        ]);

        Task::factory()->user($user)->create([
            'completed_at' => $second
        ]);

        $orderBy = 'asc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.completed_at', $first)
                ->etc()
        );

        $orderBy = 'desc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.completed_at', $second)
                ->etc()
        );
    }

    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by title, description,
     *  due date, created date, completed date and priority
     *  Must be paginated.
     */
    public function test_user_sorts_own_tasks_by_archived_date()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $first = '2023-09-15';
        $second = '2023-09-18';
        $sortBy = 'archived_at';

        // Task to search
        Task::factory()->user($user)->create([
            'archived_at' => $first
        ]);

        Task::factory()->user($user)->create([
            'archived_at' => $second
        ]);

        $orderBy = 'asc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.archived_at', $first)
                ->etc()
        );

        $orderBy = 'desc';
        $res = $this->getJson(route('tasks.index') . "?sort_by={$sortBy}&order_by={$orderBy}");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('meta.total', 2)
            ->where('data.0.archived_at', $second)
                ->etc()
        );
    }
}
