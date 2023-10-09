<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteArchivedTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000;
    public $tries = 3;

    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = new TaskService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Archived tasks should be deleted after a week automatically.
        $start = now()->startOfDay()->subDays(7);
        $end = now()->startOfDay()->subDays(7)->endOfDay();

        Task::orderBy('id')
        ->whereBetween('archived_at', [$start, $end])
        ->chunk(100, function($tasks) {
            foreach ($tasks as $task) {
                try {
                    $this->service->destroy($task->id);
                } catch (\Throwable $th) {
                    info($th->getMessage());
                    continue;
                }
            }
        });
    }
}
