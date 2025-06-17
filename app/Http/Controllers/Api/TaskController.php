<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TaskController extends Controller
{
    // Получить список задач текущего пользователя
    public function index()
    {
        return auth()->user()
            ->tasks()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Создать новую задачу
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task = auth()->user()->tasks()->create([
            'title' => $validated['title'],
            'completed' => false,
        ]);

        return response()->json($task, 201);
    }

    // Удалить задачу
    public function destroy($id)
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }

    // Переключить статус задачи (завершено/не завершено)
    public function toggle($id)
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        return response()->json($task);
    }

    // Очистить завершённые задачи
    public function clearCompleted()
    {
        auth()->user()->tasks()
            ->where('completed', true)
            ->delete();

        return response()->json(['status' => 'ok']);
    }

    // Обновить заголовок задачи
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task = auth()->user()->tasks()->findOrFail($id);
        $task->update(['title' => $validated['title']]);

        return response()->json($task);
    }
}
