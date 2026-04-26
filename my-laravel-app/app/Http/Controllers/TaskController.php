<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->tasks()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => ['nullable', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority'    => ['nullable', Rule::in(['high', 'medium', 'low', 'none'])],
            'due_date'    => 'nullable|date',
            'quadrant'    => 'nullable|string|max:50',
            'column_id'      => ['nullable', Rule::exists('board_columns', 'id')->where('user_id', $request->user()->id)],
            'est_pomodoros'  => 'nullable|integer|min:1|max:99',
            'done_pomodoros' => 'nullable|integer|min:0|max:999',
        ]);

        $task = $request->user()->tasks()->create($data);
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status'      => ['nullable', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority'    => ['nullable', Rule::in(['high', 'medium', 'low', 'none'])],
            'due_date'    => 'nullable|date',
            'quadrant'    => 'nullable|string|max:50',
            'column_id'      => ['nullable', Rule::exists('board_columns', 'id')->where('user_id', $request->user()->id)],
            'est_pomodoros'  => 'nullable|integer|min:1|max:99',
            'done_pomodoros' => 'nullable|integer|min:0|max:999',
        ]);

        $task->update($data);
        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
