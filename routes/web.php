<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Главная страница с задачами — только для авторизованных пользователей
Route::middleware(['auth'])->get('/', fn () => view('tasks'))->name('tasks');

// Альтернативный путь к задачам
Route::middleware(['auth'])->get('/tasks', fn () => view('tasks'))->name('tasks');

// Панель управления
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Профиль пользователя
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Роуты аутентификации Breeze
require __DIR__.'/auth.php';
