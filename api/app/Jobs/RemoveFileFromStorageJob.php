<?php

namespace App\Jobs;

use App\Services\AttachmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveFileFromStorageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000;
    public $tries = 3;

    protected $service;
    protected $filenames;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filenames = [])
    {
        $this->service = new AttachmentService;
        $this->filenames = $filenames;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Delete actual file from storage
        $this->service->removeFile($this->filenames);

    }
}
