<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::bind('task', function($id) {
    return Task::findOrFail($id);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('admin')->group(function() {
        Route::post('tasks/{task}/toggle-completion', [TaskController::class, 'toggleCompletion'])->name('tasks.toggle-completion');
        Route::post('tasks/{task}/toggle-archiving', [TaskController::class, 'toggleArchiving'])->name('tasks.toggle-archiving');
        Route::resource('tasks', TaskController::class)->except(['create', 'edit']);
    });

    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::prefix('auth')->group(function() {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
});
