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

class TaskFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * As an authenticated user,
     *  I want to filter my tasks.
     *  Task must be filtered by completed  date, priority,
     *  due date, archived_date and search query (title, description)
     *  Must be paginated.
     */
    public function test_user_filters_own_tasks()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Artisan::call('db:seed', ['--class' => PrioritiesSeeder::class]);

        $title = 'Apple';
        $completedAt = '2023-09-18';
        $priorityId = 1;
        $dueDate = '2023-09-15';

        // Task to search
        Task::factory()->user($user)->priority($priorityId)->create([
            'title' => $title,
            'completed_at' => $completedAt,
            'due_date' => $dueDate,
        ]);

        // create dummy tasks
        Task::factory()->user($user)->count(3)->create();

        $res = $this->getJson(route('tasks.index')
            . "?search={$title}&completed_at_to={$completedAt}&priority_id={$priorityId}&due_date_to={$dueDate}&sort_by=asc");

        $res->assertStatus(200);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
            ->where('meta.current_page', 1)
            ->where('data.0.title', $title)
            ->where('data.0.completed_at', $completedAt)
            ->where('data.0.priority_id', $priorityId)
            ->where('data.0.due_date', $dueDate)
                ->etc()
        );
    }
}
