<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::factory()->user(1)->future()->priority(1)->count(5)->create();
        Task::factory()->user(1)->archived(20)->priority(2)->count(2)->create();
        Task::factory()->user(1)->future()->count(2)->create();
        Task::factory()->user(1)->future(10)->priority(4)->count(80)->create();
        Task::factory()->user(1)->expired()->completed(1)->priority(2)->count(5)->create();
        Task::factory()->user(1)->future(10)->priority(3)->count(2)->create();
        Task::factory()->user(1)->archived(5)->priority(2)->count(2)->create();
        Task::factory()->user(1)->future(20)->priority(4)->count(100)->create();
        Task::factory()->user(1)->priority(3)->count(3)->create();
        Task::factory()->user(1)->expired(10)->completed(2)->priority(4)->count(2)->create();
        Task::factory()->user(1)->future(30)->priority(2)->count(60)->create();
        Task::factory()->user(1)->future(30)->priority(1)->count(60)->create();

        // low priority, no due date
        Task::factory()->user(1)->priority(1)->create();
        // completed status, 2 days ago, normal priority
        Task::factory()->user(1)->expired(2)->completed()->priority(2)->create();
        // archived status, 30 days ago
        Task::factory()->user(1)->expired(30)->archived(3)->create();
        // low priority, due 30 days from now
        Task::factory()->user(1)->future(30)->priority(1)->create();
        // Urgent priority, due 3 days from now
        Task::factory()->user(1)->future(3)->priority(4)->create();
    }
}
