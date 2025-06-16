<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Список  задач
    public function index()
    {
        return auth()->user()->tasks()->orderBy('created_at', 'desc')->get();
    }

    // Создать задачу
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
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }

    // Задача выполнена
        public function toggle($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        return response()->json($task);
    }
     
    // Очистка завершённых
        public function clearCompleted()
    {
        Task::where('completed', true)->delete();
        return response()->json(['status' => 'ok']);
    }


    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'title' => $request->input('title'),
        ]);

        return response()->json($task);
    }


}
