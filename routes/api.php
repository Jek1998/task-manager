<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::patch('/tasks/{id}/toggle', [TaskController::class, 'toggle']);
    Route::delete('/tasks/completed', [TaskController::class, 'clearCompleted']);
    Route::patch('/tasks/{id}', [TaskController::class, 'update']);
});
