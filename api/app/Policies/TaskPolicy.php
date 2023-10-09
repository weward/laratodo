<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(User $user)
    {
        return Auth::check() ? Response::allow() : abort(401, "Action is unauthorized.");
    }

    public function store(User $user)
    {
        return Auth::check() ? Response::allow() : abort(401, "Action is unauthorized.");
    }

    public function show(User $user, Task $task)
    {
        return $user->id == $task->user_id ?: abort(401, "Action is unauthorized.");
    }

    public function update(User $user, Task $task)
    {
        return $user->id == $task->user_id ?: abort(401, "Action is unauthorized.");
    }

    public function destroy(User $user, Task $task)
    {
        return $user->id == $task->user_id ?: abort(401, "Action is unauthorized.");
    }

    public function toggleCompletion(User $user, Task $task)
    {
        return $user->id == $task->user_id ?: abort(401, "Action is unauthorized.");
    }

    public function toggleArchiving(User $user, Task $task)
    {
        return $user->id == $task->user_id ?: abort(401, "Action is unauthorized.");
    }

}
