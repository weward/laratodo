<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all or filtered list of Tasks
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('index', Task::class);

        $res = $this->service->filter($request);

        return response()->jsonApi(new TaskCollection($res), 200);
    }

    /**
     * Create a new Task
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request)
    {
        $this->authorize('store', Task::class);

        $res = $this->service->store($request);

        if (!$res) {
            return response()->jsonApi(Task::CREATE_FAILED, 500);
        }

        return response()->jsonApi(new TaskResource($res), 200);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorize('show', $task);

        return response()->jsonApi(new TaskResource($task), 200);
    }

    /**
     * Update an existing Task
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $res = $this->service->update($request, $task);

        if (!$res) {
            return response()->jsonApi(Task::UPDATE_FAILED, 500);
        }

        return response()->jsonApi(new TaskResource($res), 200);
    }

    /**
     * Delete an existing Task
     *
     * @param App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        $this->authorize('destroy', $task);

        $res = $this->service->destroy($task->id);

        if (!$res) {
            return response()->jsonApi(Task::DELETE_FAILED, 500);
        }

        return response()->jsonApi($res, 204);
    }

    public function toggleCompletion(Request $request, Task $task)
    {
        $this->authorize('toggleCompletion', $task);

        $res = $this->service->toggleCompletion($task);

        if (!$res) {
            return response()->jsonApi(Task::STATUS_TOGGLE_FAILED, 500);
        }

        return response()->jsonApi(new TaskResource($res), 200);
    }

    public function toggleArchiving(Request $request, Task $task)
    {
        $this->authorize('toggleCompletion', $task);

        $res = $this->service->toggleArchiving($task);

        if (!$res) {
            return response()->jsonApi(Task::STATUS_TOGGLE_FAILED, 500);
        }

        return response()->jsonApi(new TaskResource($res), 200);
    }


}
