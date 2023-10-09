<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected $attachmentService;

    public function __construct()
    {
        $this->attachmentService = new AttachmentService;
    }

    public function filter($request)
    {
        return Task::filter($request)->paginate(Task::DEFAULT_PAGINATION_ROWS);
    }

    /**
     * Create a new Task record
     *
     * @param Illuminate\Http\Request $request
     * @return object|boolean
     */
    public function store($request)
    {
        DB::beginTransaction();

        try {
            $task = new Task;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->tags = cleanTags($request->tags);
            $task->priority_id = $request->priority_id;
            $task->user_id = auth()->user()->id;
            $task->save();

            // Handle new attachments
            $this->attachmentService->saveFile($request, $task->id);

            DB::commit();

            $task->fresh();

            return $task;
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
        }

        return false;
    }

    /**
     * Update Task record
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Task $task
     * @return object|boolean
     */
    public function update($request, $task)
    {
        DB::beginTransaction();

        try {
            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->tags = cleanTags($request->tags);
            $task->priority_id = $request->priority_id;
            $task->user_id = auth()->user()->id;
            $task->save();

            // Existing attachments - process before new attachments
            $this->attachmentService->updateAttachments($request, $task);
            // Handle new attachments
            $this->attachmentService->saveFile($request, $task->id);

            DB::commit();

            $task->fresh();

            return $task;
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
        }

        return false;
    }

    /**
     * Update Task Entity
     *
     * @param int $id Task ID
     * @return boolean
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $task = Task::find($id);

            $this->attachmentService->updateAttachments(null, $task);

            $task->delete();

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
        }

        return false;
    }

    public function toggleCompletion($task)
    {
        $task->completed_at = $task->completed_at ? null : now();
        $task->save();

        return $task;
    }

    public function toggleArchiving($task)
    {
        $task->archived_at = $task->archived_at ? null : now();
        $task->save();

        return $task;
    }



}
